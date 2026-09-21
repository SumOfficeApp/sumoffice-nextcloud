<?php
declare(strict_types=1);
namespace OCA\SumOffice\Service;

use OCP\App\IAppManager;
use OCP\Http\Client\IClientService;
use OCP\IConfig;
use Psr\Log\LoggerInterface;

/**
 * The whole app: point Nextcloud Office (richdocuments) at a SumOffice server
 * and tell the admin what the server offers. Nothing is cached here; the
 * truth is always fetched from the server the admin typed.
 */
class Connector {
	private const RD = 'richdocuments';

	public function __construct(
		private IConfig $config,
		private IAppManager $appManager,
		private IClientService $clientService,
		private LoggerInterface $logger,
	) {
	}

	public function status(): array {
		$installed = $this->appManager->isEnabledForUser(self::RD);
		$url = $this->config->getAppValue(self::RD, 'wopi_url', '');
		$out = [
			'officeInstalled' => $installed,
			'url' => $url,
			'connected' => false,
			'server' => null,
			'formats' => [],
			'errors' => [],
		];
		if ($url === '') {
			return $out;
		}
		return array_merge($out, $this->probe($url));
	}

	public function connect(string $url): array {
		$url = rtrim(trim($url), '/');
		$errors = [];
		if (!$this->appManager->isEnabledForUser(self::RD)) {
			$errors[] = 'Nextcloud Office (richdocuments) is not installed. Install it from the app store first — it is the WOPI host this app configures.';
		}
		if (!preg_match('#^https?://[^/\s]+#', $url)) {
			$errors[] = 'Enter the public address of your SumOffice stack, for example https://office.example.com';
		}
		if ($errors !== []) {
			return ['ok' => false, 'errors' => $errors, 'url' => $url];
		}
		$probe = $this->probe($url);
		if (!$probe['connected']) {
			return ['ok' => false, 'errors' => $probe['errors'], 'url' => $url, 'formats' => $probe['formats'], 'server' => $probe['server']];
		}
		// Same three settings as `occ richdocuments:activate-config`; the public URL is the
		// origin Nextcloud's CSP will allow for the editor frame.
		$this->config->setAppValue(self::RD, 'wopi_url', $url);
		$this->config->setAppValue(self::RD, 'public_wopi_url', $this->origin($url));
		$this->config->setAppValue(self::RD, 'wopi_callback_url', '');
		$this->resetOfficeCaches();
		return array_merge(['ok' => true, 'url' => $url], $probe);
	}

	/** Fetch discovery + capabilities and say what is there. */
	private function probe(string $url): array {
		$client = $this->clientService->newClient();
		$errors = [];
		$formats = [];
		$server = null;
		try {
			$xml = $client->get($url . '/hosting/discovery', ['timeout' => 10])->getBody();
			$doc = @simplexml_load_string((string)$xml);
			if ($doc === false) {
				$errors[] = 'The discovery endpoint did not return XML.';
			} else {
				foreach ($doc->xpath('//app') as $app) {
					$name = (string)$app['name'];
					foreach ($app->action as $action) {
						$ext = (string)$action['ext'];
						if ($ext !== '') {
							$formats[$ext] = true;
						}
					}
					if (str_contains($name, 'wordprocessingml')) { $formats['docx'] = true; }
					if (str_contains($name, 'spreadsheetml')) { $formats['xlsx'] = true; }
					if (str_contains($name, 'macroEnabled.12') && str_contains($name, 'sheet')) { $formats['xlsm'] = true; }
					if (str_contains($name, 'binary.macroEnabled')) { $formats['xlsb'] = true; }
				}
				if (!isset($formats['docx']) || !isset($formats['xlsx'])) {
					$errors[] = 'Discovery does not offer both DOCX and XLSX; Nextcloud Office needs the document editor as well. Run SumDoc and SumSheet behind one address — see https://sumoffice.com/nextcloud';
				}
				if ($doc->xpath('//app[@name="Capabilities"]') === []) {
					$errors[] = 'Discovery has no Capabilities entry; Nextcloud Office refuses such a server.';
				}
			}
		} catch (\Throwable $e) {
			$errors[] = 'Could not fetch ' . $url . '/hosting/discovery: ' . $e->getMessage();
		}
		try {
			$caps = json_decode((string)$client->get($url . '/hosting/capabilities', ['timeout' => 10])->getBody(), true);
			if (is_array($caps) && isset($caps['productName'])) {
				$server = trim(($caps['productName'] ?? '') . ' ' . ($caps['productVersion'] ?? ''));
			} else {
				$errors[] = 'The capabilities endpoint did not return productName.';
			}
		} catch (\Throwable $e) {
			$errors[] = 'Could not fetch ' . $url . '/hosting/capabilities: ' . $e->getMessage();
		}
		ksort($formats);
		return [
			'connected' => $errors === [],
			'server' => $server,
			'formats' => array_keys($formats),
			'errors' => $errors,
		];
	}

	private function origin(string $url): string {
		$p = parse_url($url);
		$port = isset($p['port']) ? ':' . $p['port'] : '';
		return ($p['scheme'] ?? 'https') . '://' . ($p['host'] ?? '') . $port;
	}

	/** richdocuments caches discovery in appdata; a stale copy keeps pointing at the old server. */
	private function resetOfficeCaches(): void {
		try {
			if (class_exists(\OCA\Richdocuments\Service\DiscoveryService::class)) {
				\OC::$server->get(\OCA\Richdocuments\Service\DiscoveryService::class)->resetCache();
			}
			if (class_exists(\OCA\Richdocuments\Service\CapabilitiesService::class)) {
				\OC::$server->get(\OCA\Richdocuments\Service\CapabilitiesService::class)->resetCache();
			}
		} catch (\Throwable $e) {
			$this->logger->warning('sumoffice: could not reset Nextcloud Office caches: ' . $e->getMessage());
		}
	}
}

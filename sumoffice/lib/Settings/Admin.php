<?php
declare(strict_types=1);
namespace OCA\SumOffice\Settings;

use OCA\SumOffice\Service\Connector;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\Settings\ISettings;
use OCP\Util;

class Admin implements ISettings {
	public function __construct(private Connector $connector, private IInitialState $initialState) {
	}

	public function getForm(): TemplateResponse {
		$this->initialState->provideInitialState('status', $this->connector->status());
		Util::addScript('sumoffice', 'admin');
		Util::addStyle('sumoffice', 'admin');
		return new TemplateResponse('sumoffice', 'admin', [], '');
	}

	public function getSection(): string { return 'sumoffice'; }
	public function getPriority(): int { return 10; }
}

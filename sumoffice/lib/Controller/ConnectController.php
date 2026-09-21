<?php
declare(strict_types=1);
namespace OCA\SumOffice\Controller;

use OCA\SumOffice\Service\Connector;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\AuthorizedAdminSetting;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ConnectController extends Controller {
	public function __construct(string $appName, IRequest $request, private Connector $connector) {
		parent::__construct($appName, $request);
	}

	#[AuthorizedAdminSetting(settings: \OCA\SumOffice\Settings\Admin::class)]
	public function status(): JSONResponse {
		return new JSONResponse($this->connector->status());
	}

	#[AuthorizedAdminSetting(settings: \OCA\SumOffice\Settings\Admin::class)]
	public function connect(string $url): JSONResponse {
		return new JSONResponse($this->connector->connect($url));
	}
}

<?php
declare(strict_types=1);
return [
	'routes' => [
		['name' => 'connect#status', 'url' => '/status', 'verb' => 'GET'],
		['name' => 'connect#connect', 'url' => '/connect', 'verb' => 'POST'],
	],
];

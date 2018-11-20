<?php
// Verify the PHP version, gives tutorial if fail.
if (version_compare(phpversion(), '5.3.3', '<')) die('PHP 5.3.3 or higher is required');
if (!isset($filepath)) $filepath = '../';

$version = '1.5_SVN';
session_start();
ob_start();
require_once $filepath.'config.php';
require_once $filepath.'engine/database/connect.php';
require_once $filepath.'engine/function/general.php';
require_once $filepath.'engine/function/cache.php';

// Initiate default config if nothing is specified (outdated config file)
if (!isset($config['api']['debug'])) $config['api']['debug'] = false;

$response = array(
	'version' => array(
		'znote' => $version,
		'ot' => $config['ServerEngine']
	),
);

if (isset($moduleVersion)) $response['version']['module'] = $moduleVersion;

function UseClass($name = false, $module = false, $path = false) {
	if ($name !== false) {
		if (!is_array($name)) {
			if (!$module) $module = $name;
			if (!$path) require_once "modules/base/{$module}/class/{$name}.php";
			else require_once "{$path}/{$name}.php";
		} else {
			foreach ($name as $class) {
				if (!$module) $module = $class;
				if (!$path) require_once "modules/base/{$module}/class/{$class}.php";
				else require_once "{$path}/{$class}.php";
			}
		}
	} else die('Error in function UseClass: class parameter is false.');
}

function SendResponse($response) {
	global $config;
	if ($config['api']['debug'] || isset($_GET['debug'])) data_dump($response, false, "Response (debug mode)");
	else echo json_encode($response);
}
?>
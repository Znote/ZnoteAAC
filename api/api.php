<?php
// Verify the PHP version, gives tutorial if fail.
if (version_compare(phpversion(), '5.3.3', '<')) die('PHP 5.3.3 or higher is required');
if (!isset($filepath)) $filepath = '../';

$version = '1.5_SVN';
session_start();
ob_start();
require $filepath.'config.php';
require $filepath.'engine/database/connect.php';
require $filepath.'engine/function/general.php';
require $filepath.'engine/function/cache.php';

// Initiate default config if nothing is specified (outdated config file)
if (!isset($config['api']['debug'])) $config['api']['debug'] = false;

$response = array(
	'version' => array(
		'znote' => $version,
		'ot' => $config['TFSVersion']
	),
);

if (isset($moduleVersion)) $response['version']['module'] = $moduleVersion;
function SendResponse($response) {
	global $config;
	if ($config['api']['debug']) data_dump($response, false, "Response (debug mode)");
	else echo json_encode($response);
}
?>
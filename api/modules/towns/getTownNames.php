<?php require_once '../../module.php';

// Configure module version number
$response['version']['module'] = 1;

// Fetch towns
$response['data']['towns'] = $config['towns'];

// Fetch towns available under character creation
foreach ($config['available_towns'] as $id) {
	$response['data']['available'][$id] = $response['data']['towns'][$id];
}

SendResponse($response);
?>
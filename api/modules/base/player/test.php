<?php $filepath = '../../../../'; require_once '../../../module.php';

// Configure module version number
$response['version']['module'] = 1;

UseClass('player');
$player = new Player(1129);
$response['player'] = $player->fetch('name');
$response['test'] = $player->fetch('level');


SendResponse($response);
?>
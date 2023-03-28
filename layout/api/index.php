<?php $filepath = '../'; require_once 'module.php';

// Autofetch API modules
$directory = 'modules';
$plugins = array();

// Load base
$plugins['base'] = array(
	'player' => 'test.php'
);

$iterator = new DirectoryIterator($directory);
foreach($iterator as $entity) {
	if($entity->isDot())
		continue;
	$iterator = new DirectoryIterator($entity->getPathname());
	foreach($iterator as $entity) {
		if($entity->isFile()) {
			$file_extension = pathinfo($entity->getFilename(), PATHINFO_EXTENSION);
			if ($file_extension == 'php') {
				$path = explode('/', $entity->getPathname());
				if (count($path) === 1) $path = explode('\\', $entity->getPathname());
				$plugins[$path[1]] = $path[2];
			}
		}
	}
}

$response['modules'] = $plugins;
$response['data']['title'] = $config['site_title'];
$response['data']['slogan'] = $config['site_title_context'];
$response['data']['time'] = getClock(time(), false, true);
$response['data']['time_formatted'] = getClock(time(), true, true);

// Account count
$accounts = mysql_select_single("SELECT COUNT('id') AS `count` FROM `accounts`;");
$response['data']['accounts'] = ($accounts !== false) ? (int)$accounts['count'] : 0;
// Player count
$players = mysql_select_single("SELECT COUNT('id') AS `count` FROM `players`;");
$response['data']['players'] = ($players !== false) ? (int)$players['count'] : 0;
// online player count
if ($config['ServerEngine'] != 'TFS_10') {
	$online = mysql_select_single("SELECT COUNT('id') AS `count`, COUNT(DISTINCT `lastip`) AS `unique` FROM `players` WHERE `online`='1';");
} else {
	$online = mysql_select_single("SELECT COUNT(`o`.`player_id`) AS `count`, COUNT(DISTINCT `p`.`lastip`) AS `unique` FROM `players_online` AS `o` INNER JOIN `players` AS `p` ON `o`.`player_id` = `p`.`id`;");
}
$response['data']['online'] = ($online !== false) ? (int)$online['count'] : 0;
$response['data']['online_unique_ip'] = ($online !== false) ? (int)$online['unique'] : 0;
$response['data']['client'] = $config['client'];
$response['data']['port'] = $config['port'];
$response['data']['guildwar'] = $config['guildwar_enabled'];
$response['data']['forum'] = $config['forum']['enabled'];

SendResponse($response);
?>
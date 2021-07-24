<?php
$cache = new Cache('engine/cache/asideServerInfo');
if ($cache->hasExpired()) {
	$asideServerInfo = mysql_select_single("
		SELECT 
			(SELECT COUNT(`id`) FROM `accounts`) as `accounts`,
			(SELECT COUNT(`id`) FROM `players`) as `players`,
			(SELECT COUNT(`player_id`) FROM `players_online`) as `online`
	");
	$cache->setContent($asideServerInfo);
	$cache->save();
} else {
	$asideServerInfo = $cache->load();
}
?>
<div class="well widget">
	<div class="header">
		Server Information
	</div>
	<div class="body">
		<ul>
			<li><a href="onlinelist.php">Players online: <?php echo $asideServerInfo['online']; ?></a></li>
			<li>Registered accounts: <?php echo $asideServerInfo['accounts'];?></li>
			<li>Registered players: <?php echo $asideServerInfo['players'];?></li>
		</ul>
	</div>
</div>

<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; 

$history = array(
	"enabled" => true,
	"days" => 14,
	"cache" => 300
);

// Returns a list of players online
$array = false;
$loadFlags = ($config['country_flags']['enabled'] && $config['country_flags']['onlinelist']) ? true : false;
$loadOutfits = ($config['show_outfits']['onlinelist']) ? true : false;
if ($config['client'] < 780) {
	$outfitQuery = ($loadOutfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`" : "";
} else {
	$outfitQuery = ($loadOutfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`, `p`.`lookaddons` AS `addons`" : "";
}

// Small 30 seconds players_online cache.
$cache = new Cache('engine/cache/onlinelist');
$cache->setExpiration(30);
if ($cache->hasExpired()) {
	// Load online list data from SQL
	$array = ($loadFlags === true) ? mysql_select_multi("SELECT `p`.`name` AS `name`, `p`.`level` AS `level`, `p`.`vocation` AS `vocation`, `g`.`name` AS `gname`, `za`.`flag` AS `flag` $outfitQuery FROM `players_online` AS `o` INNER JOIN `players` AS `p` ON `o`.`player_id` = `p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id` = `za`.`account_id` LEFT JOIN `guild_membership` AS `gm` ON `o`.`player_id` = `gm`.`player_id` LEFT JOIN `guilds` AS `g` ON `gm`.`guild_id` = `g`.`id`;") : mysql_select_multi("SELECT `p`.`name` AS `name`, `p`.`level` AS `level`, `p`.`vocation` AS `vocation`, `g`.`name` AS `gname` $outfitQuery FROM `players_online` AS `o` INNER JOIN `players` AS `p` ON `o`.`player_id` = `p`.`id` LEFT JOIN `guild_membership` AS `gm` ON `o`.`player_id` = `gm`.`player_id` LEFT JOIN `guilds` AS `g` ON `gm`.`guild_id` = `g`.`id`;");
	// End loading data from SQL
	$cache->setContent($array);
	$cache->save();
} else {
	$array = $cache->load();
}
// End cache

// 5 minute logout history cache
if ($history["enabled"]) {
	$time = time();
	$cache = new Cache('engine/cache/onlinelist_rec');
	$cache->setExpiration($history['cache']);
	if ($cache->hasExpired()) {
		// Load online list data from SQL
		$recents = ($loadFlags === true) ? mysql_select_multi("
			SELECT 
				`p`.`name` AS `name`, 
				`p`.`level` AS `level`, 
				`p`.`vocation` AS `vocation`, 
				`p`.`lastlogout`,
				`g`.`name` AS `gname`, 
				`za`.`flag` AS `flag` 
				$outfitQuery 
			FROM `players` AS `p` 
			INNER JOIN `znote_accounts` AS `za` 
				ON `p`.`account_id` = `za`.`account_id` 
			LEFT JOIN `guild_membership` AS `gm` 
				ON `p`.`id` = `gm`.`player_id` 
			LEFT JOIN `guilds` AS `g` 
				ON `gm`.`guild_id` = `g`.`id`
			WHERE `p`.`lastlogout` >= $time - ({$history['days']} * 24 * 60 * 60)
			ORDER BY `p`.`lastlogout` DESC;
		") : mysql_select_multi("
			SELECT 
				`p`.`name` AS `name`, 
				`p`.`level` AS `level`, 
				`p`.`vocation` AS `vocation`, 
				`p`.`lastlogout`,
				`g`.`name` AS `gname` 
				$outfitQuery 
			FROM `players` AS `p` 
			LEFT JOIN `guild_membership` AS `gm` 
				ON `p`.`id` = `gm`.`player_id` 
			LEFT JOIN `guilds` AS `g` 
				ON `gm`.`guild_id` = `g`.`id`
			WHERE `p`.`lastlogout` >= $time - ({$history['days']} * 24 * 60 * 60)
			ORDER BY `p`.`lastlogout` DESC;
		");
		// End loading data from SQL
		$cache->setContent($recents);
		$cache->save();
	} else {
		$recents = $cache->load();
	}
}
// End cache

?>
<h1>Who is online?</h1>
<?php

// Players currently logged in
if (!empty($array) && $array !== false): ?>
	<h2>Currently online:</h2>
	<table id="onlinelistTable" class="table table-striped table-hover">
		<tr class="yellow">
			<?php if ($loadOutfits) echo "<th>Outfit</th>"; ?>
			<th>Name:</th>
			<th>Guild:</th>
			<th>Level:</th>
			<th>Vocation:</th>
		</tr>
		<?php
		foreach ($array as $value):
			$url = url("characterprofile.php?name=". $value['name']);
			$flag = ($loadFlags === true && strlen($value['flag']) > 1) ? '<img src="' . $config['country_flags']['server'] . '/' . $value['flag'] . '.png">  ' : '';
			$guildname = (!empty($value['gname'])) ? '<a href="guilds.php?name='. $value['gname'] .'">'. $value['gname'] .'</a>' : '';
			?>
			<tr class="special">
				<?php if ($loadOutfits): ?>
					<td class="outfitColumn"><img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $value['type']; ?>&addons=<?php echo $value['addons']; ?>&head=<?php echo $value['head']; ?>&body=<?php echo $value['body']; ?>&legs=<?php echo $value['legs']; ?>&feet=<?php echo $value['feet']; ?>" alt="img"></td>
				<?php endif; ?>
				<td><?php echo $flag; ?><a href="characterprofile.php?name=<?php echo $value['name']; ?>"><?php echo $value['name']; ?></a></td>
				<td><?php echo $guildname; ?></td>
				<td><?php echo $value['level']; ?></td>
				<td><?php echo vocation_id_to_name($value['vocation']); ?></td>
			</tr>
			<?php
		endforeach; ?>
	</table>
	<?php
else:
	?>
	<p>Nobody is online.</p>
	<?php
endif;

// Players online logout history
if ($history["enabled"]) {
	$time = time();
	if (!empty($recents) && $recents !== false): ?>
		<h2>Online past <?php echo $history['days']; ?> days:</h2>
		<table id="recentlistTable" class="table table-striped table-hover">
			<tr class="yellow">
				<?php if ($loadOutfits) echo "<th>Outfit</th>"; ?>
				<th>Name:</th>
				<th>Guild:</th>
				<th>Level:</th>
				<th>Logout [days] - date</th>
			</tr>
			<?php
			foreach ($recents as $value):
				$days = floor(($time - $value['lastlogout']) / 86400);
				$url = url("characterprofile.php?name=". $value['name']);
				$flag = ($loadFlags === true && strlen($value['flag']) > 1) ? '<img src="' . $config['country_flags']['server'] . '/' . $value['flag'] . '.png">  ' : '';
				$guildname = (!empty($value['gname'])) ? '<a href="guilds.php?name='. $value['gname'] .'">'. $value['gname'] .'</a>' : '';
				?>
				<tr class="special">
					<?php if ($loadOutfits): ?>
						<td class="outfitColumn"><img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $value['type']; ?>&addons=<?php echo $value['addons']; ?>&head=<?php echo $value['head']; ?>&body=<?php echo $value['body']; ?>&legs=<?php echo $value['legs']; ?>&feet=<?php echo $value['feet']; ?>" alt="img"></td>
					<?php endif; ?>
					<td><?php echo $flag; ?><a href="characterprofile.php?name=<?php echo $value['name']; ?>"><?php echo $value['name']; ?></a></td>
					<td><?php echo $guildname; ?></td>
					<td><?php echo $value['level']; ?></td>
					<td><?php echo "{$days}D: " . getClock($value['lastlogout'], true); ?></td>
				</tr>
				<?php
			endforeach; ?>
		</table>
		<?php
	else:
		?>
		<p>Nobody has logged in past <?php echo $history['days']; ?> days.</p>
		<?php
	endif;
}

include 'layout/overall/footer.php'; ?>

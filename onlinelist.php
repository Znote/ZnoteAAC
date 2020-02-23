<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<h1>Who is online?</h1>
<?php

// Returns a list of players online
$array = false;
$loadFlags = ($config['country_flags']['enabled'] && $config['country_flags']['onlinelist']) ? true : false;
$loadOutfits = ($config['show_outfits']['onlinelist']) ? true : false;
if ($config['ServerEngine'] != 'OTHIRE') {
	if ($config['client'] < 780) {
		$outfitQuery = ($loadOutfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`" : "";
	} else {
		$outfitQuery = ($loadOutfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`, `p`.`lookaddons` AS `addons`" : "";
	}
} else {
	$outfitQuery = ($loadOutfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`" : "";
}

// Small 30 seconds players_online cache. 
$cache = new Cache('engine/cache/onlinelist');
$cache->setExpiration(30);
if ($cache->hasExpired()) {
	// Load online list data from SQL
	if ($config['ServerEngine'] == 'TFS_10') {
		$array = ($loadFlags === true) ? mysql_select_multi("SELECT `p`.`name` AS `name`, `p`.`level` AS `level`, `p`.`vocation` AS `vocation`, `g`.`name` AS `gname`, `za`.`flag` AS `flag` $outfitQuery FROM `players_online` AS `o` INNER JOIN `players` AS `p` ON `o`.`player_id` = `p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id` = `za`.`account_id` LEFT JOIN `guild_membership` AS `gm` ON `o`.`player_id` = `gm`.`player_id` LEFT JOIN `guilds` AS `g` ON `gm`.`guild_id` = `g`.`id`;") : mysql_select_multi("SELECT `p`.`name` AS `name`, `p`.`level` AS `level`, `p`.`vocation` AS `vocation`, `g`.`name` AS `gname` $outfitQuery FROM `players_online` AS `o` INNER JOIN `players` AS `p` ON `o`.`player_id` = `p`.`id` LEFT JOIN `guild_membership` AS `gm` ON `o`.`player_id` = `gm`.`player_id` LEFT JOIN `guilds` AS `g` ON `gm`.`guild_id` = `g`.`id`;");
	} else {
		$array = ($loadFlags === true) ? mysql_select_multi("SELECT `p`.`name` as `name`, `p`.`level` as `level`, `p`.`vocation` as `vocation`, `g`.`name` as `gname`, `za`.`flag` as `flag` $outfitQuery FROM `players` as `p` INNER JOIN `znote_accounts` as `za` ON `za`.`account_id` = `p`.`account_id` LEFT JOIN `guild_ranks` as `gr` ON `gr`.`id` = `p`.`rank_id` LEFT JOIN `guilds` as `g` ON `gr`.`guild_id` = `g`.`id` WHERE `p`.`online` = '1' ORDER BY `p`.`name` DESC;") : mysql_select_multi("SELECT `p`.`name` as `name`, `p`.`level` as `level`, `p`.`vocation` as `vocation`, `g`.`name` as `gname` $outfitQuery FROM `players` as `p` LEFT JOIN `guild_ranks` as `gr` ON `gr`.`id` = `p`.`rank_id` LEFT JOIN `guilds` as `g` ON `gr`.`guild_id` = `g`.`id` WHERE `p`.`online` = '1' ORDER BY `p`.`name` DESC;");
	}
	// End loading data from SQL
	$cache->setContent($array);
	$cache->save();
} else {
	$array = $cache->load();
}
// End cache

if (!empty($array) && $array !== false) {
	?>
	
	<table id="onlinelistTable" class="table table-striped table-hover">
		<tr class="yellow">
			<?php if ($loadOutfits) echo "<th>Outfit</th>"; ?>
			<th>Name:</th>
			<th>Guild:</th>
			<th>Level:</th>
			<th>Vocation:</th>
		</tr>
		<?php
		foreach ($array as $value) {
			$url = url("characterprofile.php?name=". $value['name']);
			$flag = ($loadFlags === true && strlen($value['flag']) > 1) ? '<img src="' . $config['country_flags']['server'] . '/' . $value['flag'] . '.png">  ' : '';
			$guildname = (!empty($value['gname'])) ? '<a href="guilds.php?name='. $value['gname'] .'">'. $value['gname'] .'</a>' : '';
			?>
			<tr class="special" onclick="javascript:window.location.href='<?php echo $url; ?>'">
				<?php if ($loadOutfits): ?>
					<td class="outfitColumn"><img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $value['type']; ?>&addons=<?php echo $value['addons']; ?>&head=<?php echo $value['head']; ?>&body=<?php echo $value['body']; ?>&legs=<?php echo $value['legs']; ?>&feet=<?php echo $value['feet']; ?>" alt="img"></td>
				<?php endif; ?>
				<td><?php echo $flag; ?><a href="characterprofile.php?name=<?php echo $value['name']; ?>"><?php echo $value['name']; ?></a></td>
				<td><?php echo $guildname; ?></td>
				<td><?php echo $value['level']; ?></td>
				<td><?php echo vocation_id_to_name($value['vocation']); ?></td>
			</tr>
			<?php
		}
		?>
	</table>

	<?php
} else {
	echo 'Nobody is online.';
}
?>
<?php include 'layout/overall/footer.php'; ?>

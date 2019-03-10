<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; 
protect_page();
admin_only($user_data);
$auction = $config['shop_auction'];
$step = $auction['step'];
$step_duration = $auction['step_duration'];
$loadOutfits = ($config['show_outfits']['highscores']) ? true : false;
function toDuration($is) {
	$duration['day'] = $is / (24 * 60 * 60);
	if (($duration['day'] - (int)$duration['day']) > 0) 
		$duration['hour'] = ($duration['day'] - (int)$duration['day']) * 24;
	if (isset($duration['hour'])) {
		if (($duration['hour'] - (int)$duration['hour']) > 0) 
			$duration['minute'] = ($duration['hour'] - (int)$duration['hour']) * 60;
		if (isset($duration['minute'])) {
			if (($duration['minute'] - (int)$duration['minute']) > 0) 
				$duration['second'] = ($duration['minute'] - (int)$duration['minute']) * 60;
		}
	}
	$tmp = array();
	foreach ($duration as $type => $value) {
		if ($value >= 1) {
			$pluralType = ((int)$value === 1) ? $type : $type . 's';
			if ($type !== 'second') $tmp[] = (int)$value . " $pluralType";
			else $tmp[] = (int)$value . " $pluralType";
		}
	}
	return implode(', ', $tmp);
}
// start

// Passive check to see if bid period has expired and someone won a deal
$time = time();
$expired_auctions = mysql_select_multi("
	SELECT `id`
	FROM `znote_auction_player`
	WHERE `sold` = 0 
	AND `time_end` < {$time} 
	AND `bidder_account_id` > 0
");
//data_dump($expired_auctions, $this_account_id, "expired_auctions");
if ($expired_auctions !== false) {
	$soldIds = array();
	foreach ($expired_auctions as $a) {
		$soldIds[] = $a['id'];
	}
	if (!empty($soldIds)) {
		mysql_update("
			UPDATE `znote_auction_player`
			SET `sold`=1 
			WHERE `id` IN(".implode(',', $soldIds).") 
			LIMIT ".COUNT($soldIds).";
		");
	}
}
// end passive check
// Pending auctions
$pending = mysql_select_multi("
	SELECT 
		`za`.`id` AS `zaid`, 
		`za`.`price`,
		`za`.`bid`,
		`za`.`time_begin`,
		`za`.`time_end`, 
		`p`.`id` AS `player_id`, 
		`p`.`name`, 
		`p`.`vocation`, 
		`p`.`level`, 
		`p`.`lookbody` AS `body`, 
		`p`.`lookfeet` AS `feet`, 
		`p`.`lookhead` AS `head`, 
		`p`.`looklegs` AS `legs`, 
		`p`.`looktype` AS `type`, 
		`p`.`lookaddons` AS `addons`
	FROM `znote_auction_player` za 
	INNER JOIN `players` p
		ON `za`.`player_id` = `p`.`id`
	WHERE `p`.`account_id` = {$auction['storage_account_id']} 
	AND `za`.`claimed` = 0
	AND `za`.`sold` = 1 
	ORDER BY `za`.`time_end` desc
");
// ongoing auctions
$ongoing = mysql_select_multi("
	SELECT 
		`za`.`id` AS `zaid`, 
		`za`.`price`,
		`za`.`bid`,
		`za`.`time_begin`,
		`za`.`time_end`,
		`p`.`vocation`, 
		`p`.`level`, 
		`p`.`lookbody` AS `body`, 
		`p`.`lookfeet` AS `feet`, 
		`p`.`lookhead` AS `head`, 
		`p`.`looklegs` AS `legs`, 
		`p`.`looktype` AS `type`, 
		`p`.`lookaddons` AS `addons`
	FROM `znote_auction_player` za 
	INNER JOIN `players` p
		ON `za`.`player_id` = `p`.`id`
	WHERE `p`.`account_id` = {$auction['storage_account_id']} 
	AND `za`.`sold` = 0
	ORDER BY `za`.`time_end` desc;
");
// Completed auctions
$completed = mysql_select_multi("
	SELECT 
		`za`.`id` AS `zaid`, 
		`za`.`price`,
		`za`.`bid`,
		`za`.`time_begin`,
		`za`.`time_end`, 
		`p`.`id` AS `player_id`, 
		`p`.`name`, 
		`p`.`vocation`, 
		`p`.`level`, 
		`p`.`lookbody` AS `body`, 
		`p`.`lookfeet` AS `feet`, 
		`p`.`lookhead` AS `head`, 
		`p`.`looklegs` AS `legs`, 
		`p`.`looktype` AS `type`, 
		`p`.`lookaddons` AS `addons`
	FROM `znote_auction_player` za 
	INNER JOIN `players` p
		ON `za`.`player_id` = `p`.`id`
	WHERE `za`.`claimed` = 1
	ORDER BY `za`.`time_end` desc
");
?>
<h1>Character Auction History</h1>
<p><strong>Let players sell, buy and bid on characters.</strong>
	<br>Creates a deeper shop economy, encourages players to spend more money in shop for points.
	<br>Pay to win/progress mechanic, but also lets people who can barely afford points to gain it
	<br>by leveling characters to sell. It can also discourages illegal/risky third-party account 
	<br>services. Since players can buy officially & support the server, dodgy competitors have to sell for cheaper.
	<br>Without admin interference this is organic to each individual community economy inflation.</p>
<?php data_dump($config['shop_auction'], false, "config.php: shop_auction") ?>
<h2>Pending orders to be claimed</h2>
<?php if ($pending !== false): ?>
	<table class="auction_char">
		<tr class="yellow">
			<td>Player</td>
			<td>Level</td>
			<td>Vocation</td>
			<td>Price</td>
			<td>Bid</td>
		</tr>
		<?php foreach($pending as $character): ?>
			<tr>
				<td><a href="/characterprofile.php?name=<?php echo $character['name']; ?>"><?php echo $character['name']; ?></a></td>
				<td><?php echo $character['level']; ?></td>
				<td><?php echo vocation_id_to_name($character['vocation']); ?></td>
				<td><?php echo $character['price']; ?></td>
				<td><?php echo $character['bid']; ?></td>
			</tr>
			<tr>
				<td style="text-align: right;"><strong>Added:</strong></td>
				<td><?php echo getClock($character['time_begin'], true); ?></td>
				<td style="text-align: right;"><strong>Ended:</strong></td>
				<td colspan="2"><?php echo getClock($character['time_end'], true); ?></td>
			</tr>
			<tr class="yellow">
				<td colspan="5"></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>

<h2>Ongoing auctions</h2>
<?php if (is_array($ongoing) && !empty($ongoing)): ?>
	<table class="auction_char">
		<tr class="yellow">
			<td>Level</td>
			<td>Vocation</td>
			<td>Details</td>
			<td>Price</td>
			<td>Bid</td>
			<td>Added</td>
			<td>Type</td>
		</tr>
		<?php foreach($ongoing as $character): ?>
			<tr>
				<td><?php echo $character['level']; ?></td>
				<td><?php echo vocation_id_to_name($character['vocation']); ?></td>
				<td><a href="/auctionChar.php?action=view&zaid=<?php echo $character['zaid']; ?>">VIEW</a></td>
				<td><?php echo $character['price']; ?></td>
				<td><?php echo $character['bid']; ?></td>
				<td><?php 
					$ended = (time() > $character['time_end']) ? true : false;
					echo getClock($character['time_begin'], true); 
					?>
				</td>
				<td><?php echo ($ended) ? 'Instant' : 'Bidding<br>('.toDuration(($character['time_end'] - time())).')'; ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>

<h2>Completed auctions</h2>
<?php
if ($completed !== false): ?>
	<table class="auction_char">
		<tr class="yellow">
			<td>Player</td>
			<td>Level</td>
			<td>Vocation</td>
			<td>Price</td>
			<td>Bid</td>
		</tr>
		<?php foreach($completed as $character): ?>
			<tr>
				<td><a href="/characterprofile.php?name=<?php echo $character['name']; ?>"><?php echo $character['name']; ?></a></td>
				<td><?php echo $character['level']; ?></td>
				<td><?php echo vocation_id_to_name($character['vocation']); ?></td>
				<td><?php echo $character['price']; ?></td>
				<td><?php echo $character['bid']; ?></td>
			</tr>
			<tr>
				<td style="text-align: right;"><strong>Added:</strong></td>
				<td><?php echo getClock($character['time_begin'], true); ?></td>
				<td style="text-align: right;"><strong>Ended:</strong></td>
				<td colspan="2"><?php echo getClock($character['time_end'], true); ?></td>
			</tr>
			<tr class="yellow">
				<td colspan="5"></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif;
// end
 include 'layout/overall/footer.php'; ?>
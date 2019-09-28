<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
if ($config['log_ip']) {
	znote_visitor_insert_detailed_data(3);
}

$house = (isset($_GET['id']) && (int)$_GET['id'] > 0) ? (int)$_GET['id'] : false;

if ($house !== false && $config['ServerEngine'] === 'TFS_10') {
	$house_SQL = "SELECT `id`, `owner`, `paid`, `name`, `rent`, `town_id`, `size`, `beds`, `bid`, `bid_end`, `last_bid`, `highest_bidder` FROM `houses` WHERE `id`='$house';";
	$house = mysql_select_single($house_SQL);
	$minbid = $config['houseConfig']['minimumBidSQM'] * $house['size'];
	if ($house['owner'] > 0) $house['ownername'] = user_name($house['owner']);

	if ($config['houseConfig']['shopPoints']['enabled']) {
		$house['points'] = $house['size'];

		foreach ($config['houseConfig']['shopPoints']['cost'] AS $cost_sqm => $cost_points) {
			if ($cost_sqm < $house['size']) $house['points'] = $cost_points;
		}
	}

	//data_dump($house, false, "House data");

	//////////////////////
	// Bid on house logic
	$bid_char = &$_POST['char'];
	$bid_amount = &$_POST['amount'];
	if ($bid_amount && $bid_char) {
		$bid_char = (int)$bid_char;
		$bid_amount = (int)$bid_amount;
		$player = mysql_select_single("SELECT `id`, `account_id`, `name`, `level`, `balance` FROM `players` WHERE `id`='$bid_char' LIMIT 1;");
		
		if (user_logged_in() === true && $player['account_id'] == $session_user_id) {
			// Does player have or need premium?
			$premstatus = $config['houseConfig']['requirePremium'];
			if ($premstatus) {
				$premstatus = mysql_select_single("SELECT `premdays` FROM `accounts` WHERE `id`='".$player['account_id']."' LIMIT 1;");
				$premstatus = ($premstatus['premdays'] > 0) ? true : false;
			} else $premstatus = true;
			if ($premstatus) {
				// Can player have or bid on more houses?
				$pHouseCount = mysql_select_single("SELECT COUNT('id') AS `value` FROM `houses` WHERE ((`highest_bidder`='$bid_char' AND `owner`='$bid_char') OR (`highest_bidder`='$bid_char') OR (`owner`='$bid_char')) AND `id`!='".$house['id']."' LIMIT 1;");
				if ($pHouseCount['value'] < $config['houseConfig']['housesPerPlayer']) {
					// Is character level high enough?
					if ($player['level'] >= $config['houseConfig']['levelToBuyHouse']) {
						// Can player afford this bid?
						if ($player['balance'] > $bid_amount) {
							// Is bid higher than previous bid?
							if ($bid_amount > $house['bid']) {
								// Is bid higher than lowest bid?
								if ($bid_amount > $minbid) {
									// Should only apply to external players, allowing a player to up his pledge without
									// being forced to pay his full previous bid. 
									if ($house['highest_bidder'] != $player['id']) $lastbid = $house['bid'] + 1;
									else {
										$lastbid = $house['last_bid'];
										echo "<b><font color='green'>You have raised the house pledge to ".$bid_amount."gp!</font></b><br>";
									}
									// Has bid already started?
									if ($house['bid_end'] > 0) {
										if ($house['bid_end'] > time()) {
											mysql_update("UPDATE `houses` SET `highest_bidder`='". $player['id'] ."', `bid`='$bid_amount', `last_bid`='$lastbid' WHERE `id`='". $house['id'] ."' LIMIT 1;");
											$house = mysql_select_single("SELECT `id`, `owner`, `paid`, `name`, `rent`, `town_id`, `size`, `beds`, `bid`, `bid_end`, `last_bid`, `highest_bidder` FROM `houses` WHERE `id`='". $house['id'] ."';");
										}
									} else {
										$lastbid = $minbid + 1;
										$bidend = time() + $config['houseConfig']['auctionPeriod'];
										mysql_update("UPDATE `houses` SET `highest_bidder`='". $player['id'] ."', `bid`='$bid_amount', `last_bid`='$lastbid', `bid_end`='$bidend' WHERE `id`='". $house['id'] ."' LIMIT 1;");
										$house = mysql_select_single("SELECT `id`, `owner`, `paid`, `name`, `rent`, `town_id`, `size`, `beds`, `bid`, `bid_end`, `last_bid`, `highest_bidder` FROM `houses` WHERE `id`='". $house['id'] ."';");
									}
									echo "<b><font color='green'>You have the highest bid on this house!</font></b>";
								} else echo "<b><font color='red'>You need to place a bid that is higher or equal to {$minbid}gp.</font></b>";
							} else {
								// Check if current bid is higher than last_bid
								if ($bid_amount > $house['last_bid']) {
									// Should only apply to external players, allowing a player to up his pledge without
									// being forced to pay his full previous bid.
									if ($house['highest_bidder'] != $player['id']) {
										$lastbid = $bid_amount + 1;
										mysql_update("UPDATE `houses` SET `last_bid`='$lastbid' WHERE `id`='". $house['id'] ."' LIMIT 1;");
										$house = mysql_select_single("SELECT `id`, `owner`, `paid`, `name`, `rent`, `town_id`, `size`, `beds`, `bid`, `bid_end`, `last_bid`, `highest_bidder` FROM `houses` WHERE `id`='". $house['id'] ."';");
										echo "<b><font color='orange'>Unfortunately your bid was not higher than previous bidder.</font></b>";
									} else {
										echo "<b><font color='orange'>You already have a higher pledge on this house.</font></b>";
									}
								} else {
									echo "<b><font color='red'>Too low bid amount, someone else has a higher bid active.</font></b>";
								}
							}
						} else echo "<b><font color='red'>You don't have enough money to bid this high.</font></b>";
					} else echo "<b><font color='red'>Your character is to low level, must be higher level than ", $config['houseConfig']['levelToBuyHouse']-1 ," to buy a house.</font></b>";
				} else echo "<b><font color='red'>You cannot have more houses.</font></b>";
			} else echo "<b><font color='red'>You need premium account to purchase houses.</font></b>";
		} else echo "<b><font color='red'>You may only bid on houses for characters on your account.</font></b>";
	}

	////////////////////////////////////////
	// Instantly buy house with shop points 
	if ($config['houseConfig']['shopPoints']['enabled'] 
		&& isset($_POST['instantbuy']) 
		&& $bid_char
		&& $house['owner'] == 0 
		&& isset($house['points'])) {

		$account_points = (int)$user_znote_data['points'];

		if ($account_points >= $house['points']) {

			$bid_char = (int)$bid_char;
			$player = mysql_select_single("SELECT `id`, `account_id`, `name`, `level` FROM `players` WHERE `id`='$bid_char' LIMIT 1;");
			$pHouseCount = mysql_select_single("SELECT COUNT('id') AS `value` FROM `houses` WHERE ((`highest_bidder`='$bid_char' AND `owner`='$bid_char') OR (`highest_bidder`='$bid_char') OR (`owner`='$bid_char')) AND `id`!='".$house['id']."' LIMIT 1;");

			if (user_logged_in() === true 
				&& $player['account_id'] == $session_user_id
				&& $player['level'] >= $config['houseConfig']['levelToBuyHouse']
				&& $pHouseCount['value'] < $config['houseConfig']['housesPerPlayer']) {
				
				$house_points = (int)$house['points'];
				$house_id = $house['id'];
				
				// Remove points from account
				mysql_update("
					UPDATE `znote_accounts` 
					SET `points` = `points`-{$house_points} 
					WHERE `account_id`={$session_user_id} 
					LIMIT 1;
				");
				
				// Give new ownership to house
				mysql_update("
					UPDATE `houses` 
					SET `owner` = {$bid_char}
					WHERE `id` = {$house_id} 
					LIMIT 1;
				");
				
				// Log purchase in znote_shop_logs and znote_shop_orders
				$time = time();
				mysql_insert("
					INSERT INTO `znote_shop_logs` 
					(`account_id`, `player_id`, `type`, `itemid`, `count`, `points`, `time`) VALUES 
					({$session_user_id}, {$bid_char}, 7, {$house_id}, 1, {$house_points}, {$time})
				");
				mysql_insert("
					INSERT INTO `znote_shop_orders` 
					(`account_id`, `type`, `itemid`, `count`, `time`) VALUES 
					({$session_user_id}, 7, {$house_id}, {$bid_char}, {$time})
				");
				
				// Reload house data
				$house = mysql_select_single($house_SQL);
				$minbid = $config['houseConfig']['minimumBidSQM'] * $house['size'];
				if ($house['owner'] > 0) $house['ownername'] = user_name($house['owner']);
				
				// Congratulate user and tell them they still has to pay rent (if rent > 0)
				?>
				<p><strong>Congratulations!</strong>
					<br>You now own this house!
					<br>Remember to say <strong>!shop</strong> in-game to process your ownership!
					<?php if ($house['rent'] > 0): ?>
						<br>Keep in mind you still need to pay rent on this house, make sure you have enough bank balance to cover it!
					<?php endif; ?>
				</p>
				<?php
			} else {
				?>
				<p><strong>Error:</strong>
					<br>Either your level is too low, or your player already have or is bidding on another house.
					<br>Your level: <?php echo $player['level']; ?>. Minimum level to buy house: <?php echo $config['houseConfig']['levelToBuyHouse']; ?>
					<br>Your house/bid count: <?php echo $pHouseCount['value']; ?>. Maximum house per player: <?php echo $config['houseConfig']['housesPerPlayer']; ?>.
				</p>
				<?php
			}
		}
	}

	// HTML structure and logic
	?>
	<h1>House: <?php echo $house['name']; ?></h1>
	<ul>
		<li><b>Town</b>: 
		<?php
		$town_name = &$config['towns'][$house['town_id']];
		echo "<a href='houses.php?id=". $house['town_id'] ."'>". ($town_name ? $town_name : 'Specify town id ' . $house['town_id'] . ' name in config.php first.') ."</a>";
		?></li>
		<li><b>Size</b>: <?php echo $house['size']; ?></li>
		<li><b>Beds</b>: <?php echo $house['beds']; ?></li>
		<li><b>Owner</b>: <?php
		if ($house['owner'] > 0) echo "<a href='characterprofile.php?name=". $house['ownername'] ."' target='_BLANK'>". $house['ownername'] ."</a>";
		else echo "Available for auction.";
		?></li>
		<li><b>Rent</b>: <?php echo $house['rent']; ?></li>
		<?php if ($house['owner'] == 0 && isset($house['points'])): ?>
			<li><b>Shop points</b>: <?php echo $house['points']; ?></li>
		<?php endif; ?>
	</ul>
	<?php
	// AUCTION MARKUP INIT
	if ($house['owner'] == 0) {
		?>
		<h2>This house is up on auction!</h2>
		<?php
		if ($house['highest_bidder'] == 0) echo "<b>This house don't have any bidders yet.</b>";
		else {
			$bidder = mysql_select_single("SELECT `name` FROM `players` WHERE `id`='". $house['highest_bidder'] ."' LIMIT 1;");
			echo "<b>This house have bidders! If you want this house, now is your chance!</b>";
			echo "<br><b>Active bid:</b> ". $house['last_bid'] ."gp";
			echo "<br><b>Active bid by:</b> <a href='characterprofile.php?name=". $bidder['name'] ."' target='_BLANK'>". $bidder['name'] ."</a>";
			echo "<br><b>Bid will end on:</b> ". getClock($house['bid_end'], true);
		}

		if ($house['bid_end'] == 0 || $house['bid_end'] > time()) {
			if (user_logged_in()) {
				// Your characters, indexed by char_id
				$yourChars = mysql_select_multi("SELECT `id`, `name`, `balance` FROM `players` WHERE `account_id`='". $user_data['id'] ."';");
				if ($yourChars !== false) {
					$charData = array();
					foreach ($yourChars as $char) {
						$charData[$char['id']] = $char;
					}
					?>
					<form class="house_form_bid" action="" method="post">
						<select name="char">
							<?php
							foreach ($charData as $id => $char) {
								echo "<option value='$id'>". $char['name'] ." [". $char['balance'] ."]</option>";
							}
							?>
						</select>
						<input type="text" name="amount" placeholder="Min bid: <?php echo $minbid + 1; ?>">
						<input type="submit" value="Bid on this house">
					</form>
					<?php if ($house['owner'] == 0 && isset($house['points'])): ?>
						<br>
						<?php if ((int)$user_znote_data['points'] >= $house['points']): ?>
							<form class="house_form_buy" action="" method="post">
								<p>Your account has <strong><?php echo $user_znote_data['points']; ?></strong> available shop points.</p>
								<select name="char">
									<?php
									foreach ($charData as $id => $char) {
										echo "<option value='$id'>". $char['name'] ."</option>";
									}
									?>
								</select>
								<input type="submit" name="instantbuy" value="Buy now for <?php echo $house['points']; ?> shop points!">
							</form>
						<?php else: ?>
							<p>Your account has <strong><?php echo $user_znote_data['points']; ?></strong> available shop points.
								<br>You don't have enough shop points to instantly buy this house.</p>
						<?php endif; ?>
					<?php endif; ?>
					<?php
				} else echo "<br>You need a character to bid on this house.";
			} else echo "<br>You need to login before you can bid on houses.";
		} else echo "<br><b>Bid has ended! House transaction will proceed next server restart assuming active bidder have sufficient balance.</b>";
	}
} else {
	?>
	<h1>No house selected.</h1>
	<p>Go back to the <a href="houses.php">house list</a> and select a house for further details.</p>
	<?php
}
include 'layout/overall/footer.php'; ?>

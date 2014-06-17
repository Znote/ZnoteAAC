<?php require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php'; 

// Import from config:
$shop = $config['shop'];
$shop_list = $config['shop_offers'];

if (!empty($_POST['buy'])) {
	$time = time();
	$player_points = (int)$user_znote_data['points'];
	$cid = (int)$user_data['id'];
	// Sanitizing post, setting default buy value
	$buy = false;
	$post = (int)$_POST['buy'];
	
	foreach ($shop_list as $key => $value) {
		if ($key === $post) {
			$buy = $value;
		}
	}
	if ($buy === false) die("Error: Shop offer ID mismatch.");
	
	// Verify that user can afford this offer.
	if ($player_points >= $buy['points']) {
		$data = mysql_select_single("SELECT `points` FROM `znote_accounts` WHERE `account_id`='$cid';");
		if (!$data) die("0: Account is not converted to work with Znote AAC");
		$old_points = $data['points'];
		if ((int)$old_points != (int)$player_points) die("1: Failed to equalize your points.");
		// Remove points if they can afford
		// Give points to user
		$expense_points = $buy['points'];
		$new_points = $old_points - $expense_points;
		$update_account = mysql_update("UPDATE `znote_accounts` SET `points`='$new_points' WHERE `account_id`='$cid'");
		
		$data = mysql_select_single("SELECT `points` FROM `znote_accounts` WHERE `account_id`='$cid';");
		$verify = $data['points'];
		if ((int)$old_points == (int)$verify) die("2: Failed to equalize your points.". var_dump((int)$old_points, (int)$verify, $new_points, $expense_points));
		
		// Do the magic (insert into db, or change sex etc)
		// If type is 2 or 3
		if ($buy['type'] == 2) {
			// Add premium days to account
			user_account_add_premdays($cid, $buy['count']);
			echo '<font color="green" size="4">You now have '.$buy['count'].' additional days of premium membership.</font>';
		} else if ($buy['type'] == 3) {
			// Character Gender
			mysql_insert("INSERT INTO `znote_shop_orders` (`account_id`, `type`, `itemid`, `count`, `time`) VALUES ('$cid', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '$time')");
			echo '<font color="green" size="4">You now have access to change character gender on your characters. Visit <a href="myaccount.php">My Account</a> to select character and change the gender.</font>';
		} else if ($buy['type'] == 4) {
			// Character Name
			mysql_insert("INSERT INTO `znote_shop_orders` (`account_id`, `type`, `itemid`, `count`, `time`) VALUES ('$cid', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '$time')");
			echo '<font color="green" size="4">You now have access to change character name on your characters. Visit <a href="myaccount.php">My Account</a> to select character and change the name.</font>';
		} else {
			mysql_insert("INSERT INTO `znote_shop_orders` (`account_id`, `type`, `itemid`, `count`, `time`) VALUES ('$cid', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '$time')");
			echo '<font color="green" size="4">Your order is ready to be delivered. Write this command in-game to get it: [!shop].<br>Make sure you are in depot and can carry it before executing the command!</font>';
		}
		
		// No matter which type, we will always log it.
		mysql_insert("INSERT INTO `znote_shop_logs` (`account_id`, `player_id`, `type`, `itemid`, `count`, `points`, `time`) VALUES ('$cid', '0', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '". $buy['points'] ."', '$time')");
		
	} else echo '<font color="red" size="4">You need more points, this offer cost '.$buy['points'].' points.</font>';
	//var_dump($buy);
	//echo '<font color="red" size="4">'. $_POST['buy'] .'</font>';
}

if ($shop['enabled']) {
?>

<h1>Shop Offers</h1>
<?php
if (!empty($_POST['buy'])) {
	if ($user_znote_data['points'] >= $buy['points']) {
		?><td>You have <?php echo (int)($user_znote_data['points'] - $buy['points']); ?> points. (<a href="buypoints.php">Buy points</a>).</td><?php
	} else {
		?><td>You have <?php echo $user_znote_data['points']; ?> points. (<a href="buypoints.php">Buy points</a>).</td><?php
	}
} else {
	?><td>You have <?php echo $user_znote_data['points']; ?> points. (<a href="buypoints.php">Buy points</a>).</td><?php
}
if ($config['shop_auction']['characterAuction']) {
	?>
	<p>Interested in buying characters? View the <a href="auctionChar.php">character auction page!</a></p>
	<?php
}
?>
<table>
	<tr class="yellow">
		<td>Description:</td>
		<?php if ($config['shop']['showImage']) { ?><td>Image:</td><?php } ?>
		<td>Count/duration:</td>
		<td>Points:</td>
		<td>Action:</td>
	</tr>
		<?php
		foreach ($shop_list as $key => $offers) {
		echo '<tr class="special">';
		echo '<td>'. $offers['describtion'] .'</td>';
		if ($config['shop']['showImage']) echo '<td><img src="http://'. $config['shop']['imageServer'] .'/'. $offers['itemid'] .'.'. $config['shop']['imageType'] .'" alt="img"></td>';
		if ($offers['type'] == 2) echo '<td>'. $offers['count'] .' Days</td>';
		else if ($offers['type'] == 3 && $offers['count'] == 0) echo '<td>Unlimited</td>';
		else echo '<td>'. $offers['count'] .'x</td>';
		echo '<td>'. $offers['points'] .'</td>';
		echo '<td>';
		?>
		<form action="" method="POST">
			<input type="hidden" name="buy" value="<?php echo (int)$key; ?>">
			<input type="submit" value="  PURCHASE  "  class="needconfirmation" data-item-name="<?php echo $offers['describtion']; ?>" data-item-cost="<?php echo $offers['points']; ?>">
		</form>
		<?php
		echo '</td>';
		echo '</tr>';
		}
		?>
</table>

<?php if ($shop['enableShopConfirmation']) { ?>
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $(".needconfirmation").each(function(e){
            $(this).click(function(e){
                var itemname = $(this).attr("data-item-name");
                var itemcost = $(this).attr("data-item-cost");
				var r = confirm("Do you really want to purchase "+itemname+" for "+itemcost+" points?")
				if(r == false){
					e.preventDefault();
				}			
            });
        });
    });
</script>
<?php }
} else echo '<h1>Buy Points system disabled.</h1><p>Sorry, this functionality is disabled.</p>';
include 'layout/overall/footer.php'; ?>


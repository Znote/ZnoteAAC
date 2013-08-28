<?php
	// Require the functions to connect to database and fetch config values
	require 'config.php';
	require 'engine/database/connect.php';
	
	// Fetch paypal configurations
	$paypal = $config['paypal'];
	$prices = $config['paypal_prices'];
	
	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}
	
	// post back to PayPal system to validate
	$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
	
	// assign posted variables to local variables
	$item_name = $_POST['item_name'];
	$item_number = $_POST['item_number'];
	$payment_status = $_POST['payment_status'];
	$payment_amount = $_POST['mc_gross'];
	$payment_currency = $_POST['mc_currency'];
	$txn_id = mysql_real_escape_string($_POST['txn_id']);
	$receiver_email = $_POST['receiver_email'];
	$payer_email = mysql_real_escape_string($_POST['payer_email']);
	$custom = $_POST['custom'];
	
	if (!$fp) {
		// HTTP ERROR
	} else {
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			if (strcmp ($res, "VERIFIED") == 0) {
				if ($payment_status == 'Completed') {
					$txn_id_check = mysql_query("SELECT `txn_id` FROM `znote_paypal` WHERE `txn_id`='$txn_id'");
					if (mysql_num_rows($txn_id_check) != 1) {
						if ($receiver_email == $paypal['email']) {
							
							$status = true;
							$pieces = explode("!", $custom);
							// TODO - fix this logic
							// 0 = user_id, 1 = price, 2 = points
							$f_user_id = (int)$pieces[0];
							$f_price = (float)$pieces[1];
							$f_points = (int)$pieces[2];
							if ($payment_amount != $f_price) $status = false; // If he paid wrong ammount
							if ($payment_currency != $paypal['currency']) $status = false; // If he paid using another currency
							
							// Verify that the user havent messed around with POST data
							if ($status) {
								$status = false;
								foreach ($prices as $price => $points) {
									if ($price == $f_price && $points == $f_points) $status = true; // data does not appear to be manipulated.
								}
								if ($status) {
									// transaction log
									$log_query = mysql_query("INSERT INTO `znote_paypal` VALUES ('', '$txn_id', '$payer_email', '$f_user_id', '".(int)$f_price."', '".(int)$f_points."')");
									
									// Give points to user
									$old_points = mysql_result(mysql_query("SELECT `points` FROM `znote_accounts` WHERE `account_id`='$f_user_id';"), 0, 'points');
									$new_points = (int)$f_points;
									$new_points += $old_points;
									$update_account = mysql_query("UPDATE `znote_accounts` SET `points`='$new_points' WHERE `account_id`='$f_user_id'");
								} else mysql_query("INSERT INTO `znote_paypal` VALUES ('', '$txn_id', 'ERROR: HACKER detected: $payer_email', '$f_user_id', '".(int)$f_price."', '".(int)$f_points."')");
							}
						}  else {
							$pmail = $paypal['email'];
							mysql_query("INSERT INTO `znote_paypal` VALUES ('', '$txn_id', 'ERROR: Wrong mail. Received: $receiver_email, configured: $pmail', '0', '0', '0')");
						}
					}
				}
			}
			else if (strcmp ($res, "INVALID") == 0) {
				// log for manual investigation
				
			}
		}
		fclose ($fp);
	}
?>
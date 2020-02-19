<?php
	if (gethostbyaddr($_SERVER['REMOTE_ADDR']) !== 'notify.paypal.com') {
		exit();
	}

	// Require the functions to connect to database and fetch config values
	require 'config.php';
	require 'engine/database/connect.php';
	
	// Fetch and sanitize POST and GET values
	function getValue($value) {
		return (!empty($value)) ? sanitize($value) : false;
	}
	function sanitize($data) {
		return htmlentities(strip_tags(mysql_znote_escape_string($data)));
	}
	
	function VerifyPaypalIPN(array $IPN = null){
		if(empty($IPN)){
			$IPN = $_POST;
		}
		if(empty($IPN['verify_sign'])){
			return null;
		}
		$IPN['cmd'] = '_notify-validate';
		$PaypalHost = (empty($IPN['test_ipn']) ? 'www' : 'www.sandbox').'.paypal.com';
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($cURL, CURLOPT_SSLVERSION, 6);
		curl_setopt($cURL, CURLOPT_CAINFO, __DIR__ . '/engine/cert/cacert.pem');
		curl_setopt($cURL, CURLOPT_URL, "https://{$PaypalHost}/cgi-bin/webscr");
		curl_setopt($cURL, CURLOPT_ENCODING, 'gzip');
		curl_setopt($cURL, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($cURL, CURLOPT_POST, true); // POST back
		curl_setopt($cURL, CURLOPT_POSTFIELDS, $IPN); // the $IPN
		curl_setopt($cURL, CURLOPT_HEADER, false);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cURL, CURLOPT_FORBID_REUSE, true);
		curl_setopt($cURL, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($cURL, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($cURL, CURLOPT_TIMEOUT, 60);
		curl_setopt($cURL, CURLINFO_HEADER_OUT, true);
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
			'Connection: close',
			'Expect: ',
		));
		$Response = curl_exec($cURL);
		$Status = (int)curl_getinfo($cURL, CURLINFO_HTTP_CODE);
		curl_close($cURL);
		if(empty($Response) or !preg_match('~^(VERIFIED|INVALID)$~i', $Response = trim($Response)) or !$Status){
			return null;
		}
		if(intval($Status / 100) != 2){
			return false;
		}
		return !strcasecmp($Response, 'VERIFIED');
	}

	// Fetch paypal configurations
	$paypal = $config['paypal'];
	$prices = $config['paypal_prices'];
	
	// Send an empty HTTP 204 OK response to acknowledge receipt of the notification 
	http_response_code(204);

	// Build the required acknowledgement message out of the notification just received
	$postdata = 'cmd=_notify-validate';
	if(!empty($_POST)){
		$postdata.="&".http_build_query($_POST);
	}
	// Assign payment notification values to local variables
	$item_name        = $_POST['item_name'];
	$item_number      = $_POST['item_number'];
	$payment_status   = $_POST['payment_status'];
	$payment_amount   = $_POST['mc_gross'];
	$payment_currency = $_POST['mc_currency'];
	$txn_id           = getValue($_POST['txn_id']);
	$receiver_email   = getValue($_POST['receiver_email']);
	$payer_email      = getValue($_POST['payer_email']);
	$custom           = (int)$_POST['custom'];

	$connectedIp = $_SERVER['REMOTE_ADDR'];
	mysql_insert("INSERT INTO `znote_paypal` VALUES ('0', '0', 'Connection from IP: $connectedIp', '0', '0', '0')");
	
	$status = VerifyPaypalIPN();
	if ($status) {
		// Check that the payment_status is Completed
		if ($payment_status == 'Completed') {

			
			// Check that txn_id has not been previously processed
			$txn_id_check = mysql_select_single("SELECT `txn_id` FROM `znote_paypal` WHERE `txn_id`='$txn_id'");
			if ($txn_id_check !== true) {
				// Check that receiver_email is your Primary PayPal email
				if ($receiver_email == $paypal['email']) {
					
					$status = true;
					$paidMoney = 0;
					$paidPoints = 0;

					foreach ($prices as $priceValue => $pointsValue) {
						if ($priceValue == $payment_amount) {
							$paidMoney = $priceValue;
							$paidPoints = $pointsValue;
						}
					}

					if ($paidMoney == 0) $status = false; // Wrong ammount of money
					if ($payment_currency != $paypal['currency']) $status = false; // Wrong currency
					
					// Verify that the user havent messed around with POST data
					if ($status) {
						// transaction log
						mysql_insert("INSERT INTO `znote_paypal` VALUES ('0', '$txn_id', '$payer_email', '$custom', '".$paidMoney."', '".$paidPoints."')");
						
						// Process payment
						$data = mysql_select_single("SELECT `points` AS `old_points` FROM `znote_accounts` WHERE `account_id`='$custom';");

						// Give points to user
						$new_points = $data['old_points'] + $paidPoints;
						mysql_update("UPDATE `znote_accounts` SET `points`='$new_points' WHERE `account_id`='$custom'");
					}
				}  else {
					$pmail = $paypal['email'];
					mysql_insert("INSERT INTO `znote_paypal` VALUES ('0', '$txn_id', 'ERROR: Wrong mail. Received: $receiver_email, configured: $pmail', '0', '0', '0')");
				}
			}
		}
	} else {
		// Something is wrong
		mysql_insert("INSERT INTO `znote_paypal` VALUES ('0', '$txn_id', 'ERROR: Invalid data. $postdata', '0', '0', '0')");
	}
?>

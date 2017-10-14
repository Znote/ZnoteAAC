<?php
require_once 'engine/init.php';

// Client 11 loginWebService
if($_SERVER['HTTP_USER_AGENT'] == "Mozilla/5.0" && $config['TFSVersion'] === 'TFS_10') {

	function jsonError($message, $code = 3) {
		die(json_encode(array('errorCode' => $code, 'errorMessage' => $message)));
	}

	header("Content-Type: application/json");
	$input = file_get_contents("php://input");

	// Based on tests, input length should be at least 67+ chars.
	if (strlen($input) > 10) {
		/* {
			'accountname' => 'username',
			'password' => 'superpass',
			'stayloggedin' => true,
			'token' => '123123', (or not set)
			'type' => 'login', (What other types do we have?)
		} */
		$jsonObject = json_decode($input);

		$username = sanitize($jsonObject->accountname);
		$password = SHA1($jsonObject->password);
		$token = (isset($jsonObject->token)) ? sanitize($jsonObject->token) : false;
		
		$fields = '`id`, `premdays`, `secret`';
		if ($config['twoFactorAuthenticator']) $fields .= ', `secret`';

		$account = mysql_select_single("SELECT {$fields} FROM `accounts` WHERE `name`='{$username}' AND `password`='{$password}' LIMIT 1;");
		if ($account === false) {
			jsonError('Wrong username and/or password.');
		}

		if ($config['twoFactorAuthenticator'] === true && $account['secret'] !== null) {
			if ($token === false) {
				jsonError('Submit a valid two-factor authentication token.', 6);
			} else {
				require_once("engine/function/rfc6238.php");
				if (TokenAuth6238::verify($account['secret'], $token) !== true) {
					jsonError('Two-factor authentication failed, token is wrong.', 6);
				}
			}
		}

		$players = mysql_select_multi("SELECT `name`, `sex` FROM `players` WHERE `account_id`='".$account['id']."';");
		if ($players !== false) {

			$gameserver = $config['gameserver'];
			// todo: Fix dynamic desition to pass along token. (and verify that it works). Hostname: otx11.lan
			$sessionKey = $username."\n".$jsonObject->password;
			if (strlen($account['secret']) > 5) $sessionKey .= "\n".$token."\n".floor(time() / 30);
			$response = array(
				'session' => array(
					'fpstracking' => false,
					'isreturner' => true,
					'returnernotification' => false,
					'showrewardnews' => false,
					'sessionkey' => $sessionKey,
					'lastlogintime' => 0,
					'ispremium' => ($account['premdays'] > 0) ? true : false,
					'premiumuntil' => time() + ($account['premdays'] * 86400),
					'status' => 'active'
				),
				'playdata' => array(
					'worlds' => array(
						array(
							'id' => 0,
							'name' => $gameserver['name'],
							'externaladdress' => $gameserver['ip'],
							'externalport' => $gameserver['port'],
							'previewstate' => 0,
							'location' => 'ALL',
							'externaladdressunprotected' => $gameserver['ip'],
							'externaladdressprotected' => $gameserver['ip'],
							'anticheatprotection' => false
						)
					),
					'characters' => array(
						//array( 'worldid' => ASD, 'name' => asd, 'ismale' => true, 'tutorial' => false ),
					)
				)
			);

			foreach ($players as $player) {
				$response['playdata']['characters'][] = array(
					'worldid' => 0,
					'name' => $player['name'],
					'ismale' => ($player['sex'] === 1) ? true : false,
					'tutorial' => false
				);
			}

			//error_log("= SESSION KEY: " . $response['session']['sessionkey']);
			die(json_encode($response));
		} else {
			jsonError("Character list is empty.");
		}
	} else {
		jsonError("Unrecognized event.");
	}
} // End client 11 loginWebService

logged_in_redirect();
include 'layout/overall/header.php';

if (empty($_POST) === false) {

	if ($config['log_ip']) {
		znote_visitor_insert_detailed_data(5);
	}

	$username = $_POST['username'];
	$password = $_POST['password'];

	if (empty($username) || empty($password)) {
		$errors[] = 'You need to enter a username and password.';
	} else if (strlen($username) > 32 || strlen($password) > 64) {
			$errors[] = 'Username or password is too long.';
	} else if (user_exist($username) === false) {
		$errors[] = 'Failed to authorize your account, are the details correct, have you <a href=\'register.php\'>register</a>ed?';
	} /*else if (user_activated($username) === false) {
		$errors[] = 'You havent activated your account! Please check your email. <br>Note it may appear in your junk/spam box.';
	} */else if ($config['use_token'] && !Token::isValid($_POST['token'])) {
		Token::debug($_POST['token']);
		$errors[] = 'Token is invalid.';
	} else {

		// Starting loging
		if ($config['TFSVersion'] == 'TFS_02' || $config['TFSVersion'] == 'TFS_10') $login = user_login($username, $password);
		else if ($config['TFSVersion'] == 'TFS_03') $login = user_login_03($username, $password);
		else $login = false;
		if ($login === false) {
			$errors[] = 'Username and password combination is wrong.';
		} else {
			// Check if user have access to login
			$status = false;
			if ($config['mailserver']['register']) {
				$authenticate = mysql_select_single("SELECT `id` FROM `znote_accounts` WHERE `account_id`='$login' AND `active`='1' LIMIT 1;");
				if ($authenticate !== false) {
					$status = true;
				} else {
					$errors[] = "Your account is not activated. An email should have been sent to you when you registered. Please find it and click the activation link to activate your account.";
				}
			} else $status = true;

			if ($status) {
				// Regular login success, now lets check authentication token code
				if ($config['TFSVersion'] == 'TFS_10' && $config['twoFactorAuthenticator']) {
					require_once("engine/function/rfc6238.php");

					// Two factor authentication code / token
					$authcode = (isset($_POST['authcode'])) ? getValue($_POST['authcode']) : false;

					// Load secret values from db
					$query = mysql_select_single("SELECT `a`.`secret` AS `secret`, `za`.`secret` AS `znote_secret` FROM `accounts` AS `a` INNER JOIN `znote_accounts` AS `za` ON `a`.`id` = `za`.`account_id` WHERE `a`.`id`='".(int)$login."' LIMIT 1;");

					// If account table HAS a secret, we need to validate it
					if ($query['secret'] !== NULL) {

						// Validate the secret first to make sure all is good.
						if (TokenAuth6238::verify($query['secret'], $authcode) !== true) {
							$errors[] = "Submitted Two-Factor Authentication token is wrong.";
							$errors[] = "Make sure to type the correct token from your mobile authenticator.";
							$status = false;
						}

					} else {

						// secret from accounts table is null/not set. Perhaps we can activate it:
						if ($query['znote_secret'] !== NULL && $authcode !== false && !empty($authcode)) {

							// Validate the secret first to make sure all is good.
							if (TokenAuth6238::verify($query['znote_secret'], $authcode)) {
								// Success, enable the 2FA system
								mysql_update("UPDATE `accounts` SET `secret`= '".$query['znote_secret']."' WHERE `id`='$login';");
							} else {
								$errors[] = "Activating Two-Factor authentication failed.";
								$errors[] = "Try to login without token and configure your app properly.";
								$errors[] = "Submitted Two-Factor Authentication token is wrong.";
								$errors[] = "Make sure to type the correct token from your mobile authenticator.";
								$status = false;
							}
						}
					}
				} // End tfs 1.0+ with 2FA auth

				if ($status) {
					setSession('user_id', $login);

					// if IP is not set (etc acc created before Znote AAC was in use)
					$znote_data = user_znote_account_data($login);
					if ($znote_data['ip'] == 0) {
						$update_data = array(
						'ip' => getIPLong(),
						);
						user_update_znote_account($update_data);
					}

					// Send them to myaccount.php
					header('Location: myaccount.php');
					exit();
				}
			}
		}
	}
} else {
	header('Location: index.php');
}

if (empty($errors) === false) {
	?>
	<h2>We tried to log you in, but...</h2>
	<?php
	header("HTTP/1.1 401 Not Found");
	echo output_errors($errors);
}

include 'layout/overall/footer.php'; ?>

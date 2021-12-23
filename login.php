<?php
require_once 'engine/init.php';

// Client 11 loginWebService
// DEV: Uncomment all //error_log lines and tail error.log file to see communication from and to client.
// ...: Configure webserver to don't display PHP errors/warnings so the client can parse the json response.
if($_SERVER['HTTP_USER_AGENT'] == "Mozilla/5.0" && $config['ServerEngine'] === 'TFS_10' && $config['login_web_service'] == true) {

	function sendError($message, $code = 3) {
		$response = json_encode(array('errorCode' => $code, 'errorMessage' => $message));
		//error_log("\nServer = " . $response . "\n-");
		die($response);
	}

	function sendMessage($message) {
		$response = json_encode($message);
		//error_log("\nServer = " . $response . "\n\n-");
		die($response);
	}


	header("Content-Type: application/json");
	$input = file_get_contents("php://input");
	//error_log("\n\n\nClient = " . $input . "\n");

	$client = json_decode($input);

	if (!isset($client->type)) {
		sendError("Type missing.");
	}

	switch($client->type) {
		// {"count":0,"isreturner":true,"offset":0,"showrewardnews":false,"type":"news"}
		case "cacheinfo":
			// {"type":"cacheinfo"}
			sendMessage(array(
				'playersonline' => (int)user_count_online(),
				'twitchstreams' => 0,
				'twitchviewer' => 0,
				'gamingyoutubestreams' => 0,
				'gamingyoutubeviewer' => 0
			));
			break;

		case 'eventschedule':
			// {"type":"eventschedule"}
			$eventlist = [];
				$file_path = $config['server_path'] . 'data/XML/events.xml';
				/*  <?xml version="1.0" encoding="UTF-8"?>
					<events>
						<event name="Otservbr example 1" startdate="11/03/2020" enddate="11/30/2020" >
							<ingame exprate="250" lootrate="200" spawnrate="100" skillrate="200" />
							<description description="Otserver br example 1 description double exp and a half, double loot !chance!, regular spawn and double skill" />
							<colors colordark="#235c00" colorlight="#2d7400" />
							<details displaypriority="6" isseasonal="0" specialevent="0" />
						</event>
						<event name="Otservbr example 2" startdate="12/01/2020" enddate="12/26/2020" >
							<ingame exprate="50" lootrate="300" spawnrate="150" skillrate="100" />
							<description description="Otserver br example 2 description 50% less exp, triple loot !chance!, 50% faster spawn and regular skill" />
							<colors colordark="#735D10" colorlight="#8B6D05" />
							<details displaypriority="6" isseasonal="0" specialevent="0" />
						</event>
					</events>
				*/
				if (!file_exists($file_path)) {
					sendMessage(array(
						'eventlist' => array()
					));
				}
				$xml = new DOMDocument;
				$xml->load($file_path);
				$tableevent = $xml->getElementsByTagName('event');

				if (!function_exists("parseEvent")) {
					function parseEvent($table1, $date, $table2) {
						if ($table1) {
							if ($date) {
								if ($table2) {
									$date = $table1->getAttribute('startdate');
									return date_create("{$date}")->format('U');
								} else {
									$date = $table1->getAttribute('enddate');
									return date_create("{$date}")->format('U');
								}
							} else {
								foreach($table1 as $attr) {
									if ($attr) {
										return $attr->getAttribute($table2);
									}
								}
							}
						}
						return;
					}
				}

				foreach ($tableevent as $event) {
					if ($event) {
						$eventlist[] = array(
							'colorlight' => parseEvent($event->getElementsByTagName('colors'), false, 'colorlight'),
							'colordark' => parseEvent($event->getElementsByTagName('colors'), false, 'colordark'),
							'description' => parseEvent($event->getElementsByTagName('description'), false, 'description'),
							'displaypriority' => intval(parseEvent($event->getElementsByTagName('details'), false, 'displaypriority')),
							'enddate' => intval(parseEvent($event, true, false)),
							'isseasonal' => (intval(parseEvent($event->getElementsByTagName('details'), false, 'isseasonal')) == 1) ? true : false,
							'name' => $event->getAttribute('name'),
							'startdate' => intval(parseEvent($event, true, true)),
							'specialevent' => intval(parseEvent($event->getElementsByTagName('details'), false, 'specialevent'))
						);
					}
				}

				sendMessage(array(
					'eventlist' => $eventlist, 
					'lastupdatetimestamp' => time()
				));
			break;

		case 'boostedcreature':
			// {"type":"boostedcreature"}
			sendMessage(array(
				//'boostedcreature' => false,
				'raceid' => 219
			));
			break;

		case 'news':
			// {"count":0,"isreturner":true,"offset":0,"showrewardnews":false,"type":"news"}
			sendMessage(array(
				'gamenews' => array(), // element structure?
				'categorycounts' => array(
					'support' => 1,
					'game contents' => 2,
					'useful info' => 3,
					'major updates' => 4,
					'client features' => 5
				),
				'maxeditdate' => 1590979202
			));
			break;

		case "login":
			/* {
				'accountname' => 'username',
				"email":"my@email.com",
				'password' => 'superpass',
				'stayloggedin' => true,
				'token' => '123123', (or not set)
				'type' => 'login',
			} */

			$email = (isset($client->email)) ? sanitize($client->email) : false;
			$username = (isset($client->accountname)) ? sanitize($client->accountname) : false;
			$password = SHA1($client->password);
			$token = (isset($client->token)) ? sanitize($client->token) : false;

			$fields = '`id`, `premium_ends_at`';
			if ($config['twoFactorAuthenticator']) $fields .= ', `secret`';

			$account = false;

			if ($email !== false) {
				$fields .= ', `name`';
				$account = mysql_select_single("SELECT {$fields} FROM `accounts` WHERE `email`='{$email}' AND `password`='{$password}' LIMIT 1;");
				if ($account !== false) {
					$username = $account['name'];
				}
			} elseif ($username !== false) {
				$account = mysql_select_single("SELECT {$fields} FROM `accounts` WHERE `name`='{$username}' AND `password`='{$password}' LIMIT 1;");
			}

			if ($account === false) {
				sendError('Wrong username and/or password.');
			}

			if ($config['twoFactorAuthenticator'] === true && $account['secret'] !== null) {
				if ($token === false) {
					sendError('Submit a valid two-factor authentication token.', 6);
				} else {
					require_once("engine/function/rfc6238.php");
					if (TokenAuth6238::verify($account['secret'], $token) !== true) {
						sendError('Two-factor authentication failed, token is wrong.', 6);
					}
				}
			}

			$players = mysql_select_multi("SELECT `name`, `sex`, `level`, `vocation`, `lookbody`, `looktype`, `lookhead`, `looklegs`, `lookfeet`, `lookaddons`, `deletion` FROM `players` WHERE `account_id`='".$account['id']."';");
			if ($players !== false) {

				$gameserver = $config['gameserver'];
				// Override $config['gameserver'] if server has installed Lua script for loginWebService
				$sql_elements = mysql_select_multi("
					SELECT
						`key`,
			            `value`
					FROM `znote_global_storage`
					WHERE `key` IN('SERVER_NAME', 'IP', 'GAME_PORT')
				");
				if ($sql_elements !== false) {
					foreach ($sql_elements AS $element) {
						switch ($element['key']) {
							case 'SERVER_NAME':
								$gameserver['name'] = $element['value'];
								break;
							case 'IP':
								$gameserver['ip'] = $element['value'];
								break;
							case 'GAME_PORT':
								$gameserver['port'] = (int)$element['value'];
								break;
						}
					}
				}

				$sessionKey = ($email !== false) ? $email."\n".$client->password : $username."\n".$client->password;
				$sessionKey .= (isset($account['secret']) && strlen($account['secret']) > 5) ? "\n".$token : "\n";
				$sessionKey .= "\n".floor(time() / 30);

				$freePremium = (isset($config['freePremium'])) ? $config['freePremium'] : true;
				$response = array(
					'session' => array(
						'fpstracking' => false,
						'optiontracking' => false,
						'isreturner' => true,
						'returnernotification' => false,
						'showrewardnews' => false,
						'tournamentticketpurchasestate' => 0,
						'emailcoderequest' => false,
						'sessionkey' => $sessionKey,
						'lastlogintime' => 0,
						'ispremium' => ($account['premium_ends_at'] > time() || $freePremium) ? true : false,
						'premiumuntil' => $account['premium_ends_at'],
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
								// 0 - open pvp
								// 1 - optional
								// 2 - hardcore
								// 3 - retro open pvp
								// 4 - retro hardcore pvp
								// 5 and higher - (unknown)
								'pvptype' => 0,
								'externaladdressunprotected' => $gameserver['ip'],
								'externaladdressprotected' => $gameserver['ip'],
								'externalportunprotected' => $gameserver['port'],
								'externalportprotected' => $gameserver['port'],
								'istournamentworld' => false,
								'restrictedstore' => false,
								'currenttournamentphase' => 2,
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
						'tutorial' => false,
						'level' => intval($player['level']),
						'vocation' => vocation_id_to_name($player['vocation']),
						'outfitid' => intval($player['looktype']),
						'headcolor' => intval($player['lookhead']),
						'torsocolor' => intval($player['lookbody']),
						'legscolor' => intval($player['looklegs']),
						'detailcolor' => intval($player['lookfeet']),
						'addonsflags' => intval($player['lookaddons']),
						'ishidden' => intval($player['deletion']) === 1,
						'istournamentparticipant' => false,
						'remainingdailytournamentplaytime' => 0
					);
				}

				sendMessage($response);
			} else {
				sendError("Character list is empty.");
			}
			break;

		default:
			sendError("Unsupported type: " . sanitize($client->type));
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
		if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'OTHIRE' || $config['ServerEngine'] == 'TFS_10') $login = user_login($username, $password);
		else if ($config['ServerEngine'] == 'TFS_03') $login = user_login_03($username, $password);
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
				if ($config['ServerEngine'] == 'TFS_10' && $config['twoFactorAuthenticator']) {
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
					$znote_data = user_znote_account_data($login, 'ip');
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

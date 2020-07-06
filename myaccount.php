<?php require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php'; 
#region CANCEL CHARACTER DELETE
$undelete_id = @$_GET['cancel_delete_id'];
if($undelete_id) {
	$undelete_id = (int)$undelete_id;
	$undelete_q1 = mysql_select_single('SELECT `character_name` FROM `znote_deleted_characters` WHERE `done` = 0 AND `id` = ' . $undelete_id . ' AND `original_account_id` = ' . $session_user_id . ' AND NOW() < `time`');
	if($undelete_q1) {
		mysql_delete('DELETE FROM `znote_deleted_characters` WHERE `id` = ' . $undelete_id);
		echo 'Pending delete of ' . $undelete_q1['character_name'] . ' has been successfully canceled.<br/>';
	}
}
#endregion

// Variable used to check if main page should be rendered after handling POST (Change comment page)
$render_page = true;

// Handle GET (verify email)
if (isset($_GET['authenticate']) && $config['mailserver']['myaccount_verify_email']):
	// If we need to process email verification
	if (isset($_GET['u']) && isset($_GET['k'])) {
		// Authenticate user, fetch user id and activation key
		$auid = (isset($_GET['u']) && (int)$_GET['u'] > 0) ? (int)$_GET['u'] : false;
		$akey = (isset($_GET['k']) && (int)$_GET['k'] > 0) ? (int)$_GET['k'] : false;
		if ($auid !== false && $akey !== false) {
			// Find a match
			$user = mysql_select_single("SELECT `id`, `active`, `active_email` FROM `znote_accounts` WHERE `account_id`='{$auid}' AND `activekey`='{$akey}' LIMIT 1;");
			if ($user !== false) {
				$user = (int) $user['id'];
				$active = (int) $user['active'];
				$active_email = (int) $user['active_email'];
				$verify_points = ($active_email == 0 && $config['mailserver']['verify_email_points'] > 0)
					? ", `points` = `points` + {$config['mailserver']['verify_email_points']}" 
					: '';
				// Enable the account to login
				if ($active == 0 || $active_email == 0) {
					$new_activeKey = rand(100000000, 999999999);
					mysql_update("UPDATE `znote_accounts` SET `active`='1', `active_email`='1', `activekey`='{$new_activeKey}' {$verify_points} WHERE `id`= {$user} LIMIT 1;");
				}
				echo '<h1>Congratulations!</h1> <p>Your email has been verified.</p>';
				if ($verify_points !== '') echo "<p>As thanks for having a verified email, you have received <a href='/shop.php'>{$config['mailserver']['verify_email_points']} shop points</a>!</p>";
				$user_znote_data['active_email'] = 1;
				// Todo: Bonus points as thanks for verifying email
			} else {
				echo '<h1>Authentication failed</h1> <p>Either the activation link is wrong, or your account is already activated.</p>';
			}
		} else {
			echo '<h1>Authentication failed</h1> <p>Either the activation link is wrong, or your account is already activated.</p>';
		}
	} else { // We need to send email verification
		$verify_account_id = (int)$session_user_id;
		$user = mysql_select_single("SELECT `id`, `activekey`, `active_email` FROM `znote_accounts` WHERE `account_id`='{$verify_account_id}' LIMIT 1;");
		if ($user !== false) {
			$thisurl = config('site_url') . "myaccount.php";
			$thisurl .= "?authenticate&u=".$verify_account_id."&k=".$user['activekey'];

			$mailer = new Mail($config['mailserver']);

			$title = "Please authenticate your email at {$_SERVER['HTTP_HOST']}.";
			
			$body = "<h1>Please click on the following link to authenticate your account:</h1>";
			$body .= "<p><a href='{$thisurl}'>{$thisurl}</a></p>";
			$body .= "<p>Thank you for verifying your email and enjoy your stay at {$config['mailserver']['fromName']}.</p>";
			$body .= "<hr><p>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p>";
			
			$user_name = ($config['ServerEngine'] !== 'OTHIRE') ? $user_data['name'] : $user_data['id'];
			//echo "<h1>" . $title . "<h1>" . $body;
			$mailer->sendMail($user_data['email'], $title, $body, $user_name);
			?>
			<h1>Email authentication sent</h1>
			<p>We have sent you an email with a verification link to your email address: <strong><?php echo $user_data['email']; ?></strong></p>
			<p>If you can't find the email within 5 minutes, check your <strong>junk/trash inbox (spam filter)</strong> as it may be misplaced there.</p>
			<?php
		} else {
			echo '<h1>Authentication failed</h1> <p>Failed to verify user when trying to send a verification email.</p>';
		}
	}
endif;

// Handle POST
if (!empty($_POST['selected_character'])) {
	if (!empty($_POST['action'])) {
		// Validate token
		if (!Token::isValid($_POST['token'])) {
			exit();
		}
		// Sanitize values
		$action = getValue($_POST['action']);
		$char_name = getValue($_POST['selected_character']);

		// Handle actions
		switch($action) {
			// Change character comment PAGE2 (Success).
			case 'update_comment':
				if (user_character_account_id($char_name) === $session_user_id) {
					user_update_comment(user_character_id($char_name), getValue($_POST['comment']));
					echo 'Successfully updated comment.';
				}
				break;
			// end

			// Hide character
			case 'toggle_hide':
				$hide = (user_character_hide($char_name) == 1 ? 0 : 1);
				if (user_character_account_id($char_name) === $session_user_id) {
					user_character_set_hide(user_character_id($char_name), $hide);
				}
				break;
			// end

			// DELETE character
			case 'delete_character':
				if (user_character_account_id($char_name) === $session_user_id) {
					$charid = user_character_id($char_name);
					if ($charid !== false) {
						if ($config['ServerEngine'] === 'TFS_10') {
							if (!user_is_online_10($charid)) {
								if (guild_leader_gid($charid) === false) user_delete_character_soft($charid);
								else echo 'Character is leader of a guild, you must disband the guild or change leadership before deleting character.';
							} else echo 'Character must be offline first.';
						} else {
							$chr_data = user_character_data($charid, 'online');
							if ($chr_data['online'] != 1) {
								if (guild_leader_gid($charid) === false) user_delete_character_soft($charid);
								else echo 'Character is leader of a guild, you must disband the guild or change leadership before deleting character.';
							} else echo 'Character must be offline first.';
						}
					}
				}
				break;
			// end

			// CHANGE character name
			case 'change_name':
				$oldname = $char_name;
				$newname = isset($_POST['newName']) ? getValue($_POST['newName']) : '';

				$player = false;
				if ($config['ServerEngine'] === 'TFS_10') {
					$player = mysql_select_single("SELECT `id`, `account_id` FROM `players` WHERE `name` = '$oldname'");
					$player['online'] = (user_is_online_10($player['id'])) ? 1 : 0;
				} else $player = mysql_select_single("SELECT `id`, `account_id`, `online` FROM `players` WHERE `name` = '$oldname'");

				// Check if user is online
				if ($player['online'] == 1) {
					$errors[] = 'Character must be offline first.';
				}

				// Check if player has bough ticket
				$accountId = $player['account_id'];
				$order = mysql_select_single("SELECT `id`, `account_id` FROM `znote_shop_orders` WHERE `type`='4' AND `account_id` = '$accountId' LIMIT 1;");
				if ($order === false) {
					$errors[] = 'Did not find any name change tickets, buy them in our <a href="shop.php">shop!</a>';
				}

				// Check if player and account matches
				if ($session_user_id != $accountId || $session_user_id != $order['account_id']) {
					if (empty($errors)) {
						$errors[] = 'Failed to sync your account. :|';
					}
				}

				$newname = validate_name($newname);
				if ($newname === false) {
					$errors[] = 'Your name can not contain more than 2 words.';
				} else {
					if (empty($newname)) {
						$errors[] = 'Please enter a name!';
					} else if (user_character_exist($newname) !== false) {
						$errors[] = 'Sorry, that character name already exist.';
					} else if (!preg_match("/^[a-zA-Z_ ]+$/", $newname)) {
						$errors[] = 'Your name may only contain a-z, A-Z and spaces.';
					} else if (strlen($newname) < $config['minL'] || strlen($newname) > $config['maxL']) {
						$errors[] = 'Your character name must be between ' . $config['minL'] . ' - ' . $config['maxL'] . ' characters long.';
					} else if (!ctype_upper($newname{0})) {
						$errors[] = 'The first letter of a name has to be a capital letter!';
					}

					// name restriction
					$resname = explode(" ", $_POST['newName']);
					foreach($resname as $res) {
						if(in_array(strtolower($res), $config['invalidNameTags'])) {
							$errors[] = 'Your username contains a restricted word.';
						} else if(strlen($res) == 1) {
							$errors[] = 'Too short words in your name.';
						}
					}
				}

				if (!empty($newname) && empty($errors)) {
					echo 'You have successfully changed your character name to ' . $newname . '.';
					mysql_update("UPDATE `players` SET `name`='$newname' WHERE `id`='".$player['id']."' LIMIT 1;");
					mysql_delete("DELETE FROM `znote_shop_orders` WHERE `id`='".$order['id']."' LIMIT 1;");

				} else if (!empty($errors)) {
					echo '<font color="red"><b>';
					echo output_errors($errors);
					echo '</b></font>';
				}

				break;
			// end

			// Change character sex
			case 'change_gender':
				if (user_character_account_id($char_name) === $session_user_id) {
					$char_id = (int)user_character_id($char_name);
					$account_id = user_character_account_id($char_name);

					if ($config['ServerEngine'] == 'TFS_10') {
						$chr_data['online'] = user_is_online_10($char_id) ? 1 : 0;
					} else $chr_data = user_character_data($char_id, 'online');
					if ($chr_data['online'] != 1) {
						// Verify that we are not messing around with data
						if ($account_id != $user_data['id']) die("wtf? Something went wrong, try relogging.");

						// Fetch character tickets
						$tickets = shop_account_gender_tickets($account_id);
						if ($tickets !== false || $config['free_sex_change'] == true) {
							// They are allowed to change gender
							$last = false;
							$infinite = false;
							$tks = 0;
							// Do we have any infinite tickets?
							foreach ($tickets as $ticket) {
								if ($ticket['count'] == 0) $infinite = true;
								else if ($ticket > 0 && $infinite === false) $tks += (int)$ticket['count'];
							}
							if ($infinite === true) $tks = 0;
							$dbid = (int)$tickets[0]['id'];
							// If they dont have unlimited tickets, remove a count from their ticket.
							if ($tickets[0]['count'] > 1) { // Decrease count
								$tks--;
								$tkr = ((int)$tickets[0]['count'] - 1);
								shop_update_row_count($dbid, $tkr);
							} else if ($tickets[0]['count'] == 1) { // Delete record
								shop_delete_row_order($dbid);
								$tks--;
							}

							// Change character gender:
							//
							user_character_change_gender($char_name);
							echo 'You have successfully changed gender on character '. $char_name .'.';
							if ($tks > 0) echo '<br>You have '. $tks .' gender change tickets left.';
							else if ($infinite !== true) echo '<br>You are out of tickets.';
						} else echo 'You don\'t have any character gender tickets, buy them in the <a href="shop.php">SHOP</a>!';
					} else echo 'Your character must be offline.';
				}
				break;
			// end

			// Change character comment PAGE1:
			case 'change_comment':
				$render_page = false; // Regular "myaccount" page should not render
				if (user_character_account_id($char_name) === $session_user_id) {
					$comment_data = user_znote_character_data(user_character_id($char_name), 'comment');
					?>
					<!-- Changing comment MARKUP -->
					<h1>Change comment on:</h1>
					<form action="" method="post">
						<ul>
							<li>
								<input name="action" type="hidden" value="update_comment">
								<input name ="selected_character" type="text" value="<?php echo $char_name; ?>" readonly="readonly">
							</li>
							<li>
								<font class="profile_font" name="profile_font_comment">Comment:</font> <br>
								<textarea name="comment" cols="70" rows="10"><?php echo $comment_data['comment']; ?></textarea>
							</li>
							<?php
								/* Form file */
								Token::create();
							?>
							<li><input type="submit" value="Update Comment"></li>
						</ul>
					</form>
					<?php
				}
				break;
			//end
		}
	}
}

if ($render_page) {
	$char_count = user_character_list_count($session_user_id);
	$pending_delete = user_pending_deletes($session_user_id);
	if ($pending_delete) {
		foreach($pending_delete as $delete) {
			if(new DateTime($delete['time']) > new DateTime())
				echo '<b>CAUTION!</b> Your character with name <b>' . $delete['character_name'] . ' will be deleted on ' . $delete['time'] . '</b>. <a href="myaccount.php?cancel_delete_id=' . $delete['id'] . '">Cancel this operation.</a><br/>';
			else {
				user_delete_character(user_character_id($delete['character_name']));
				mysql_update('UPDATE `znote_deleted_characters` SET `done` = 1 WHERE `id` = '. $delete['id']. '');
				echo '<b>Character ' . $delete['character_name'] . ' has been deleted</b>. This operation was requested by owner of this account.';
				$char_count--;
			}
		}
	}

	?>
	<div id="myaccount">
		<h1>My account</h1>
		<p>Welcome to your account page, <?php if ($config['ServerEngine'] !== 'OTHIRE') echo $user_data['name']; else echo $user_data['id']; ?><br>
			<?php if ($config['ServerEngine'] !== 'OTHIRE') {
				if ($user_data['premdays'] != 0) {
					echo 'You have ' .$user_data['premdays']. ' remaining premium account days.';
				} else {
					echo 'You are free account.';
				}
			} else {
				if ($user_data['premend'] != 0) {
					echo 'Your premium account will last till ';
					echo date("d/m/Y", $user_data['premend']);
				} else {
					echo 'You do not have premium account days.';
				}
			} 
			if ($config['mailserver']['myaccount_verify_email']):
				?><br>Email: <?php echo $user_data['email'];
				if ($user_znote_data['active_email'] == 1) {
					?> (Verified).<?php
				} else {
					?><br><strong>Your email is not verified! <a href="?authenticate">Please verify it</a>.</strong><?php
				}
			endif; ?>
		</p>
		<?php
		if ($config['ServerEngine'] === 'TFS_10' && $config['twoFactorAuthenticator']) {
			$query = mysql_select_single("SELECT `secret` FROM `accounts` WHERE `id`='".(int)$session_user_id."' LIMIT 1;");
			$status = ($query['secret'] === NULL) ? false : true;
			?><p>Account security with Two-factor Authentication: <a href="twofa.php"><?php echo ($status) ? 'Enabled' : 'Disabled'; ?></a></p><?php
		}
		?>
		<h2>Character List: <?php echo $char_count; ?> characters.</h2>
		<?php
		// Echo character list!
		$char_array = user_character_list($user_data['id']);
		// Design and present the list
		if ($char_array) {
			?>
			<table id="myaccountTable" class="table table-striped table-hover">
				<tr class="yellow">
					<th>NAME</th><th>LEVEL</th><th>VOCATION</th><th>TOWN</th><th>LAST LOGIN</th><th>STATUS</th><th>HIDE</th>
				</tr>
				<?php
				$characters = array();
				foreach ($char_array as $value) {
					// characters: [0] = name, [1] = level, [2] = vocation, [3] = town_id, [4] = lastlogin, [5] = online
					echo '<tr>';
					echo '<td><a href="characterprofile.php?name='. $value['name'] .'">'. $value['name'] .'</a></td><td>'. $value['level'] .'</td><td>'. $value['vocation'] .'</td><td>'. $value['town_id'] .'</td><td>'. $value['lastlogin'] .'</td><td>'. $value['online'] .'</td><td>'. hide_char_to_name(user_character_hide($value['name'])) .'</td>';
					echo '</tr>';
					$characters[] = $value['name'];
				}
			?>
			</table>
			<!-- FORMS TO EDIT CHARACTER-->
			<form action="" method="post">
				<table class="table">
					<tr>
						<td>
							<select id="selected_character" name="selected_character" class="form-control">
							<?php
							for ($i = 0; $i < $char_count; $i++) {
								if (user_character_hide($characters[$i]) == 1) {
									echo '<option value="'. $characters[$i] . '">'. $characters[$i] .'</option>';
								} else {
									echo '<option value="'. $characters[$i] . '">'. $characters[$i] .'</option>';
								}
							}
							?>
							</select>
						</td>
						<td>
							<select id="action" name="action" class="form-control" onChange="changedOption(this)">
								<option value="none" selected>Select action</option>
								<option value="toggle_hide">Toggle hide</option>
								<option value="change_comment">Change comment</option>
								<option value="change_gender">Change gender</option>
								<option value="change_name">Change name</option>
								<option value="delete_character" class="needconfirmation">Delete character</option>
							</select>
						</td>
						<td id="submit_form">
							<?php
								/* Form file */
								Token::create();
							?>
							<input id="submit_button" type="submit" value="Submit" class="btn btn-primary btn-block"></input>
						</td>
					</tr>
				</table>
			</form>
			<?php
		} else {
			echo 'You don\'t have any characters. Why don\'t you <a href="createcharacter.php">create one</a>?';
		}
		?>
	</div>
	<script>
		function changedOption(e) {
			// If selection is 'Change name' add a name field in the form
			// Else remove name field if it exists
			if (e.value == 'change_name') {
				var lastCell = document.getElementById('submit_form');
				var x = document.createElement('TD');
				x.id = "new_name";
				x.innerHTML = '<input type="text" name="newName" placeholder="New Name" class="form-control">';
				lastCell.parentNode.insertBefore(x, lastCell);
			} else {
				var child = document.getElementById('new_name');
				if (child) {
					child.parentNode.removeChild(child);
				}
			}
		}
	</script>
	<script src="engine/js/jquery-1.10.2.min.js" type="text/javascript"></script>
	<script>
		$(document).ready(function(){
			$("#submit_button").click(function(e){
				if ($("#action").find(":selected").attr('class') == "needconfirmation") {
					var r = confirm("Do you really want to DELETE character: "+$('#selected_character').find(":selected").text()+"?")
					if (r == false) {
						e.preventDefault();
					}
				}
			});
		});
	</script>
	<?php
}
include 'layout/overall/footer.php';
?>

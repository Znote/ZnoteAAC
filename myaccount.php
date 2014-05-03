<?php require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php'; 
// Change character comment PAGE2 (Success).
if (!empty($_POST['comment']) &&!empty($_POST['charn'])) {
	if (!Token::isValid($_POST['token'])) {
		exit();
	}
	if (user_character_account_id($_POST['charn']) === $session_user_id) {
		user_update_comment(user_character_id($_POST['charn']), $_POST['comment']);
		echo 'Successfully updated comment.';
	}
} else {
// Hide character
if (!empty($_POST['selected_hide'])) {
	if (!Token::isValid($_POST['token'])) {
		exit();
	}
	$hide_array = explode("!", $_POST['selected_hide']);	
	if (user_character_account_id($hide_array[0]) === $session_user_id) {
		user_character_set_hide(user_character_id($hide_array[0]), $hide_array[1]);
	}
}
// end
// DELETE character
if (!empty($_POST['selected_delete'])) {
	if (!Token::isValid($_POST['token'])) {
		exit();
	}
	if (user_character_account_id($_POST['selected_delete']) === $session_user_id) {
		$charid = user_character_id($_POST['selected_delete']);
		if ($charid !== false) {
			if ($config['TFSVersion'] === 'TFS_10') {
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
}
// end

#region CANCEL CHARACTER DELETE
$undelete_id = @$_GET['cancel_delete_id'];
if($undelete_id) {
	$undelete_id = (int)$undelete_id;
	$undelete_q1 = mysql_select_single('SELECT `character_name` FROM `znote_deleted_characters` WHERE `done` = 0 AND `id` = ' . $undelete_id . ' AND `original_account_id` = ' . $session_user_id . ' AND NOW() < `time`');
	if($undelete_q1) {
		mysql_delete('DELETE FROM `znote_deleted_characters` WHERE `id` = ' . $undelete_id);
		echo 'Pending delete of ' . $undelete_q1['character_name'] . ' has been successfully cancelled.<br/>';
	}
}
#endregion

// CHANGE character name
if (!empty($_POST['change_name'])) {
	if (!Token::isValid($_POST['token'])) {
		exit();
	}
	$oldname = getValue($_POST['change_name']);
	$newname = getValue($_POST['newName']);
	

	// Check if user is online
	$player = false;
	if ($config['TFSVersion'] === 'TFS_10') {
		$player = mysql_select_single("SELECT `id`, `account_id` FROM `players` WHERE `name` = '$oldname'");
		$player['online'] = (user_is_online_10($player['id'])) ? 1 : 0;
	} else $player = mysql_select_single("SELECT `id`, `account_id`, `online` FROM `players` WHERE `name` = '$oldname'");
	
	// Check if player has bough ticket
	$order = mysql_select_single("SELECT `id`, `account_id` FROM `znote_shop_orders` WHERE `type`='4' LIMIT 1;");
	if ($order !== false) {
		// Check if player and account matches
		if ($session_user_id == $player['account_id'] && $session_user_id == $order['account_id']) {
			// Check if new name is not occupied
			$exist = mysql_select_single("SELECT `id` FROM `players` WHERE `name`='$newname';");
			if (!$exist) {
				// Check if new name follow rules
				$newname = validate_name($newname);
				if ($newname !== false) {
					$error = false;
					// name restriction
					$resname = explode(" ", $_POST['name']);
					foreach($resname as $res) {
						if(in_array(strtolower($res), $config['invalidNameTags'])) {
							$error = true;
						}
						else if(strlen($res) == 1) {
							$error = true;
						}
					}
					if ($error === false) {
						// Change the name!
						mysql_update("UPDATE `players` SET `name`='$newname' WHERE `id`='".$player['id']."' LIMIT 1;");
						mysql_delete("DELETE FROM `znote_shop_orders` WHERE `id`='".$order['id']."' LIMIT 1;");
					} else echo "Illegal name.";
				} else echo "Name validation failed, use another name.";
			} else echo "The character name you wish to change to already exist.";
		} else echo "Failed to sync your account. :|";
	} else echo "Did not find any name change tickets, but them in our <a href='shop.php'>shop!</a>";
}
// end
// Change character sex
if (!empty($_POST['change_gender'])) {
	if (!Token::isValid($_POST['token'])) {
		exit();
	}
	if (user_character_account_id($_POST['change_gender']) === $session_user_id) {
		$char_name = sanitize($_POST['change_gender']);
		$char_id = (int)user_character_id($char_name);
		$account_id = user_character_account_id($char_name);
		
		if ($config['TFSVersion'] == 'TFS_10') {
			$chr_data = user_is_online_10($user_id);
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
}
// end
// Change character comment PAGE1:
if (!empty($_POST['selected_comment'])) {
	if (!Token::isValid($_POST['token'])) {
		exit();
	}
	if (user_character_account_id($_POST['selected_comment']) === $session_user_id) {
		$comment_data = user_znote_character_data(user_character_id($_POST['selected_comment']), 'comment');
		?>
		<!-- Changing comment MARKUP -->
		<h1>Change comment on:</h1>
		<form action="" method="post">
			<ul>
				<li>
					<input name ="charn" type="text" value="<?php echo $_POST['selected_comment']; ?>" readonly="readonly">
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
} else {
	// end
	$char_count = user_character_list_count($session_user_id);
	$pending_delete = user_pending_deletes($session_user_id);
	if($pending_delete)
		foreach($pending_delete as $delete) {
			if(new DateTime($delete['time']) > new DateTime())
				echo '<b>CAUTION!</b> Your character with name <b>' . $delete['character_name'] . ' will be deleted on ' . $delete['time'] . '</b>. <a href="myaccount.php?cancel_delete_id=' . $delete['id'] . '">Cancel this operation.</a><br/>';
			else {
				user_delete_character(user_character_id($delete['character_name']));
				mysql_update('UPDATE `znote_deleted_characters` SET `done` = 1');
				echo '<b>Character ' . $delete['character_name'] . ' has been deleted</b>. This operation was requested by owner of this account.';
			}
		}
	?>
	<div id="myaccount">
		<h1>My account</h1>
		<p>Welcome to your account page, <?php echo $user_data['name']; ?></p>

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
			<!-- FORMS TO HIDE CHARACTER-->
			<form action="" method="post">
				<ul>
					<li>
						Character hide:<br>
						<select name="selected_hide" multiple="multiple">
						<?php
						for ($i = 0; $i < $char_count; $i++) {
							if (user_character_hide($characters[$i]) == 1) {
								echo '<option value="'. $characters[$i] .'!0">'. $characters[$i] .'</option>'; 	
							} else {
								echo '<option value="'. $characters[$i] .'!1">'. $characters[$i] .'</option>'; 	
							}
						}
						?>
						</select>
						<?php
							/* Form file */
							Token::create();
						?>
						<input type="submit" value="Toggle hide" class="btn btn-info">
					</li>
				</ul>
			</form>
			<!-- FORMS TO CHANGE CHARACTER COMMENT-->
			<form action="" method="post">
				<ul>
					<li>
						Character comment:<br>
						<select name="selected_comment" multiple="multiple">
						<?php
						for ($i = 0; $i < $char_count; $i++) {
							echo '<option value="'. $characters[$i] .'">'. $characters[$i] .'</option>'; 	
						}
						?>
						</select>
						<?php
							/* Form file */
							Token::create();
						?>
						<input type="submit" value="Change comment" class="btn btn-info">
					</li>
				</ul>
			</form>
			<!-- FORMS TO CHANGE CHARACTER GENDER-->
			<form action="" method="post">
				<ul>
					<li>
						Change character gender:<br>
						<select name="change_gender" multiple="multiple">
						<?php
						for ($i = 0; $i < $char_count; $i++) {
							echo '<option value="'. $characters[$i] .'">'. $characters[$i] .'</option>'; 	
						}
						?>
						</select>
						<?php
							/* Form file */
							Token::create();
						?>
						<input type="submit" value="Change gender" class="btn btn-info">
					</li>
				</ul>
			</form>
			<!-- FORMS TO CHANGE CHARACTER NAME-->
			<form action="" method="post">
				<ul>
					<li>
						Change character name:<br>
						<select name="change_name" multiple="multiple">
						<?php
						for ($i = 0; $i < $char_count; $i++) {
							echo '<option value="'. $characters[$i] .'">'. $characters[$i] .'</option>'; 	
						}
						?>
						</select>
						<input type="text" name="newName" placeholder="New Name">
						<?php
							/* Form file */
							Token::create();
						?>
						<input type="submit" value="Change name" class="btn btn-info">
					</li>
				</ul>
			</form>
			<!-- FORMS TO DELETE CHARACTER-->
			<form action="" method="post">
				<ul>
					<li>
						Delete character:<br>
						<select id="selected_delete" name="selected_delete" multiple="multiple">
						<?php
						for ($i = 0; $i < $char_count; $i++) {
							echo '<option value="'. $characters[$i] .'">'. $characters[$i] .'</option>'; 	
						}
						?>
						</select>
						<?php
							/* Form file */
							Token::create();
						?>
						<input type="submit" value="Delete Character" class="btn btn-danger needconfirmation">
					</li>
				</ul>
			</form>
			<script src="engine/js/jquery-1.10.2.min.js" type="text/javascript"></script>
			<script>
			    $(document).ready(function(){
			        $(".needconfirmation").each(function(e){
			            $(this).click(function(e){
			                var itemname = $(this).attr("data-item-name");
							var r = confirm("Do you really want to DELETE character: "+$('#selected_delete').find(":selected").text()+"?")
							if(r == false){
								e.preventDefault();
							}			
			            });
			        });
			    });
			</script>
			<?php 
			} else {
				echo 'You don\'t have any characters. Why don\'t you <a href="createcharacter.php">create one</a>?';
			}
			//Done.
		}
		?>
	</div>
	<?php
}
include 'layout/overall/footer.php'; ?>
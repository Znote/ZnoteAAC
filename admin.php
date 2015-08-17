<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; 
protect_page();
admin_only($user_data);
// Encryption (if select field has $key 0, it will return false, so add $enc + $key will return 100, subtract and you get 0, not false). 
$enc = 100;
// Don't bother to think about cross site scripting here, since they can't access the page unless they are admin anyway.

// start
if (empty($_POST) === false) {
	// BAN system!
	if (!empty($_POST['ban_char']) && !empty($_POST['ban_type']) && !empty($_POST['ban_action']) && !empty($_POST['ban_reason']) && !empty($_POST['ban_time']) && !empty($_POST['ban_comment'])) {
		if (user_character_exist($_POST['ban_char'])) {
			
			// Decrypt and store values
			$charname = $_POST['ban_char'];
			$typeid = (int)$_POST['ban_type'] - $enc;
			$actionid = (int)$_POST['ban_action'] - $enc;
			$reasonid = (int)$_POST['ban_type'] - $enc;
			$time = (int)$_POST['ban_time'] - $enc;
			$comment = $_POST['ban_comment'];
			//var_dump($charname, $typeid, $actionid, $reasonid, $time, $comment);
			
			if (set_rule_violation($charname, $typeid, $actionid, $reasonid, $time, $comment)) {
				$errors[] = 'Violation entry has been set for '. $charname .'.';
			} else {
				$errors[] = 'Website character name: '. $config['website_char'] .' does not exist. Create this character name or configure another name in config.php';
				$errors[] = 'Website failed to recognize a character it can represent while inserting a rule violation.';
			}
			
		} else {
			$errors[] = 'Character '. getValue($_POST['ban_char']) .' does not exist.';
		}
	}
	
	
	// delete character:
	if (empty($_POST['del_name']) === false) {
		if (user_character_exist($_POST['del_name'])) {
			user_delete_character(user_character_id($_POST['del_name']));
			$errors[] = 'Character '. getValue($_POST['del_name']) .' permanently deleted.';
		} else {
			$errors[] = 'Character '. getValue($_POST['del_name']) .' does not exist.';
		}
	}
	
	// Reset password for char name
	if (empty($_POST['reset_pass']) === false && empty($_POST['new_pass']) === false) {
		// reset_pass = character name
		if (user_character_exist($_POST['reset_pass'])) {
			$acc_id = user_character_account_id($_POST['reset_pass']);
			
			if ($acc_id != $session_user_id) {
				if ($config['TFSVersion'] == 'TFS_02' || $config['TFSVersion'] == 'TFS_10') {
					user_change_password($acc_id, $_POST['new_pass']);
				} else if ($config['TFSVersion'] == 'TFS_03') {
					user_change_password03($acc_id, $_POST['new_pass']);
				}
				$errors[] = 'The password to the account of character name: '. getValue($_POST['reset_pass']) .' has been set to: '. getValue($_POST['new_pass']) .'.';
			} else {
				header('Location: changepassword.php');
				exit();
			}
		}
	}

	// Give points to character
	if (empty($_POST['points_char']) === false && empty($_POST['points_value']) === false) {
		$char = sanitize($_POST['points_char']);
		$points = (int)$_POST['points_value'];
		data_dump($_POST, false, "post data");
		$account = mysql_select_single("SELECT `account_id` FROM `players` WHERE `name`='$char' LIMIT 1;");
		data_dump($account, false, "fetching account id from players table");
		$znote_account = mysql_select_single("SELECT `id`, `points` FROM `znote_accounts` WHERE `account_id`='". $account['account_id'] ."';");
		data_dump($znote_account, false, "Fetching existing points from znote_accounts");

		data_dump(
			array(
				'Old:' => $znote_account['points'], 
				'New:' => $points, 
				'Total:' => ($znote_account['points'] + $points)
				),
			false,
			"Points calculation:");
		$points += $znote_account['points'];
		mysql_update("UPDATE `znote_accounts` SET `points`='$points' WHERE `account_id`='". $account['account_id'] ."';");
	}
	
	// Set character position
	if (empty($_POST['position_name']) === false && empty($_POST['position_type']) === false) {
		if (user_character_exist($_POST['position_name'])) {
			if (array_key_exists($_POST['position_type'], $config['ingame_positions'])) {
				if ($config['TFSVersion'] == 'TFS_02' || $config['TFSVersion'] == 'TFS_10') {
					set_ingame_position($_POST['position_name'], $_POST['position_type']);
				} else if ($config['TFSVersion'] == 'TFS_03') {
					set_ingame_position03($_POST['position_name'], $_POST['position_type']);
				}
				$pos = 'Undefined';
				foreach ($config['ingame_positions'] as $key=>$value) {
					if ($key == $_POST['position_type']) {
						$pos = $value;
					}
				}
				$errors[] = 'Character '. getValue($_POST['position_name']) .' recieved the ingame position: '. $pos .'.';
			}
		} else {
			$errors[] = 'Character '. getValue($_POST['position_name']) .' does not exist.';
		}
	}
	
// If empty post
}

// Display whatever output we figure out to add
if (empty($errors) === false){
	echo '<font color="red"><b>';
	echo output_errors($errors);
	echo '</b></font>';
}
// end
?>
<h1>Admin Page.</h1>
<p>
<?php
$basic = user_znote_data('version', 'installed', 'cached');
if ($basic['version'] !== $version) {
	mysql_update("UPDATE `znote` SET `version`='$version';");
	$basic = user_znote_data('version', 'installed', 'cached');
}
echo "Running Znote AAC Version: ". $basic['version'] .".<br>";
echo "Last cached on: ". getClock($basic['cached'], true) .".<br>";
?>
</p>
<ul>
	<li>
		<b>Permanently Delete/erase character from database:</b> 
		<form type="submit" action="" method="post">
			<input type="text" name="del_name" placeholder="Character name...">
		</form>
	</li>
	<li>
		<b>Ban/violate :3 character and/or his account:</b>
		<form action="" method="post">
			<table style="background-color:lightblue;">
				<!-- row 1 -->
				<tr>
					<td>
						<input type="text" name="ban_char" placeholder="Character name...">
					</td>
				</tr>
				
				<!-- row 2 -->
				<tr>
					<td>
						<select name="ban_type">
							<?php
							foreach ($config['ban_type'] as $key=>$value) {
								echo "<option value=\"". ($enc + $key) ."\">". $value ."</option>";
							}
							?>
						</select>
						<select name="ban_action">
							<?php
							foreach ($config['ban_action'] as $key=>$value) {
								echo "<option value=\"". ($enc + $key) ."\">". $value ."</option>";
							}
							?>
						</select>
						<select name="ban_time">
							<?php
							foreach ($config['ban_time'] as $key=>$value) {
								echo "<option value=\"". ($enc + $key) ."\">". $value ."</option>";
							}
							?>
						</select>
					</td>
				</tr>
				
				<!-- row 3 -->
				<tr>
					<td>
						Ban reason: 
						<select name="ban_reason">
							<?php
							foreach ($config['ban_reason'] as $key=>$value) {
								echo "<option value=\"". ($enc + $key) ."\">". $value ."</option>";
							}
							?>
						</select>
					</td>
				</tr>
				
				<!-- row 4 -->
				<tr>
					<td>
						Violation comment: (max 60 cols).
						<input type="text" name="ban_comment" maxlength="60" placeholder="Ban for botting rotworms.">
						<input type="submit" value="Set Violation">
					</td>
				</tr>
			</table>
		</form>
	</li>
	<li>
		<b>Reset password to the account of character name:</b>
		<form action="" method="post">
			<input type="text" name="reset_pass" placeholder="Character name">
			<input type="text" name="new_pass" placeholder="New password">
			<input type="submit" value="Change Password">
		</form>
	</li>
	<li>
		<b>Set character name to position:</b>
		<?php
		if ($config['TFSVersion'] == 'TFS_03' && count($config['ingame_positions']) == 5) {
			?>
			<font color="red">ERROR: You forgot to add (Senior Tutor) rank in config.php!</font>
			<?php
		}
		?>
		<form action="" method="post">
			<input type="text" name="position_name" placeholder="Character name">
			<select name="position_type">
				<?php
				foreach ($config['ingame_positions'] as $key=>$value) {
					echo "<option value=\"". $key ."\">". $value ."</option>";
				}
				?>
			</select>
			<input type="submit" value="Set Position">
		</form>
	</li>
	<li>
		<b>Give shop points to character:</b>
		<form action="" method="post">
			<input type="text" name="points_char" placeholder="Character name">
			<input type="text" name="points_value" placeholder="Points">
			<input type="submit" value="Give Points">
		</form>
	</li>
</ul>
<div id="twitter"><?php include 'twtrNews.php'; ?></div>

<?php include 'layout/overall/footer.php'; ?>
<?php require_once 'engine/init.php';
logged_in_redirect();
include 'layout/overall/header.php';
if ($config['mailserver']['accountRecovery']) {
	// Fetch, sanitize and assign POST and GET variables.
	$mode = (isset($_GET['mode']) && !empty($_GET['mode'])) ? getValue($_GET['mode']) : false;
	$email = (isset($_POST['email']) && !empty($_POST['email'])) ? getValue($_POST['email']) : false;
	$character = (isset($_POST['character']) && !empty($_POST['character'])) ? getValue($_POST['character']) : false;
	$password = (isset($_POST['password']) && !empty($_POST['password'])) ? getValue($_POST['password']) : false;
	$username = (isset($_POST['username']) && !empty($_POST['username'])) ? getValue($_POST['username']) : false;
	//data_dump($_GET, $_POST, "Posted data.");

	if (!empty($_POST)) {
		$status = true;
		if ($config['use_captcha']) {
			include_once 'captcha/securimage.php';
			$securimage = new Securimage();
			if ($securimage->check($_POST['captcha_code']) == false) {
			  $status = false;
			}
		}
		if ($status) {
			if (!$username) {
				// Recover username
				$salt = '';
				if ($config['TFSVersion'] != 'TFS_03') {
					// TFS 0.2 and 1.0
					$password = sha1($password);
				} else {
					// TFS 0.3/4
					if (config('salt') === true) {
						$saltdata = mysql_select_single("SELECT `salt` FROM `accounts` WHERE `email`='$email' LIMIT 1;");
						if ($saltdata !== false) $salt .= $saltdata['salt'];
					}
					$password = sha1($salt.$password);
				}
				$user = mysql_select_single("SELECT `p`.`id` AS `player_id`, `a`.`name` FROM `players` `p` INNER JOIN `accounts` `a` ON `p`.`account_id` = `a`.`id` WHERE `p`.`name` = '$character' AND `a`.`email` = '$email' AND `a`.`password` = '$password' LIMIT 1;");
				if ($user !== false) {
					// Found user

					$mailer = new Mail($config['mailserver']);
					$title = "$_SERVER[HTTP_HOST]: Your username";
					$body = "<h1>Account Recovery</h1>";
					$body .= "<p>Your username is: <b>$user[name]</b><br>";
					$body .= "Enjoy your stay at ".$config['mailserver']['fromName'].". <br>";
					$body .= "<hr>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p>";
					$mailer->sendMail($email, $title, $body, $user['name']);

					?>
					<h1>Account Found!</h1>
					<p>We have sent your username to <b><?php echo $email; ?></b>.</p>
					<p>If you can't find the email within 5 minutes, check your junk/trash inbox as it may be mislocated there.</p>
					<?php
				} else {
					// Wrong submitted info
					?>
					<h1>Account recovery failed!</h1>
					<p>Submitted data is wrong.</p>
					<?php
				}

			} else {
				// Recover password
				$newpass = rand(100000000, 999999999);
				$salt = '';
				if ($config['TFSVersion'] != 'TFS_03') {
					// TFS 0.2 and 1.0
					$password = sha1($newpass);
				} else {
					// TFS 0.3/4
					if (config('salt') === true) {
						$saltdata = mysql_select_single("SELECT `salt` FROM `accounts` WHERE `email`='$email' LIMIT 1;");
						if ($saltdata !== false) $salt .= $saltdata['salt'];
					}
					$password = sha1($salt.$newpass);
				}
				$user = mysql_select_single("SELECT `p`.`id` AS `player_id`, `a`.`name`, `a`.`id` AS `account_id` FROM `players` `p` INNER JOIN `accounts` `a` ON `p`.`account_id` = `a`.`id` WHERE `p`.`name` = '$character' AND `a`.`email` = '$email' AND `a`.`name` = '$username' LIMIT 1;");
				if ($user !== false) {
					// Found user
					// Give him the new password
					mysql_update("UPDATE `accounts` SET `password`='$password' WHERE `id`='".$user['account_id']."' LIMIT 1;");
					// Send him a mail with the new password
					$mailer = new Mail($config['mailserver']);
					$title = "$_SERVER[HTTP_HOST]: Your new password";
					$body = "<h1>Account Recovery</h1>";
					$body .= "<p>Your new password is: <b>$newpass</b><br>";
					$body .= "We recommend you to login and change it before you continue playing. <br>";
					$body .= "Enjoy your stay at ".$config['mailserver']['fromName'].". <br>";
					$body .= "<hr>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p>";
					$mailer->sendMail($email, $title, $body, $user['name']);
					?>
					<h1>Account Found!</h1>
					<p>We have sent your new password to <b><?php echo $email; ?></b>.</p>
					<p>If you can't find the email within 5 minutes, check your junk/trash inbox as it may be mislocated there.</p>
					<?php
				} else {
					// Wrong submitted info
					?>
					<h1>Account recovery failed!</h1>
					<p>Submitted data is wrong.</p>
					<?php
				}
			}
		} else echo "Captcha image verification was submitted wrong.";
	} else {
		?>
		<h1>Account Recovery</h1>
		<!-- HTML code -->
		<?php
		if (in_array($mode, array('username', 'password'))) {
			?>
			<form action="" method="POST">
				<label for="email">Email:</label><input type="text" name="email" placeholder="name@mail.com"><br>
				<label for="Character">Character: </label><input type="text" name="character"><br>
				<?php
				if ($mode === 'password') echo '<label for="username">Username:</label> <input type="text" name="username"><br>';
				else echo '<label for="password">Password:</label> <input type="password" name="password"><br>';
				if ($config['use_captcha']) {
					?>
						<b>Write the image symbols in the text field to verify that you are a human:</b>
						<img id="captcha" src="captcha/securimage_show.php" alt="CAPTCHA Image" /><br>
						<input type="text" name="captcha_code" size="10" maxlength="6" />
						<a href="#" onclick="document.getElementById('captcha').src = 'captcha/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a><br><br>
					<?php
				}
				?>
				<input type="submit" value="Recover Account">
			</form>
			<?php
		} else {
			?>
			<p>Do you wish to recover your <a href="?mode=username">username</a> or <a href="?mode=password">password</a>?</p>
			<?php
		}
	}
} else {
	?>
	<h1>System Disabled</h1>
	<p>The admin have disabled automatic account recovery.</p>
	<?php
}
include 'layout/overall/footer.php'; ?>
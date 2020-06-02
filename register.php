<?php
require_once 'engine/init.php';
logged_in_redirect();
include 'layout/overall/header.php';
require_once('config.countries.php');

if (empty($_POST) === false) {
	// $_POST['']
	$required_fields = array('username', 'password', 'password_again', 'email', 'selected');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}

	// check errors (= user exist, pass long enough
	if (empty($errors) === true) {
		/* Token used for cross site scripting security */
		if (!Token::isValid($_POST['token'])) {
			$errors[] = 'Token is invalid.';
		}

		if ($config['use_captcha']) {
			if(!verifyGoogleReCaptcha($_POST['g-recaptcha-response'])) {
				$errors[] = "Please confirm that you're not a robot.";
			}
		}

		if (user_exist($_POST['username']) === true) {
			$errors[] = 'Sorry, that username already exist.';
		}

		// Don't allow "default admin names in config.php" access to register.
		$isNoob = in_array(strtolower($_POST['username']), $config['page_admin_access']) ? true : false;
		if ($isNoob) {
			$errors[] = 'This account name is blocked for registration.';
		}
		if ($config['ServerEngine'] !== 'OTHIRE' && $config['client'] >= 830) {
			if (preg_match("/^[a-zA-Z0-9]+$/", $_POST['username']) == false) {
				$errors[] = 'Your account name can only contain characters a-z, A-Z and 0-9.';
			}
		} else {
			if (preg_match("/^[0-9]+$/", $_POST['username']) == false) {
				$errors[] = 'Your account can only contain numbers 0-9.';
			}
			if ((int)$_POST['username'] < 100000 || (int)$_POST['username'] > 999999999) {
				$errors[] = 'Your account number must be a value between 6-8 numbers long.';
			}
		}
		// name restriction
		$resname = explode(" ", $_POST['username']);
		foreach($resname as $res) {
			if(in_array(strtolower($res), $config['invalidNameTags'])) {
				$errors[] = 'Your username contains a restricted word.';
			}
			else if(strlen($res) == 1) {
				$errors[] = 'Too short words in your name.';
			}
		}
		if (strlen($_POST['username']) > 32) {
			$errors[] = 'Your account name must be less than 33 characters.';
		}
		// end name restriction
		if (strlen($_POST['password']) < 6) {
			$errors[] = 'Your password must be at least 6 characters.';
		}
		if (strlen($_POST['password']) > 100) {
			$errors[] = 'Your password must be less than 100 characters.';
		}
		if ($_POST['password'] !== $_POST['password_again']) {
			$errors[] = 'Your passwords do not match.';
		}
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
			$errors[] = 'A valid email address is required.';
		}
		if (user_email_exist($_POST['email']) === true) {
			$errors[] = 'That email address is already in use.';
		}
		if ($_POST['selected'] != 1) {
			$errors[] = 'You are only allowed to have an account if you accept the rules.';
		}
		if (validate_ip(getIP()) === false && $config['validate_IP'] === true) {
			$errors[] = 'Failed to recognize your IP address. (Not a valid IPv4 address).';
		}
			if (strlen($_POST['flag']) < 1) {
						$errors[] = 'Please choose country.';
				}
	}
}

?>
<h1>Register Account</h1>
<?php
if (isset($_GET['success']) && empty($_GET['success'])) {
	if ($config['mailserver']['register']) {
		?>
		<h1>Email authentication required</h1>
		<p>We have sent you an email with an activation link to your submitted email address.</p>
		<p>If you can't find the email within 5 minutes, check your <strong>junk/trash inbox (spam filter)</strong> as it may be mislocated there.</p>
		<?php
	} else echo 'Congratulations! Your account has been created. You may now login to create a character.';
} elseif (isset($_GET['authenticate']) && empty($_GET['authenticate'])) {
	// Authenticate user, fetch user id and activation key
	$auid = (isset($_GET['u']) && (int)$_GET['u'] > 0) ? (int)$_GET['u'] : false;
	$akey = (isset($_GET['k']) && (int)$_GET['k'] > 0) ? (int)$_GET['k'] : false;
	// Find a match
	$user = mysql_select_single("SELECT `id`, `active`, `active_email` FROM `znote_accounts` WHERE `account_id`='$auid' AND `activekey`='$akey' LIMIT 1;");
	if ($user !== false) {
		$user = (int) $user['id'];
		$active = (int) $user['active'];
		$active_email = (int) $user['active_email'];
		// Enable the account to login
		if ($active == 0 || $active_email == 0) {
			mysql_update("UPDATE `znote_accounts` SET `active`='1', `active_email`='1' WHERE `id`= $user LIMIT 1;");
		}
		echo '<h1>Congratulations!</h1> <p>Your account has been created. You may now login to create a character.</p>';
	} else {
		echo '<h1>Authentication failed</h1> <p>Either the activation link is wrong, or your account is already activated.</p>';
	}
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		if ($config['log_ip']) {
			znote_visitor_insert_detailed_data(1);
		}

		//Register
		if ($config['ServerEngine'] !== 'OTHIRE') {
			$register_data = array(
				'name'		=>	$_POST['username'],
				'password'	=>	$_POST['password'],
				'email'		=>	$_POST['email'],
				'created'	=>	time(),
				'ip'		=>	getIPLong(),
				'flag'		=> 	$_POST['flag']
			);
		} else {
			$register_data = array(
				'id'		=>	$_POST['username'],
				'password'	=>	$_POST['password'],
				'email'		=>	$_POST['email'],
				'created'	=>	time(),
				'ip'		=>	getIPLong(),
				'flag'		=> 	$_POST['flag']
			);			
		}	

		user_create_account($register_data, $config['mailserver']);
		if (!$config['mailserver']['debug']) header('Location: register.php?success');
		exit();
		//End register

	} else if (empty($errors) === false){
		echo '<font color="red"><b>';
		echo output_errors($errors);
		echo '</b></font>';
	}
?>
	<form action="" method="post">
		<ul>
			<li>
				Account Name:<br>
				<input type="text" name="username">
			</li>
			<li>
				Password:<br>
				<input type="password" name="password">
			</li>
			<li>
				Password again:<br>
				<input type="password" name="password_again">
			</li>
			<li>
				Email:<br>
				<input type="text" name="email">
			</li>
			<li>
				Country:<br>
				<select name="flag">
					<option value="">(Please choose)</option>
					<?php
					foreach(array('pl', 'se', 'br', 'us', 'gb', ) as $c)
						echo '<option value="' . $c . '">' . $config['countries'][$c] . '</option>';

						echo '<option value="">----------</option>';
						foreach($config['countries'] as $code => $c)
							echo '<option value="' . $code . '">' . $c . '</option>';
					?>
				</select>
			</li>
			<?php
			if ($config['use_captcha']) {
				?>
				<li>
					 <div class="g-recaptcha" data-sitekey="<?php echo $config['captcha_site_key']; ?>"></div>
				</li>
				<?php
			}
			?>
			<li>
				<h2>Server Rules</h2>
				<p>The golden rule: Have fun.</p>
				<p>If you get pwn3d, don't hate the game.</p>
				<p>No <a href='https://en.wikipedia.org/wiki/Cheating_in_video_games' target="_blank">cheating</a> allowed.</p>
				<p>No <a href='https://en.wikipedia.org/wiki/Video_game_bot' target="_blank">botting</a> allowed.</p>
				<p>The staff can delete, ban, do whatever they want with your account and your <br>
					submitted information. (Including exposing and logging your IP).</p>
			</li>
			<li>
				Do you agree to follow the server rules?<br>
				<select name="selected">
				  <option value="0">Umh...</option>
				  <option value="1">Yes.</option>
				  <option value="2">No.</option>
				</select>
			</li>
			<?php
				/* Form file */
				Token::create();
			?>
			<li>
				<input type="submit" value="Create Account">
			</li>
		</ul>
	</form>
<?php
}
include 'layout/overall/footer.php';
?>

<?php
require_once 'engine/init.php';
logged_in_redirect();
include 'layout/overall/header.php';

if (empty($_POST) === false) {
	if ($config['log_ip']) {
		znote_visitor_insert_detailed_data(5);
	}
	$username = $_POST['username'];
	$password = $_POST['password'];
	//data_dump($_POST, false, "POST");
	if (empty($username) || empty($password)) {
		$errors[] = 'You need to enter a username and password.';
	} else if (strlen($username) > 32 || strlen($password) > 64) {
			$errors[] = 'Username or password is too long.';
	} else if (user_exist($username) === false) {
		$errors[] = 'Failed to authorize your account, are the details correct, have you <a href=\'register.php\'>register</a>ed?';
	} /*else if (user_activated($username) === false) {
		$errors[] = 'You havent activated your account! Please check your email. <br>Note it may appear in your junk/spam box.';
	} */else if (!Token::isValid($_POST['token'])) {
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
} else {
	header('Location: index.php');
}

if (empty($errors) === false) {
?>
	<h2>We tried to log you in, but...</h2>
<?php
	echo output_errors($errors);
}
include 'layout/overall/footer.php';
?>
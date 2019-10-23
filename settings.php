<?php
require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';
require_once('config.countries.php');

if (empty($_POST) === false) {
	// $_POST['']
	/* Token used for cross site scripting security */
	if (!Token::isValid($_POST['token'])) {
		$errors[] = 'Token is invalid.';
	}
	$required_fields = array('new_email', 'new_flag');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}

	if (empty($errors) === true) {
		if (filter_var($_POST['new_email'], FILTER_VALIDATE_EMAIL) === false) {
			$errors[] = 'A valid email address is required.';
		} else if (user_email_exist($_POST['new_email']) === true && $user_data['email'] !== $_POST['new_email']) {
			$errors[] = 'That email address is already in use.';
		}
	}
}
?>
<h1>Settings</h1>

<?php
if (isset($_GET['success']) === true && empty($_GET['success']) === true) {
	echo 'Your settings have been updated.';
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		$update_data = array(
			'email' => $_POST['new_email']
		);

		$update_znote_data = array(
			'flag' => getValue($_POST['new_flag']),
			'active_email' => '0'
		);

		// If he had previously verified his email address, remove the previously aquired bonus points
		if ($user_znote_data['active_email'] > 0) {
			$update_znote_data['points'] = $user_znote_data['points'] - $config['mailserver']['verify_email_points'];
		}
		
		user_update_account($update_data);
		user_update_znote_account($update_znote_data);
		header('Location: settings.php?success');
		exit();

	} else if (empty($errors) === false) {
		echo output_errors($errors);
	}
	?>

	<form action="" method="post">
		<ul>
			<li>
				email:<br>
				<input type="text" name="new_email" value="<?php echo $user_data['email']; ?>">
			</li>
			<li>
				Country:<br>
				<select name="new_flag" id="flag_select">
					<option value="">(Please choose)</option>
					<?php
					foreach(array('pl', 'se', 'br', 'us', 'gb', ) as $c)
						echo '<option value="' . $c . '">' . $config['countries'][$c] . '</option>';

						echo '<option value="">----------</option>';
						foreach($config['countries'] as $code => $c)
							echo '<option value="' . $code . '"' . (isset($user_znote_data['flag']) && $user_znote_data['flag'] == $code ? ' selected' : '') . '>' . $c . '</option>';
					?>
				</select>
			</li>
			<?php
				/* Form file */
				Token::create();
			?>
			<li>
				<input type="submit" value="Update settings">
			</li>
		</ul>
	</form>
<?php
}
include 'layout/overall/footer.php';
?>

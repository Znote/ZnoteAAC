<?php require_once 'engine/init.php';
logged_in_redirect();
include 'layout/overall/header.php'; ?>

<h1>Account Recovery</h1>
<!-- Success markup -->
<?php
$mode_allowed = array('username', 'password');
if (isset($_GET['mode']) === true && in_array($_GET['mode'], $mode_allowed) === true) {
	if (isset($_POST['email']) === true && empty($_POST['email']) === false) {
		if (user_email_exist($_POST['email']) === true) {
		znote_visitor_insert_detailed_data(5);
		$mail = $_POST['email'];
		$acc_id = user_id_from_email($mail);
			if (isset($_POST['character']) === true && empty($_POST['character']) === false) {
				if (user_character_exist($_POST['character']) === true) {
					// EDOM
					if ($_GET['mode'] === 'username') { // Recover password, edom == username
						// edom == password
						if (isset($_POST['edom']) === true && empty($_POST['edom']) === false) {
							if (user_password_match($_POST['edom'], $acc_id) === true) {
								// User exist, email exist, character exist. Lets start the recovery function
								user_recover($_GET['mode'], $_POST['edom'], $_POST['email'], $_POST['character'], ip2long(getIP()));
								//echo 'password';
							} else {
								echo 'That password is incorrect.';
							}
						} else { echo 'You forgot to write password.'; }
						//echo 'username';
					} else {
						if (isset($_POST['edom']) === true && empty($_POST['edom']) === false) {
							if (user_exist($_POST['edom']) === true) {
								// User exist, email exist, character exist. Lets start the recovery function
								user_recover($_GET['mode'], $_POST['edom'], $_POST['email'], $_POST['character'], ip2long(getIP()));
								//echo 'password';
							} else { echo 'That username ['. $_POST['edom'] .'] is incorrect.'; }
						} else { echo 'You forgot to write username.'; }
					}
					// end EDOM
				} else { echo 'That character name does not exist.'; }
			} else { echo 'You need to type in a character name from your account.'; }
			
		} else {
			echo 'We couldn\'t find that email address!';
		}
	}
?>

	<form action="" method="post">
		<ul>
			<li>
				Please enter your email address:<br>
				<input type="text" name="email">
			</li>
			<li>
				Please enter your <?php
					if (isset($_GET['mode']) === true && in_array($_GET['mode'], $mode_allowed) === true) {
						if ($_GET['mode'] === 'username') {
							echo 'password';
						} else {
							echo 'username';
						}
					} else { echo'[Error: Mode not recognized.]'; exit(); }
				?>:<br>
				<input type="<?php
					if (isset($_GET['mode']) === true && in_array($_GET['mode'], $mode_allowed) === true) {
						if ($_GET['mode'] === 'username') {
							echo 'password';
						} else {
							echo 'text';
						}
					} else { echo'[Error: Mode not recognized.]'; }
				?>" name="edom">
			</li>
			<li>
				Character name on your account:<br>
				<input type="text" name="character">
			</li>
			<li>
				<input type="submit" value="Recover">
			</li>
		</ul>
	</form>

<?php
} else {
	header('Location: index.php');
	exit();
}
?>

<?php include 'layout/overall/footer.php'; ?>
<?php require_once 'engine/init.php'; if ($config['twoFactorAuthenticator'] === false) die("twoFactorAuthenticator is disabled in config.php"); protect_page(); include 'layout/overall/header.php';
// Two-Factor Authentication setup page
if ($config['ServerEngine'] !== 'TFS_10') {
	?>
	<h1>Server compatibility error</h1>
	<p>Sorry, this server is not compatible with Two-Factor Authentication.<br>
	TFS 1.2 or higher is required to run two-factor authentication, grab it 
	<a href="https://github.com/otland/forgottenserver/releases" target="_BLANK">here</a>.</p>
	<?php
} else {
	// If user wishes to disable Two-Factor Authentication
	if (isset($_GET['disable'])) {
		mysql_update("UPDATE `accounts` SET `secret`=NULL WHERE `id`='".(int)$session_user_id."' LIMIT 1;");
		mysql_update("UPDATE `znote_accounts` SET `secret`=NULL WHERE `account_id`='".(int)$session_user_id."' LIMIT 1;");
	}

	// General init
	require_once("engine/function/rfc6238.php");

	// Fetch the secret data from accounts and znote_accounts table
	$query = mysql_select_single("SELECT `a`.`secret` AS `secret`, `za`.`secret` AS `znote_secret` FROM `accounts` AS `a` INNER JOIN `znote_accounts` AS `za` ON `a`.`id` = `za`.`account_id` WHERE `a`.`id`='".(int)$session_user_id."' LIMIT 1;");

	// If secret column returns NULL on the regular accounts table, then it means the system is not active.
	$status = ($query['secret'] === NULL) ? false : true;

	// If secret column returns NULL on the znote_accounts table, then it means we havent generated a secret for it yet.
	if ($query['znote_secret'] === NULL) {
		$scrtString = ($query['secret'] === NULL) ? generateRandomString(16) : $query['secret'];
		// Add secret to znote_accounts table
		mysql_update("UPDATE `znote_accounts` SET `secret`= '$scrtString' WHERE `account_id`='$session_user_id';");
		$query['znote_secret'] = $scrtString;
	}
	// HTML rendering
	?>
	<h1>Two-Factor Authentication</h1>
	<p>Account security with Two-factor Authentication: <b><?php echo ($status) ? 'Enabled' : 'Disabled'; ?></b>.</p>
	
	<?php if ($status === false): ?>
		<p><strong>Login with a token generated from this QR code to activate:</strong></p>
	<?php else: ?>
		<p>Click <a href="?disable">HERE</a> to disable Two-Factor Authentication and generate a new QR code.</p>
	<?php endif; ?>
	
	<img
		src="<?php echo TokenAuth6238::getBarCodeUrl($user_data['name'], $_SERVER["HTTP_HOST"], $query['znote_secret'], preg_replace('/\s+/', '', $config['site_title'])); ?>" 
		alt="Two-Factor Authentication QR code image for this account."
	/>

	<h2>How to use:</h2>
	<ol>
		<li>Download an authenticator app for free on your mobile phone like <strong>Authy</strong> (<a target="_BLANK" href="https://play.google.com/store/apps/details?id=com.authy.authy">Android</a>), (<a target="_BLANK" href="https://itunes.apple.com/us/app/authy/id494168017">iPhone</a>) or <strong>Google Authenticator</strong> (<a target="_BLANK" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">Android</a>), (<a target="_BLANK" href="https://itunes.apple.com/us/app/google-authenticator/id388497605">iPhone</a>).</li>
		<li>Scan the QR image with the app on your phone to create a Two-Factor account for this server.</li>
		<li><a href="logout.php">Logout</a>, then login with username, password and token generated from your phone to enable Two-Factor Authentication.</li>
	</ol>
	<?php
}
include 'layout/overall/footer.php'; ?>
<?php
require '../config.php';
require '../engine/database/connect.php';
?>

<h1>Gesior and Modern shop points to Znote AAC shop points</h1>
<p>Convert donation/shop points from previous Gesior/Modern installation to Znote AAC:</p>
<?php
	$accounts = mysql_select_multi("SELECT `id`, `premium_points` FROM `accounts` WHERE `premium_points`>'0';");
	$accountids = array();
	foreach ($accounts as $acc) $accountids[] = $acc['id'];
	$accidlist = join(',',$accountids);

	if ($accounts !== false) echo "<p>Detected: ". count($accounts) ." accounts who have points in old system.</p>";
	else die("<h1>All accounts already converted. :)</h1>");

	$znote_accounts = mysql_select_multi("SELECT `account_id`, `points` FROM `znote_accounts` WHERE `account_id` IN ($accidlist);");

	if (count($accounts) !== count($znote_accounts)) die("<h1><font color='red'>Failed to syncronize accounts. You need to convert all accounts to Znote AAC first!</font></h1>");

	// Order old accounts by id.
	$idaccounts = array();
	foreach ($accounts as $acc) {
		$idaccounts[$acc['id']] = $acc['premium_points'];
	}
	foreach ($znote_accounts as $acc) {
		mysql_update("UPDATE `znote_accounts` SET `points`='". ($acc['points'] + $idaccounts[$acc['account_id']]) ."' WHERE `account_id`='". $acc['account_id'] ."' LIMIT 1;");
	}
	mysql_update("UPDATE `accounts` SET `premium_points`='0';");

	echo "<h1><font color='green'>Successfully converted all points!</font></h1>";
?>
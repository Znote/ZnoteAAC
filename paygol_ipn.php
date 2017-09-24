<?php
require 'config.php';
require 'engine/database/connect.php';

// Fetch and sanitize POST and GET values
function getValue($value) {
	return (!empty($value)) ? sanitize($value) : false;
}
function sanitize($data) {
	return htmlentities(strip_tags(mysql_znote_escape_string($data)));
}

// get the variables from PayGol system
$message_id	= getValue($_GET['message_id']);
$service_id	= getValue($_GET['service_id']);
$shortcode	= getValue($_GET['shortcode']);
$keyword	= getValue($_GET['keyword']);
$message	= getValue($_GET['message']);
$sender		= getValue($_GET['sender']);
$operator	= getValue($_GET['operator']);
$country	= getValue($_GET['country']);
$custom		= getValue($_GET['custom']);
$points		= getValue($_GET['points']);
$price		= getValue($_GET['price']);
$currency	= getValue($_GET['currency']);
$secret		= getValue($_GET['secret']);

// config paygol settings
$paygol = $config['paygol'];

// Check for valid secret key
if($secret != $paygol['secret']) {
	header("HTTP/1.0 403 Forbidden");
	die("Error: secretKey does not match.");
}

// Check if request serviceID is the same as it is in config
if($service_id != $paygol['serviceID']) {
	header("HTTP/1.0 403 Forbidden");
	die("Error: serviceID does not match.");
}

$new_points = $paygol['points'];

// Update logs:
mysql_insert("INSERT INTO `znote_paygol` VALUES ('', '$custom', '$price', '$new_points', '$message_id', '$service_id', '$shortcode', '$keyword', '$message', '$sender', '$operator', '$country', '$currency')");

// Fetch points
$account = mysql_select_single("SELECT `points` FROM `znote_accounts` WHERE `account_id`='$custom';");

// Calculate new points
$new_points = $account['points'] + $new_points;

// Update new points
mysql_update("UPDATE `znote_accounts` SET `points`='$new_points' WHERE `account_id`='$custom'");
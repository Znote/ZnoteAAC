<?php
require 'config.php';
require 'engine/database/connect.php';

// check that the request comes from PayGol server
if(!in_array($_SERVER['REMOTE_ADDR'],
  array('109.70.3.48', '109.70.3.146', '109.70.3.58'))) {
  header("HTTP/1.0 403 Forbidden");
  die("Error: Unknown IP");
}

// get the variables from PayGol system
$message_id	= $_GET['message_id'];
$service_id	= $_GET['service_id'];
$shortcode	= $_GET['shortcode'];
$keyword	= $_GET['keyword'];
$message	= $_GET['message'];
$sender	 = $_GET['sender'];
$operator	= $_GET['operator'];
$country	= $_GET['country'];
$custom	 = $_GET['custom'];
$points	 = $_GET['points'];
$price	 = $_GET['price'];
$currency	= $_GET['currency'];

$paygol = $config['paygol'];
$new_points = $paygol['points'];

// Update logs:
mysql_insert("INSERT INTO `znote_paygol` VALUES ('', '$custom', '$price', '$new_points', '$message_id', '$service_id', '$shortcode', '$keyword', '$message', '$sender', '$operator', '$country', '$currency')");

// Fetch points
$account = mysql_select_single("SELECT `points` FROM `znote_accounts` WHERE `account_id`='$custom';");
// Calculate new points
$new_points = $account['points'] + $new_points;
// Update new points
mysql_update("UPDATE `znote_accounts` SET `points`='$new_points' WHERE `account_id`='$custom'");
?>
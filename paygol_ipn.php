<?php
	// Require the functions to connect to database and fetch config values
	require 'config.php';
	require 'engine/database/connect.php';
	
	// Fetch paygol configurations
	$paygol = $config['paygol'];

	// check that the request comes from PayGol server
	if(!in_array($_SERVER['REMOTE_ADDR'],
	  array('109.70.3.48', '109.70.3.146', '109.70.3.58', '31.45.23.9'))) {
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
	
	// FUNCTIONS
	function sanitize($data)/* Security reasons */ {
		return htmlentities(strip_tags(mysql_znote_escape_string($data)));
	}
	function user_data($user_id)/* account data */ {
		$data = array();
		$user_id = sanitize($user_id);
		
		$func_num_args = func_num_args();
		$func_get_args = func_get_args();
		
		if ($func_num_args > 1)  {
			unset($func_get_args[0]);
			
			$fields = '`'. implode('`, `', $func_get_args) .'`';
			$data = mysql_fetch_assoc(mysql_query("SELECT $fields FROM `accounts` WHERE `id` = $user_id;"));
			return $data;
		}
	}
	// Since only paygol.com is able to communicate with this script, we will blindly trust them until proven othervise.
	if ($service_id == $paygol['serviceID']) {
		$new_points = (int)$paygol['points'];
		
		$data = user_data($custom, 'name');
		if ($data['name']) {
			// Sanitize all data: (ok, we do not completely trust them blindly. D:)
			$message_id	= sanitize($message_id);
			$service_id	= sanitize($service_id);
			$shortcode	= sanitize($shortcode);
			$keyword	= sanitize($keyword);
			$message	= sanitize($message);
			$sender	 = sanitize($sender);
			$operator	= sanitize($operator);
			$country	= sanitize($country);
			$custom	 = sanitize($custom);
			$points	 = sanitize($points);
			$price	 = sanitize($price);
			$currency	= sanitize($currency);
			
			// Update logs:
			$log_query = mysql_query("INSERT INTO `znote_paygol` VALUES ('', '$custom', '$price', '$new_points', '$message_id', '$service_id', '$shortcode', '$keyword', '$message', '$sender', '$operator', '$country', '$currency')")or die("Log paygol SQL ERROR");
			
			// Give points to user
			$old_points = mysql_result(mysql_query("SELECT `points` FROM `znote_accounts` WHERE `account_id`='$custom';"), 0, 'points');
			echo 'Custom: '. $custom .'<br>';
			echo "Query: SELECT `points` FROM `znote_accounts` WHERE `account_id`='$custom';<br>";
			echo 'Old points: '. $old_points .'<br>';
			$new_points += $old_points;
			echo 'New points: '. $new_points .'<br>';
			$update_account = mysql_query("UPDATE `znote_accounts` SET `points`='$new_points' WHERE `account_id`='$custom'")or die(mysql_error());
			echo 'Account id 2 shold be updated now!';
			
		} else echo ' character data false';
	
	} else echo 'service id wrong';
?>



<?php 
/* TODO: FIX THIS FOR 1.5
require_once 'engine/init.php'; 
include 'layout/overall/header.php';

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

// Here you can do whatever you want with the variables, for instance inserting or updating data into your Database 


$query = mysql_query("UPDATE `znote_accounts` SET `points` = `points` + ".$points." WHERE `account_id` = ".$custom);

include 'layout/overall/footer.php';
*/
?>
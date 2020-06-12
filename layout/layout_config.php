<?php
	// Set enabled to false to remove the widget
	$follow = array(
		"enabled" => false,
		"twitter" => "https://www.twitter.com/",
		"facebook" => "https://www.facebook.com/",
		"youtube" => "https://www.youtube.com/",
		"twitch" => "https://www.twitch.tv/"
	);

	// Use same date format when changing: yyyy-mm-dd hh:mm
	$countDown = "2020-06-10 01:00";
	
	// Hide countdown after 1 day (24 hours) after countDown
	$countDown_hide = 1 * 24 * 60 * 60;

	// Say this after countdown, and before the row is hidden
	$countDown_complete = "<span style='color: green;'>ONLINE</span>";
?>

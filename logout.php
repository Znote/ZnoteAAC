<?php 
require_once 'engine/init.php'; include 'layout/overall/header.php';
	
	if (isset($_SESSION)) {
		session_destroy();
		header('Location: index.php');
	} 
?>
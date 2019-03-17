<!DOCTYPE HTML>
<html>
<?php 
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;
include 'layout/head.php'; ?>
<body<?php if (isset($page_filename) && strlen($page_filename) > 0) echo " class='page_{$page_filename}'"; ?>>
  <div id="main">
    <?php include 'layout/header.php'; ?>
    <div id="site_content">
      <?php include 'layout/aside.php'; ?>
      <div class="content">
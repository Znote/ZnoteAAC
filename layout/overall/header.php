<!DOCTYPE HTML>
<html>
<?php 
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;
include 'layout/head.php'; ?>
<body>
  <div id="main">
    <?php include 'layout/header.php'; ?>
    <div id="site_content">
      <?php include 'layout/aside.php'; ?>
      <div class="content">
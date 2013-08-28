<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; 

if ($config['allowSubPages']) include 'layout/sub.php';
else echo '<h2>System disabled.</h2><p>The sub page system is disabled.</p>';

include 'layout/overall/footer.php'; ?>
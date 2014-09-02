<?php require_once 'engine/init.php'; require_once 'layout/overall/header.php'; 
if ($config['allowSubPages']) {
	$page = (isset($_GET['page']) && !empty($_GET['page'])) ? getValue($_GET['page']) : '';
	if (isset($subpages[$page]['file'])) require_once 'layout/sub/'.$subpages[$page]['file'];
	else {
		if (isset($subpages)) echo '<h2>Sub page not recognized.</h2><p>The sub page you requested is not recognized.</p>';
	}
}
else echo '<h2>System disabled.</h2><p>The sub page system is disabled.</p>';
require_once 'layout/overall/footer.php'; ?>
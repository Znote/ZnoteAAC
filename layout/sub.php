<?php
switch ($_GET['page'])
{
	case 'blank':
		include 'layout/sub/blank.php';
	break;
	
	case 'houses':
		include 'layout/sub/houses.php';
	break;
	
	case 'bomberman':
		include 'layout/sub/bomberman.php';
	break;
	
	default:
		echo '<h2>Sub page not recognized.</h2><p>The sub page you requested is not recognized.</p>';
}
?>
<div id="sidebar_container">
	<?php
		if (user_logged_in() === true) {
			include 'layout/widgets/loggedin.php';
		} else {
			include 'layout/widgets/login.php';
		}
		if (user_logged_in() && is_admin($user_data)) include 'layout/widgets/Wadmin.php';
		if ($config['otservers_eu_voting']['enabled']) include 'layout/widgets/vote.php';
		include 'layout/widgets/charactersearch.php';
		include 'layout/widgets/topplayers.php';
		include 'layout/widgets/highscore.php';
		include 'layout/widgets/serverinfo.php';
		if ($config['ServerEngine'] !== 'TFS_02') include 'layout/widgets/houses.php';
		// Remove // to enable twitter, edit twitter stuff in /widgets/twitter.php
		//include 'layout/widgets/twitter.php';
	?>
</div>
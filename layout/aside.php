<!-- RIGHT PANE -->
<div class="pull-right rightPane">
	<?php
	if (user_logged_in() === true) {
		include 'layout/widgets/myaccount.php';
		if (is_admin($user_data)) include 'layout/widgets/admin.php';
	} else {
		include 'layout/widgets/login.php';
	}
	if ($config['otservers_eu_voting']['enabled']) include 'layout/widgets/vote.php';
	include 'layout/widgets/charactersearch.php';
	include 'layout/widgets/topplayers.php';
	include 'layout/widgets/highscore.php';
	include 'layout/widgets/serverinfo.php';
	if ($config['ServerEngine'] !== 'TFS_02') include 'layout/widgets/houses.php';
	if ($follow["enabled"]): ?>
		<div class="well">
			<div class="header">
				Follow Us
			</div>
			<div class="body">
				<table class="smedia centralizeContent">
					<tr>
						<td><a href="<?php echo $follow["facebook"]; ?>" target="_blank"><i class="fa fa-facebook"></i> </a></td>
						<td><a href="<?php echo $follow["twitter"]; ?>"  target="_blank"><i class="fa fa-twitter"></i> </a></td>
						<td><a href="<?php echo $follow["youtube"]; ?>"  target="_blank"><i class="fa fa-youtube"></i> </a></td>
						<td><a href="<?php echo $follow["twitch"]; ?>"   target="_blank"><i class="fa fa-twitch"></i> </a></td>
					</tr>
				</table>
			</div>
		</div>
	<?php endif;
	/*
		<div class="well">
			<div class="header">
				Events
			</div>
			<div class="body">
				<table>
					<tr><td>Event Name</td><td><i class="fa fa-clock-o"></i> 2h 5m 10s</td></tr>
					<tr><td>Event Name</td><td><i class="fa fa-clock-o"></i> 2h 5m 10s</td></tr>
					<tr><td>Event Name</td><td><i class="fa fa-clock-o"></i> 2h 5m 10s</td></tr>
					<tr><td>Event Name</td><td><i class="fa fa-clock-o"></i> 2h 5m 10s</td></tr>
					<tr><td>Event Name</td><td><i class="fa fa-clock-o"></i> 2h 5m 10s</td></tr>
				</table>
			</div>
		</div>

		<div class="well">
			<div class="header">
				Top 10 Players
			</div>
			<div class="body">
				<table>
					<tr><td>#</td><td>Name</td></tr>
					<tr><td>1</td><td>Name</td></tr>
					<tr><td>2</td><td>Name</td></tr>
					<tr><td>3</td><td>Name</td></tr>
					<tr><td>4</td><td>Name</td></tr>
					<tr><td>5</td><td>Name</td></tr>
					<tr><td>6</td><td>Name</td></tr>
					<tr><td>7</td><td>Name</td></tr>
					<tr><td>8</td><td>Name</td></tr>
					<tr><td>9</td><td>Name</td></tr>
					<tr><td>10</td><td>Name</td></tr>
				</table>
			</div>
		</div>
	*/
	?>
</div>
<!-- RIGHT PANE END -->
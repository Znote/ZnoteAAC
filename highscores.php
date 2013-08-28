<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
if ($config['log_ip']) {
	znote_visitor_insert_detailed_data(3);
}
if (empty($_POST) === false) {
	
	#if ($_POST['token'] == $_SESSION['token']) {
	
	/* Token used for cross site scripting security */
	if (isset($_POST['token']) && Token::isValid($_POST['token'])) {
		
		$skillid = (int)$_POST['selected'];
		$cache = new Cache('engine/cache/highscores');

		if ($cache->hasExpired()) {
			if ($config['TFSVersion'] != 'TFS_10') $tmp = highscore_getAll();
			else $tmp = highscore_getAll_10(0, 30);

			$cache->setContent($tmp);
			$cache->save();

			$array = isset($tmp[$skillid]) ? $tmp[$skillid] : $tmp[7];
		} else {
			$tmp = $cache->load();
			$array = $tmp[$skillid];
		}
		
		if ($skillid < 9) {
		// Design and present the list
		if ($array) {
			?>
			<h2>
				<?php echo ucfirst(skillid_to_name($skillid)); ?> scoreboard. Next update: 
					<?php
						if ($cache->remainingTime() > 0) {
							$hours = seconds_to_hours($cache->remainingTime());
							$minutes = ($hours - (int)$hours) * 60;
							$seconds = ($minutes - (int)$minutes) * 60;
							if ($hours >= 1) {
								echo (int)$hours .'h';
							}
							if ($minutes >= 1) {
								echo ' '. (int)$minutes .'m';
							}
							if ($seconds >= 1) {
								echo ' '. (int)$seconds .'s';
							}
						} else {
							echo '0s';
						}
						
					?>. <?php echo remaining_seconds_to_clock($cache->remainingTime());?>
			</h2>
			<table id="highscoresTable" class="table table-striped table-hover">
				<tr class="yellow">
					<th>Name:</th>
					<?php
					if ($skillid == 7) echo '<th>Level:</th><th>Experience:</th>';
					else {
					?>
					<th>Value:</th>
					<?php
					}
					if ($skillid == 7 || $skillid == 6 || $skillid == 5) {
						echo '<th>Vocation:</th>';
					}
					?>
				</tr>
					<?php
					foreach ($array as $value) {
						// start foreach
						if ($value['group_id'] < 2) {
							echo '<tr>';
							echo '<td><a href="characterprofile.php?name='. $value['name'] .'">'. $value['name'] .'</a></td>';
							if ($skillid == 7) echo '<td>'. $value['level'] .'</td>';
							echo '<td>'. $value['value'] .'</td>';
							if ($skillid == 7 || $skillid == 6 || $skillid == 5) {
								echo '<td>'. $value['vocation'] .'</td>';
							}
							echo '</tr>';
						}
						// end foreach
					}
					?>
			</table>
			<?php
		} else {
			echo 'Empty list, it appears all players have less than 500 experience points.';
		}
		//Done.
		}
	} else {
		echo 'Token appears to be incorrect.<br><br>';
		//Token::debug($_POST['token']);
		echo 'Please clear your web cache/cookies <b>OR</b> use another web browser<br>';
	}
}

/*
0 fist: SELECT (SELECT `name` from `players` WHERE `player_id`=`id`) AS `name`, `value` FROM `player_skills` WHERE `skillid`=0
1 club: 
2 sword: 
3 axe: 
4 dist: 
5 Shield: 
6 Fish
7 Hardcoded experience
8 Hardcoded maglevel
*/
include 'layout/overall/footer.php'; ?>
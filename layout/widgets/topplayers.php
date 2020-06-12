<div class="well widget">
	<div class="header">
		Top 5 players
	</div>
	<div class="body">
		<table>
			<?php
			$cache = new Cache('engine/cache/topPlayer');
			if ($cache->hasExpired()) {
				$players = mysql_select_multi('SELECT `name`, `level`, `experience` FROM `players` WHERE `group_id` < ' . $config['highscore']['ignoreGroupId'] . ' ORDER BY `level` DESC, `experience` DESC LIMIT 5;');
				
				$cache->setContent($players);
				$cache->save();
			} else {
				$players = $cache->load();
			}

			if ($players) {
				foreach($players as $count => $player) {
					$nr = $count+1;
					echo "<tr><td>{$nr}</td><td><a href='characterprofile.php?name={$player['name']}'>{$player['name']}</a> ({$player['level']}).</td></tr>";
				}
			}
			?>
		</table>
	</div>
</div>
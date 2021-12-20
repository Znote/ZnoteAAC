<div class="well widget">
	<div class="header">
		Top 5 Powergamers
	</div>
	<div class="body">
		<table>
			<?php
			$cache = new Cache('engine/cache/widget_powergamers');
			if ($cache->hasExpired()) {
				$players = mysql_select_multi("
					SELECT
					    `h`.`player_id`,
					    `p`.`name`,
					    `p`.`level`,
					    CAST(`p`.`experience` as signed) - CAST(`f`.`experience` as signed) AS `diff_experience`
					FROM (
					    SELECT
					        `i`.`player_id`,
					        IFNULL(`o`.`id`, `i`.`id`) AS `from_id`
					    FROM `player_history_skill` AS `i`
					    LEFT JOIN (
					        SELECT
					            `x`.`player_id`,
					            MAX(`x`.`id`) AS `id`
					        FROM `player_history_skill` AS `x`
					        WHERE 
					            `x`.`lastlogout` < UNIX_TIMESTAMP() - 7 * 24 * 60 * 60
					        GROUP BY
					            `x`.`player_id`
					    ) AS `o`
					        ON `i`.`player_id` = `o`.`player_id`
					    WHERE 
					        `i`.`lastlogout` >= UNIX_TIMESTAMP() - 7 * 24 * 60 * 60
					    GROUP BY
					        `i`.`player_id`
					) AS `h`
					INNER JOIN `player_history_skill` AS `f`
					    ON `h`.`from_id` = `f`.`id`
					INNER JOIN `players` AS `p`
					    ON `h`.`player_id` = `p`.`id`
					WHERE CAST(`p`.`experience` as signed) - CAST(`f`.`experience` as signed) > 0
					ORDER BY CAST(`p`.`experience` as signed) - CAST(`f`.`experience` as signed) DESC
					LIMIT 5
				");

				$cache->setContent($players);
				$cache->save();
			} else {
				$players = $cache->load();
			}

			if ($players) {
				foreach($players as $count => $player) {
					$nr = $count+1;
					$kexp = $player['diff_experience'] / 1000;
					$kexp = number_format($kexp, 0, '', ' ');
					echo "<tr><td>{$nr}</td><td><a href='characterprofile.php?name={$player['name']}'>{$player['name']}</a> ({$player['level']}) <span style='float: right;font-size:14px;'>{$kexp} K exp</span></td></tr>";
				}
			}
			?>
		</table>
	</div>
</div>

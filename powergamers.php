<?php require_once 'engine/init.php'; include 'layout/overall/header.php';

if (!$config['powergamers']['enabled']) {
	echo 'This page has been disabled at config.php.';
	include 'layout/overall/footer.php';
	exit();
}

$query_CTE = "
	WITH CTE_history AS (
		SELECT
			`id`,
			`player_id`,
			CAST(DATE_FORMAT(FROM_UNIXTIME(`lastlogin`), '%y%m%d') as int) AS `login_int`,
			CAST(DATE_FORMAT(FROM_UNIXTIME(`lastlogout`), '%y%m%d') as int) AS `logout_int`,
			`experience`
		FROM `player_history_skill`
	), CTE_time AS (
		SELECT
			1 AS `link`,
			CAST(DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP() - 7 * 24 * 60 * 60), '%y%m%d') as int) AS `d7ago`,
			CAST(DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP() - 6 * 24 * 60 * 60), '%y%m%d') as int) AS `d6ago`,
			CAST(DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP() - 5 * 24 * 60 * 60), '%y%m%d') as int) AS `d5ago`,
			CAST(DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP() - 4 * 24 * 60 * 60), '%y%m%d') as int) AS `d4ago`,
			CAST(DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP() - 3 * 24 * 60 * 60), '%y%m%d') as int) AS `d3ago`,
			CAST(DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP() - 2 * 24 * 60 * 60), '%y%m%d') as int) AS `d2ago`,
			CAST(DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP() - 1 * 24 * 60 * 60), '%y%m%d') as int) AS `d1ago`
	), CTE_first AS (
		SELECT `player_id`, MIN(`id`) AS `id`
		FROM CTE_history
		GROUP BY `player_id`
	), CTE_7b AS (
		SELECT `player_id`, MAX(`id`) AS `id`
		FROM CTE_history INNER JOIN CTE_time AS `t` ON `t`.`link` = 1
		WHERE `logout_int` <= `t`.`d7ago`
		GROUP BY `player_id`
	), CTE_6b AS (
		SELECT `player_id`, MAX(`id`) AS `id`
		FROM CTE_history INNER JOIN CTE_time AS `t` ON `t`.`link` = 1
		WHERE `logout_int` <= `t`.`d6ago`
		GROUP BY `player_id`
	), CTE_5b AS (
		SELECT `player_id`, MAX(`id`) AS `id`
		FROM CTE_history INNER JOIN CTE_time AS `t` ON `t`.`link` = 1
		WHERE `logout_int` <= `t`.`d5ago`
		GROUP BY `player_id`
	), CTE_4b AS (
		SELECT `player_id`, MAX(`id`) AS `id`
		FROM CTE_history INNER JOIN CTE_time AS `t` ON `t`.`link` = 1
		WHERE `logout_int` <= `t`.`d4ago`
		GROUP BY `player_id`
	), CTE_3b AS (
		SELECT `player_id`, MAX(`id`) AS `id`
		FROM CTE_history INNER JOIN CTE_time AS `t` ON `t`.`link` = 1
		WHERE `logout_int` <= `t`.`d3ago`
		GROUP BY `player_id`
	), CTE_2b AS (
		SELECT `player_id`, MAX(`id`) AS `id`
		FROM CTE_history INNER JOIN CTE_time AS `t` ON `t`.`link` = 1
		WHERE `logout_int` <= `t`.`d2ago`
		GROUP BY `player_id`
	), CTE_1b AS (
		SELECT `player_id`, MAX(`id`) AS `id`
		FROM CTE_history INNER JOIN CTE_time AS `t` ON `t`.`link` = 1
		WHERE `logout_int` <= `t`.`d1ago`
		GROUP BY `player_id`
	)
";
$cache = new Cache('engine/cache/page_powergamers');
if ($cache->hasExpired()) {
	$players = mysql_select_multi($query_CTE."
		SELECT
			`p`.`name`,
			IFNULL(`p`.`experience`, 0) - CASE WHEN `h7b`.`experience` IS NULL 
				THEN `hfb`.`experience` 
				ELSE `h7b`.`experience` 
			END AS `diff_exp`,
			CAST(`p`.`experience` as SIGNED) - IFNULL(`h1b`.`experience`, 0) AS `diff_0`,
			IFNULL(`h1b`.`experience`, 0) - IFNULL(`h2b`.`experience`, 0) AS `diff_1`,
			IFNULL(`h2b`.`experience`, 0) - IFNULL(`h3b`.`experience`, 0) AS `diff_2`,
			IFNULL(`h3b`.`experience`, 0) - IFNULL(`h4b`.`experience`, 0) AS `diff_3`,
			IFNULL(`h4b`.`experience`, 0) - IFNULL(`h5b`.`experience`, 0) AS `diff_4`,
			IFNULL(`h5b`.`experience`, 0) - IFNULL(`h6b`.`experience`, 0) AS `diff_5`,
			IFNULL(`h6b`.`experience`, 0) - IFNULL(`h7b`.`experience`, 0) AS `diff_6`
		FROM `players` AS `p`
		LEFT JOIN CTE_first AS `first` ON `p`.`id` = `first`.`player_id`
		LEFT JOIN CTE_1b AS `d1b` ON `p`.`id` = `d1b`.`player_id`
		LEFT JOIN CTE_2b AS `d2b` ON `p`.`id` = `d2b`.`player_id`
		LEFT JOIN CTE_3b AS `d3b` ON `p`.`id` = `d3b`.`player_id`
		LEFT JOIN CTE_4b AS `d4b` ON `p`.`id` = `d4b`.`player_id`
		LEFT JOIN CTE_5b AS `d5b` ON `p`.`id` = `d5b`.`player_id`
		LEFT JOIN CTE_6b AS `d6b` ON `p`.`id` = `d6b`.`player_id`
		LEFT JOIN CTE_7b AS `d7b` ON `p`.`id` = `d7b`.`player_id`
		LEFT JOIN CTE_history AS `hfb` ON `first`.`id` = `hfb`.`id`
		LEFT JOIN CTE_history AS `h1b` ON `d1b`.`id` = `h1b`.`id`
		LEFT JOIN CTE_history AS `h2b` ON `d2b`.`id` = `h2b`.`id`
		LEFT JOIN CTE_history AS `h3b` ON `d3b`.`id` = `h3b`.`id`
		LEFT JOIN CTE_history AS `h4b` ON `d4b`.`id` = `h4b`.`id`
		LEFT JOIN CTE_history AS `h5b` ON `d5b`.`id` = `h5b`.`id`
		LEFT JOIN CTE_history AS `h6b` ON `d6b`.`id` = `h6b`.`id`
		LEFT JOIN CTE_history AS `h7b` ON `d7b`.`id` = `h7b`.`id`
		WHERE IFNULL(`p`.`experience`, 0) - CASE WHEN `h7b`.`experience` IS NULL THEN `hfb`.`experience` ELSE `h7b`.`experience` END != 0
		ORDER BY IFNULL(`p`.`experience`, 0) - CASE WHEN `h7b`.`experience` IS NULL THEN `hfb`.`experience` ELSE `h7b`.`experience` END DESC
	");
	$cache->setContent($players);
	$cache->save();
} else {
	$players = $cache->load();
}

$dates = mysql_select_single("
	SELECT
	    FROM_UNIXTIME(UNIX_TIMESTAMP() - 7 * 24 * 60 * 60, '%d %b') AS `d7ago`,
	    FROM_UNIXTIME(UNIX_TIMESTAMP() - 6 * 24 * 60 * 60, '%d %b') AS `d6ago`,
	    FROM_UNIXTIME(UNIX_TIMESTAMP() - 5 * 24 * 60 * 60, '%d %b') AS `d5ago`,
	    FROM_UNIXTIME(UNIX_TIMESTAMP() - 4 * 24 * 60 * 60, '%d %b') AS `d4ago`,
	    FROM_UNIXTIME(UNIX_TIMESTAMP() - 3 * 24 * 60 * 60, '%d %b') AS `d3ago`,
	    FROM_UNIXTIME(UNIX_TIMESTAMP() - 2 * 24 * 60 * 60, '%d %b') AS `d2ago`,
	    FROM_UNIXTIME(UNIX_TIMESTAMP() - 1 * 24 * 60 * 60, '%d %b') AS `d1ago`,
	    FROM_UNIXTIME(UNIX_TIMESTAMP(), '%d %b') AS `d0ago`
");
?>
<table id="tbl_powergamers">
	<thead>
		<tr>
			<th colspan="9"><h1>Powergamers</h1></th>
		</tr>
		<tr>
			<th>Name</th>
			<th>k Diff</th>
			<th><?php echo $dates['d0ago']; ?></th>
			<th><?php echo $dates['d1ago']; ?></th>
			<th><?php echo $dates['d2ago']; ?></th>
			<th><?php echo $dates['d3ago']; ?></th>
			<th><?php echo $dates['d4ago']; ?></th>
			<th><?php echo $dates['d5ago']; ?></th>
			<th><?php echo $dates['d6ago']; ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($players AS $i => $player): ?>
			<tr>
				<td><?php echo $i+1 .". "; ?><a href="/characterprofile.php?name=<?php echo $player['name']; ?>"><?php echo $player['name']; ?></a></td>
				<td><?php echo number_format($player['diff_exp'] / 1000,0,'',' '); ?></td>
				<td><?php echo number_format($player['diff_0'] / 1000,0,'',' '); ?></td>
				<td><?php echo number_format($player['diff_1'] / 1000,0,'',' '); ?></td>
				<td><?php echo number_format($player['diff_2'] / 1000,0,'',' '); ?></td>
				<td><?php echo number_format($player['diff_3'] / 1000,0,'',' '); ?></td>
				<td><?php echo number_format($player['diff_4'] / 1000,0,'',' '); ?></td>
				<td><?php echo number_format($player['diff_5'] / 1000,0,'',' '); ?></td>
				<td><?php echo number_format($player['diff_6'] / 1000,0,'',' '); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<style type="text/css">
	#tbl_powergamers {
		padding:  0;
	}
</style>
<?php
include 'layout/overall/footer.php'; ?>

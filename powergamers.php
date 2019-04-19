<?php
require_once 'engine/init.php';
include 'layout/overall/header.php'; 
if (!$config['powergamers']['enabled']) {
echo 'This page has been disabled at config.php.';
include 'layout/overall/footer.php';
	exit();
}
?>
<div class="panel">
<div class="page-header"><h3>Powergamers</h3></div>
	<?php
	$limit = $config['powergamers']['limit'];
	$days = isset($_POST['days']);
	$today = true;
	if ($days) {
		$selected = ($_POST['days']);
		$days = (int) $selected[1];
		$vocation = (int) $selected[0];
		if ($days > 0)
		$today = false;
	} else {
		$znotePlayers = mysql_select_multi('SELECT `a`.`id`, `b`.`player_id`, `a`.`name`, `a`.`vocation`, `a`.`level`, `a`.`group_id`, `a`.`experience`, `b`.`exphist_lastexp`, `b`.`exphist1`, `b`.`exphist2`, `b`.`exphist3`, `b`.`exphist4`, `b`.`exphist5`, `b`.`exphist6`, `b`.`exphist7`,   (`a`.`experience` - `b`.`exphist_lastexp`)  AS `expdiff` FROM `players` `a` JOIN `znote_players` `b` ON `a`.`id` = `b`.`player_id`  WHERE `a`.`group_id` < 2 ORDER BY `expdiff` DESC LIMIT '.$limit);
	}
	$limit = $config['powergamers']['limit'];

	if(!empty($days) && !empty($vocation)) 
		$znotePlayers = mysql_select_multi('SELECT `a`.`id`, `b`.`player_id`, `a`.`name`, `a`.`vocation`, `a`.`level`, `a`.`group_id`, `a`.`experience`, `b`.`exphist_lastexp`, `b`.`exphist1`, `b`.`exphist2`, `b`.`exphist3`, `b`.`exphist4`, `b`.`exphist5`, `b`.`exphist6`, `b`.`exphist7`, (`a`.`experience` - `b`.`exphist_lastexp`) AS `expdiff` FROM `players` `a` JOIN `znote_players` `b` ON `a`.`id` = `b`.`player_id`  WHERE `a`.`group_id` < 2 AND `a`.`vocation`='. (int)$vocation .' OR `a`.`vocation`='. ((int)$vocation +4) .' ORDER BY `exphist' . (int)$days . '` DESC LIMIT '.$limit);
	elseif(empty($days) && !empty($vocation)) {
		$znotePlayers = mysql_select_multi('SELECT `a`.`id`, `b`.`player_id`, `a`.`name`, `a`.`vocation`, `a`.`level`, `a`.`group_id`, `a`.`experience`, `b`.`exphist_lastexp`, `b`.`exphist1`, `b`.`exphist2`, `b`.`exphist3`, `b`.`exphist4`, `b`.`exphist5`, `b`.`exphist6`, `b`.`exphist7`,   (`a`.`experience` - `b`.`exphist_lastexp`)  AS `expdiff` FROM `players` `a` JOIN `znote_players` `b` ON `a`.`id` = `b`.`player_id`  WHERE `a`.`group_id` < 2 AND `a`.`vocation`='. (int)$vocation .' OR `a`.`vocation`='. ((int)$vocation +4) .' ORDER BY `expdiff` DESC LIMIT '.$limit);
	}elseif(!empty($days) && empty($vocation)) 
		$znotePlayers = mysql_select_multi('SELECT `a`.`id`, `b`.`player_id`, `a`.`name`, `a`.`vocation`, `a`.`level`, `a`.`group_id`, `a`.`experience`, `b`.`exphist_lastexp`, `b`.`exphist1`, `b`.`exphist2`, `b`.`exphist3`, `b`.`exphist4`, `b`.`exphist5`, `b`.`exphist6`, `b`.`exphist7`, (`a`.`experience` - `b`.`exphist_lastexp`) AS `expdiff` FROM `players` `a` JOIN `znote_players` `b` ON `a`.`id` = `b`.`player_id`  WHERE `a`.`group_id` < 2 ORDER BY `exphist' . (int)$days . '` DESC LIMIT '.$limit);
	else 
		$znotePlayers = mysql_select_multi('SELECT `a`.`id`, `b`.`player_id`, `a`.`name`, `a`.`vocation`, `a`.`level`, `a`.`group_id`, `a`.`experience`, `b`.`exphist_lastexp`, `b`.`exphist1`, `b`.`exphist2`, `b`.`exphist3`, `b`.`exphist4`, `b`.`exphist5`, `b`.`exphist6`, `b`.`exphist7`,   (`a`.`experience` - `b`.`exphist_lastexp`)  AS `expdiff` FROM `players` `a` JOIN `znote_players` `b` ON `a`.`id` = `b`.`player_id`  WHERE `a`.`group_id` < 2 ORDER BY `expdiff` DESC LIMIT '.$limit);

	$showVoc = (!empty($vocation)) ? $vocation : 0;
	?>
	<form class="form form-inline" action="" method="post">
		<div class="col sm-4">
			<center>
			<select class="form-control" name="days[]">
				<option value="" selected="all">All</option>
				<option value="1">Sorcerers</option>
				<option value="2">Druids</option>
				<option value="3">Paladins</option>
				<option value="4">Knights</option>
				<option value="none">No vocation</option>
			</select>
			<select class="form-control" name="days[]">
				<option value="" selected="Today">Today</option>
				<option value="1">Yesterday</option>
				<option value="2">2 days ago</option>
				<option value="3">3 days ago</option>
			</select>
			<input type="submit" class="btn btn-primary"><br>
			<?php echo ($showVoc > 0) ? 'Showing only <b>'. strtolower(vocation_id_to_name($vocation)).'s</b> and' : 'Showing <b>all</b> vocations and'; ?>
			<?php echo ($days > 0) ? 'sorted by <b>'. $days .'</b> days': 'sorted by <b>today</b>'; 	?>.
			</center>
		</div>
	</form>
	<table class="table table-striped">
		<td width="5%"><center>#</center></td>
		<td>Name</td>
		<?php
	for($i = 3; $i >= 2; $i--)
		echo ($days == $i) ? '<td class="pull-right" width="70%"><b>'.$i.' Days Ago</b></td>' : '';
		echo ($days == 1) ? '<td class="pull-right" width="70%"><b>Yesterday</b></td>' : '';
		echo ($today) ? '<td class="pull-right" width="70%"><b>Today</b></td>' : '';
		echo ($days == 4) ? '<td class="pull-right" width="70%"><b>Total</b></td>' : '';
		echo '</tr>';

	$number_of_rows = 0;
	if($znotePlayers) {
		foreach($znotePlayers as $player)
		{
			$number_of_rows++;
			echo '<td><center>'. $number_of_rows . '.</center></td>';
			echo '<td><a href="characterprofile.php?name=' .$player['name']. '">' .$player['name']. '</a>';
			echo '<br> '. ($player['level']. ' '.htmlspecialchars(vocation_id_to_name($player['vocation'])) ).' ';
			echo ($days == 3) ? '<td><center>'. number_format($player['exphist3']) .'</center></td>' : '';
			echo ($days == 2) ? '<td><center>'. $player['exphist2'] .'</center></td>' : '';
			echo ($days == 1) ? '<td><center>'. $player['exphist1'] .'</center></td>' : '';
			echo ($today == true) ? '<td><center>'. ($player['experience']-$player['exphist_lastexp']) .'</center></td>' : '';
			echo '</tr>';
		}
	}
	?>
	</table>
	<br>
</div>
<?php
include 'layout/overall/footer.php';

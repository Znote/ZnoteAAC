<?php
require_once 'engine/init.php';
include 'layout/overall/header.php'; 

if (!$config['powergamers']['enabled']) {
echo 'This page has been disabled at config.php.';
include 'layout/overall/footer.php';
	exit();
}

$limit = $config['powergamers']['limit'];
?>
<style>
#selectedP {
text-decoration: underline
}
</style>
	  <?php
$type = $_GET['type'];

function coloured_value($valuein)
{
	$value2 = $valuein;
	while(strlen($value2) > 3)
	{
		$value .= '.'.substr($value2, -3, 3);
		$value2 = substr($value2, 0, strlen($value2)-3);
	}
	@$value = $value2.$value;
	if($valuein > 0)
		return '<b><font color="green">+'.$value.'</font></b>';
	elseif($valuein < 0)
		return '<font color="red">'.$value.'</font>';
	else
		return $value;
}

if(empty($type))
	$znotePlayers = mysql_select_multi('SELECT `a`.`id`, `b`.`player_id`, `a`.`name`, `a`.`vocation`, `a`.`level`, `a`.`group_id`, `a`.`experience`, `b`.`exphist_lastexp`, `b`.`exphist1`, `b`.`exphist2`, `b`.`exphist3`, `b`.`exphist4`, `b`.`exphist5`, `b`.`exphist6`, `b`.`exphist7`,   (`a`.`experience` - `b`.`exphist_lastexp`)  AS `expdiff` FROM `players` `a` JOIN `znote_players` `b` ON `a`.`id` = `b`.`player_id`  WHERE `a`.`group_id` < 2 ORDER BY `expdiff` DESC LIMIT '.$limit);
elseif($type >= 1 && $type <= 3)
	$znotePlayers = mysql_select_multi('SELECT `a`.`id`, `b`.`player_id`, `a`.`name`, `a`.`vocation`, `a`.`level`, `a`.`group_id`, `a`.`experience`, `b`.`exphist_lastexp`, `b`.`exphist1`, `b`.`exphist2`, `b`.`exphist3`, `b`.`exphist4`, `b`.`exphist5`, `b`.`exphist6`, `b`.`exphist7`, (`a`.`experience` - `b`.`exphist_lastexp`) AS `expdiff` FROM `players` `a` JOIN `znote_players` `b` ON `a`.`id` = `b`.`player_id`  WHERE `a`.`group_id` < 2 ORDER BY `exphist' . (int) $type . '` DESC LIMIT '.$limit);
echo '<CENTER><H2>Ranking of powergamers</H2></CENTER>
<BR>
<table class="table table-striped">
		<td><center><b>#</b></center></td>
		<td><b>Name</b></td>';
echo '<td><center>Total</center></td>';

for($i = 3; $i >= 2; $i--)
	echo ($type == $i) ? '<TD id="selectedP" width="16%"><a href="powergamers.php?type='.$i.'">'.$i.'<b> Days Ago</b></a></B></TD>' : '<TD width="16%"><center><a href="powergamers.php?type='.$i.'">'.$i.' Days Ago</a></TD>';
	echo ($type == 1) ? '<TD id="selectedP" width="16%"><b><a href="powergamers.php?type=1">1 Day Ago</a></B></TD>' : '<TD width="16%"><a href="powergamers.php?type=1">1 Day Ago</a></TD>';
	echo (empty($type)) ? '<TD id="selectedP"><b><a href="powergamers.php">Today</a></b></TD>' : '<TD><a href="powergamers.php">Today</a></TD>';
	echo '</TR>';

$number_of_rows = 1;
if($znotePlayers)
	foreach($znotePlayers as $player)
	{
		echo '<td><center>'. $number_of_rows . '.</center></td>';
		echo '<td><a href="characterprofile.php?name=' .$player['name']. '">' .$player['name']. '</a>';
		echo '<br> ' .$player['level']. ' '.htmlspecialchars(vocation_id_to_name($player['vocation'])).' ';
		
		echo '<td><center>'.coloured_value($player['exphist1'] + $player['exphist2'] + $player['exphist3'] + $player['experience'] - $player['exphist_lastexp']).'</center></td>';
		echo '<td><center>'.coloured_value($player['exphist3']).'</center></td><td><center>'.coloured_value($player['exphist2']).'</center></td><td><center>'.coloured_value($player['exphist1']).'</center></td><td><center>'.coloured_value($player['experience']-$player['exphist_lastexp']).'</center></td></tr>';	$number_of_rows++;
	}
echo '</table><br></div>';
?>
<?php
include 'layout/overall/footer.php';
?>

<?php
require_once 'engine/init.php';
include 'layout/overall/header.php'; 
$powergamers = $config['powergamers'];
$limit = $powergamers['limit'];

if ($config['powergamers_enabled'] === false) {
echo 'Powergamers page has been disabled at config.php.';
include 'layout/overall/footer.php';
	exit();
}
?>
	  <?php

$type = @$_GET['type'];
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
	$players = mysql_select_multi('SELECT `p`.`name`, `p`.`level`, `p`.`vocation`, `p`.`experience`, `p`.`exphist1`, `p`.`exphist2`, `p`.`exphist3`, `p`.`exphist_lastexp`, `p_on`.`player_id` AS `online` FROM `players` p LEFT JOIN `players_online` p_on ON `p`.`id` = `p_on`.`player_id` WHERE `p`.`group_id` < 2 ORDER BY `p`.`experience`-`p`.`exphist_lastexp` DESC LIMIT ' . $limit);
elseif($type == "sum")
	$players = mysql_select_multi('SELECT `p`.*, `p_on`.`player_id` AS online FROM `players` p LEFT JOIN `players_online` p_on ON `p`.`id` = `p_on`.`experience`-`p`.`exphist_lastexp` DESC LIMIT ' . $limit);
elseif($type >= 1 && $type <= 7)
	$players = mysql_select_multi('SELECT `p`.*, `p_on`.`player_id` AS online FROM `players` p LEFT JOIN `players_online` p_on ON `p`.`id` = `p_on`.`player_id` WHERE `p`.`group_id` < 2 ORDER BY `p`.`exphist' . (int) $type . '` DESC LIMIT '.$limit);
echo '<CENTER><H2>Ranking of powergamers</H2></CENTER>
<BR>
<table class="table table-striped">
		<td><B>#</B></td>
		<td><B>Name</B></td>';

echo ($type == 'sum') ? '<TD><b><center>Weekly <br> Experience</B></TD>' : '<TD>Weekly<br> Experience</TD>';

for($i = 3; $i >= 2; $i--)
	echo ($type == $i) ? '<TD><a href="powergamers.php?type='.$i.'">'.$i.'<br><b> Days Ago</b></a></B></TD>' : '<TD><center><a href="powergamers.php?type='.$i.'">'.$i.'<br> Days Ago</a></TD>';

echo ($type == 1) ? '<TD><b><a href="powergamers.php?type=1">1<br> Day Ago</a></B></TD>' : '<TD><a href="powergamers.php?type=1">1<br> Day Ago</a></TD>';

echo (empty($type)) ? '<TD><b><a href="powergamers.php"><br>Today</a></TD>' : '<TD><a href="powergamers.php"><br>Today</a></TD>';
echo '</TR>';

$number_of_rows = 1;
if($players)
	foreach($players as $player)
	{
		echo '<td>'. $number_of_rows . '. </td>';
		
		


		echo ($player['online']) ? '<td><a href="characterprofile.php?name=' . urlencode($player['name']) . '"><b><font color="green">' . htmlspecialchars($player['name']) . '</font></b></a>' : '<td><a href="characterprofile.php?name=' . urlencode($player['name']) . '"><b><font color="red">' . htmlspecialchars($player['name']) . '</font></b></a>';
		
		echo '<br />'.$player['level'].' '.htmlspecialchars(vocation_id_to_name($player['vocation'])).'</td><td  >'.coloured_value($player['exphist1'] + $player['exphist2'] + $player['exphist3'] + $player['experience'] - $player['exphist_lastexp']).'</td>';
		echo '<td  >'.coloured_value($player['exphist3']).'</td><td  >'.coloured_value($player['exphist2']).'</td><td  >'.coloured_value($player['exphist1']).'</td><td  >'.coloured_value($player['experience']-$player['exphist_lastexp']).'</td></tr>';
		$number_of_rows++;
	}
echo '</table>';
?>
<?php
include 'layout/overall/footer.php';
?>

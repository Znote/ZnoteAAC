<?php
require_once 'engine/init.php';
include 'layout/overall/header.php'; 
if (!$config['toponline']['enabled']) {
echo 'This page has been disabled at config.php.';
include 'layout/overall/footer.php';
	exit();
}
$limit = $config['toponline']['limit'];
$type = (isset($_GET['type'])) ? getValue($_GET['type']) : false;

function onlineTimeTotal($value)
{
	$hours = floor($value / 3600);
	$value = $value - $hours * 3600;
	$minutes = floor($value / 60);
		return '<font color="black">'.$hours.'h '.$minutes.'m</font>';
}
function hours_and_minutes($value, $color = 1)
{
	$hours = floor($value / 3600);
	$value = $value - $hours * 3600;
	$minutes = floor($value / 60);
	if($color != 1)
		return '<font color="black">'.$hours.'h '.$minutes.'m</font>';
	else
		if($hours >= 12)
			return '<font color="red">'.$hours.'h '.$minutes.'m</font>';
		elseif($hours >= 6)
			return '<font color="black">'.$hours.'h '.$minutes.'m</font>';
		else
			return '<font color="green">'.$hours.'h '.$minutes.'m</font>';
}
if(empty($type))
	$znotePlayers = mysql_select_multi('SELECT * FROM `znote_players` AS `z` JOIN `players` AS `p` WHERE `p`.`id`=`z`.`player_id` and `p`.`group_id` < 3 ORDER BY `onlinetimetoday` DESC LIMIT '.$limit);
elseif($type == "sum")
	$znotePlayers = mysql_select_multi('SELECT * FROM `znote_players` AS `z` JOIN `players` AS `p` WHERE `p`.`id`=`z`.`player_id` and `p`.`group_id` < 3 ORDER BY `z`.`onlinetimeall` DESC LIMIT '. $limit);
elseif($type >= 1 && $type <= 4)
	$znotePlayers = mysql_select_multi('SELECT * FROM `znote_players` AS `z` JOIN `players` AS `p` WHERE `p`.`id`=`z`.`player_id` and `p`.`group_id` < 3 ORDER BY `onlinetime' . (int) $type . '` DESC LIMIT '.$limit);
	
echo '<CENTER><H2>Most online on' .$config['site_title'] . '</H2></CENTER>
<BR>
<table class="table table-striped">
		<td><center><b>#</b></center></td>
		<td width="10%"><b>Name</b></td>';
if($type == "sum")
	echo '<td ><center><b><center><a href="?subtopic=onlinetime&type=sum">Total</a></center></B></TD>';
else
	echo '<td ><center><b><center><a href="?subtopic=onlinetime&type=sum">Total</a></center></B></TD>';
for($i = 3; $i >= 2; $i--)
{
	if($type == $i)
		echo '<TD ><b><center><a href="?subtopic=onlinetime&type='.$i.'">'.$i.' Days Ago</a></center></B></TD>';
	else
		echo '<TD ><b><center><a href="?subtopic=onlinetime&type='.$i.'">'.$i.' Days Ago</a></center></B></TD>';
}
if($type == 1)
	echo '<TD ><b><center><a href="?subtopic=onlinetime&type=1">1 Day Ago</a></center></B></TD>';
else
	echo '<TD ><b><center><a href="?subtopic=onlinetime&type=1">1 Day Ago</a></center></B></TD>';
if(empty($type))
	echo '<TD><b><center><a href="?subtopic=onlinetime">Today</a></center></B></TD>';
else
	echo '<TD ><b><center><a href="?subtopic=onlinetime">Today</a></center></B></TD>';
echo '</TR>';
$number_of_rows = 1;
if($znotePlayers)
foreach($znotePlayers as $player)
{	
	echo '<td><center>'. $number_of_rows . '.</center></td>';
	echo '<td><a href="characterprofile.php?name=' .$player['name']. '">' .$player['name']. '</a>';
	echo '<br> ' .$player['level']. ' '.htmlspecialchars(vocation_id_to_name($player['vocation'])).' ';
	echo '<td ><center>' .onlineTimeTotal($player['onlinetimeall']).'</td>';
	$number_of_rows++;
	echo '<td ><center>'.hours_and_minutes($player['onlinetime3']).'</center></td><td ><center>'.hours_and_minutes($player['onlinetime2']).'</center></td><td ><center>'.hours_and_minutes($player['onlinetime1']).'</center></td><td ><center>'.hours_and_minutes($player['onlinetimetoday']).'</center></td></tr>';
}
echo '</TABLE></div>';
?>
<?php
include 'layout/overall/footer.php';
?>

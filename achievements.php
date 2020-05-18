<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
if ($config['Ach'] == true) {
?>
<center><h3>Achievements on <?php echo $config['site_title'] ?></h3></center>
<div class="panel-body">
<table class="table table-striped table-bordered table-condensed">
<tr>
<td width="10%">Grade</td>
<td width="17%">Name</td>
<td>Description</td>
<td width="7%">Secret</td>
<td width="2%">Points</td>
</tr>
<style>
#wtf {
   margin-left:0px;
   
}
</style>
<tr>
<?php
foreach ($config['achievements'] as $key => $achName) {
	// Set defaults
	if (!isset($achName['secret'])) $achName['secret'] = false;
	if (!isset($achName['img'])) $achName['img'] = 'https://i.imgur.com/ZqWp1TE.png';
	
	if (($achName['points'] >= 1) and ($achName['points'] <= 3) and (!$achName['img']))  {
		echo '<td><center><img id="wtf" src="https://i.imgur.com/TUCGsr3.gif"></center></td>';
	
	} elseif (($achName['points'] >= 4) and ($achName['points'] <= 6) and (!$achName['img'])) {
		echo '<td><center><img id="wtf" src="https://i.imgur.com/TUCGsr3.gif"><img id="wtf" src="https://i.imgur.com/TUCGsr3.gif"></center></td>';
	
	} elseif (($achName['points'] >= 7) and ($achName['points'] <= 9) and (!$achName['img'])) {
		echo '<td><center><img id="wtf" src="https://i.imgur.com/TUCGsr3.gif"><img id="wtf" src="https://i.imgur.com/TUCGsr3.gif"><img id="wtf" src="https://i.imgur.com/TUCGsr3.gif"></center></td>';
	
	} elseif (($achName['points'] >= 10) and (!$achName['img'])) {
		echo '<td><center><img id="wtf" src="https://i.imgur.com/TUCGsr3.gif"><img id="wtf" src="https://i.imgur.com/TUCGsr3.gif"><img id="wtf" src="https://i.imgur.com/TUCGsr3.gif"></center></td>';
	
	} else {
		echo '<td><img id="wtf" src="' .$achName['img']. '"><br><br></td>';
	}
	echo '<td>' .$achName[0]. '</td>';
	echo '<td>' .$achName[1]. '</td>';
	if ($achName['secret'] == true) {
		echo '<td><img id="wtf" src="https://i.imgur.com/NbPRl7b.gif"></td>';
		echo '<td>'. $achName['points'] .'</td>';
	} else {
		echo '<td></td><td>'. $achName['points'] .'</td>';
	}
	echo '</tr>';		
}
?>
</table>
</div>
</div>
<?php
include 'layout/overall/footer.php';
}
else{
echo 'This page has been disabled, this page can be enabled at config';
}
?>

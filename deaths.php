<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
$cache = new Cache('engine/cache/deaths');
if ($cache->hasExpired()) {
	
	if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10') {
		$deaths = fetchLatestDeaths();
	} else if ($config['ServerEngine'] == 'TFS_03' || $config['ServerEngine'] == 'OTHIRE') {
		$deaths = fetchLatestDeaths_03(30);
	}
	$cache->setContent($deaths);
	$cache->save();
} else {
	$deaths = $cache->load();
}
if ($deaths) {
?>
<h1>Latest Deaths</h1>
<table id="deathsTable" class="table table-striped">
	<tr class="yellow">
		<th>Victim</th>
		<th>Time</th>
		<th>Killer</th>
	</tr>
	<?php foreach ($deaths as $death) { 
		echo '<tr>';
		echo "<td>At level ". $death['level'] .": <a href='characterprofile.php?name=". $death['victim'] ."'>". $death['victim'] ."</a></td>";
		echo "<td>". getClock($death['time'], true) ."</td>";
		if ($death['is_player'] == 1) echo "<td>Player: <a href='characterprofile.php?name=". $death['killed_by'] ."'>". $death['killed_by'] ."</a></td>";
		else if ($death['is_player'] == 0) {
			if ($config['ServerEngine'] == 'TFS_03') echo "<td>Monster: ". ucfirst(str_replace("a ", "", $death['killed_by'])) ."</td>";
			else echo "<td>Monster: ". ucfirst($death['killed_by']) ."</td>";
		}
		else echo "<td>". $death['killed_by'] ."</td>";
		echo '</tr>';
	} ?>
</table>
<?php
} else echo 'No deaths exist.';
include 'layout/overall/footer.php'; ?>
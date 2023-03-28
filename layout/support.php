<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
?><h1>Support in-game</h1><?php
$cache = new Cache('engine/cache/support');
if ($cache->hasExpired()) {
	// Fetch all staffs in-game.
	if ($config['ServerEngine'] == 'TFS_03') {
		$staffs = support_list03();
	} else $staffs = support_list();
	// Fetch group ids and names from config.php
	$groups = $config['ingame_positions'];
	// Loops through groups, separating each group element into an ID variable and name variable
	foreach ($groups as $group_id => $group_name) {
		// Loops through list of staffs
		if (!empty($staffs))
		foreach ($staffs as $staff) {
			if ($staff['group_id'] == $group_id) $srtGrp[$group_name][] = $staff;
		}
	}
	if (!empty($srtGrp)) {
		$cache->setContent($srtGrp);
		$cache->save();
	}
} else {
	$srtGrp = $cache->load();
}
$writeHeader = true;
if (!empty($srtGrp)) {
	foreach (array_reverse($srtGrp) as $grpName => $grpList) {
		?>
		<table id="supportTable" class="table table-striped">
			<?php if ($writeHeader) {
			$writeHeader = false; ?>
			<tr class="yellow">
				<th width="30%">Group</th>
				<th width="40%">Name</th>
				<th width="30%">Status</th>
			</tr>
			<?php
			}
			foreach ($grpList as $char) {
				if ($char['name'] != $config['website_char']) {
					echo '<tr>';
					echo "<td width='30%'>". $grpName ."</td>";
					echo '<td width="40%"><a href="characterprofile.php?name='. $char['name'] .'">'. $char['name'] .'</a></td>';
					echo "<td width='30%'>". online_id_to_name($char['online']) ."</td>";
					echo '</tr>';
				}
			}
			?>
		</table>
		<?php
	}
}
echo'</table>'; include 'layout/overall/footer.php'; ?>
<table id="questTable">
	<?php
	$completed = '<font color="green">[Completed]</font>';
	$notstarted = '';
	function Progress($min, $max, $design = '<font color="orange">[x%]</font>') {
		$design = explode("x%",$design);
		$percent = ($min / $max) * 100;
		return $design[0] . $percent . $design[1];
	}
	$quests = array(
		// Simple quests
		'Bearslayer' => 1050,
		'Sword Quest' => 1337,

		// Advanced quest with progress par:
		'Postman Quest' => array(
			1338,
			3,
		),
	);
	?>
	<tr class="yellow">
		<td>Quest Name</td>
		<td>Status</td>
	</tr>
	<?php
	// Rolling through quests
	foreach ($quests as $key => $quest) {

		// Is quest NOT an array (advanced quest?)
		if (!is_array($quest)) {
			// Query to find quest results
			$query = mysql_select_single("SELECT `value` FROM `player_storage` WHERE `key`='$quest' AND `player_id`='$user_id' AND `value`='1' LIMIT 1;");

			if ($query !== false) $quest = $completed;
			else $quest = $notstarted;

		} else {
			$query = mysql_select_single("SELECT `value` FROM `player_storage` WHERE `key`='".$quest[0]."' AND `player_id`='$user_id' AND `value`>'0' LIMIT 1;");
			if (!$query) $quest = $notstarted;
			else {
				if ($query['value'] >= $quest[1]) $quest = $completed;
				else $quest = Progress($query['value'], $quest[1]);
			}
		}
		?>
		<tr>
			<td><?php echo $key; ?></td>
			<td><?php echo $quest; ?></td>
		</tr>
		<?php
	}
	?>
</table>
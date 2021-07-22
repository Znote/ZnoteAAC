<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

if ($config['log_ip'])
	znote_visitor_insert_detailed_data(3);

// Fetch values
$querystring_id = &$_GET['id'];
$townid = ($querystring_id) ? (int)$_GET['id'] : $config['houseConfig']['HouseListDefaultTown'];
$towns = $config['towns'];

$order = &$_GET['order'];
$type = &$_GET['type'];

// Create Search house box
?>
<form action="" method="get" class="houselist">
	<table>
		<tr>
			<td>Town</td>
			<td>Order</td>
			<td>Sort</td>
		</tr>
		<tr>
			<td>
				<select name="id">
				<?php
				foreach ($towns as $id => $name)
					echo '<option value="'. $id .'"' . ($townid != $id ?: ' selected') . '>'. $name .'</option>';
				?>
				</select>
			</td>
			<td>
				<select name="order">
				<?php
				$order_allowed = array('id', 'name', 'size', 'beds', 'rent', 'owner');
				foreach($order_allowed as $o)
					echo '<option value="' . $o . '"' . ($o != $order ?: ' selected') . '>' . ucfirst($o) . '</option>';
				?>
				</select>
			</td>
			<td>
				<select name="type">
				<?php
				$type_allowed = array('desc', 'asc');
				foreach($type_allowed as $t)
					echo '<option value="' . $t . '"' . ($t != $type ?: ' selected') . '>' . ($t == 'desc' ? 'Descending' : 'Ascending') .'</option>';
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<input type="submit" value="Fetch houses"/>
			</td>
		</tr>
	</table>
</form>
<?php
if(!in_array($order, $order_allowed))
	$order = 'id';

if(!in_array($type, $type_allowed))
	$type = 'desc';

// Create or fetch data from cache
$cache = new Cache('engine/cache/houses/houses-' . $order . '-' . $type);
$houses = array();

if ($cache->hasExpired()) {

	$houses = mysql_select_multi("
		SELECT 
			`id`, `owner`, `paid`, `warnings`, `name`, `rent`, `town_id`, 
			`size`, `beds`, `bid`, `bid_end`, `last_bid`, `highest_bidder` 
		FROM `houses` 
		ORDER BY {$order} {$type};
	");

	if ($houses !== false) {
		// Fetch player names
		$playerlist = array();

		foreach ($houses as $h)
			if ($h['owner'] > 0)
				$playerlist[] = $h['owner'];

		if (!empty($playerlist)) {
			$ids = join(',', $playerlist);
			$tmpPlayers = mysql_select_multi("SELECT `id`, `name` FROM players WHERE `id` IN ($ids);");

			// Sort $tmpPlayers by player id
			$tmpById = array();
			foreach ($tmpPlayers as $p)
				$tmpById[$p['id']] = $p['name'];

			for ($i = 0; $i < count($houses); $i++)
				if ($houses[$i]['owner'] > 0)
					$houses[$i]['ownername'] = $tmpById[$houses[$i]['owner']];
		}

		$cache->setContent($houses);
		$cache->save();
	}
} else
	$houses = $cache->load();

if ($houses !== false || !empty($houses)) {
	// Intialize stuff
	//data_dump($houses, false, "House data");
	?>
	<table id="housetable">
		<tr class="yellow">
			<th>Name</th>
			<th>Size</th>
			<th>Beds</th>
			<th>Rent</th>
			<th>Owner</th>
			<th>Town</th>
		</tr>
		<?php
		foreach ($houses as $house) {
			if ($house['town_id'] == $townid) {
				?>
				<tr>
					<td><?php echo "<a href='house.php?id=". $house['id'] ."'>". $house['name'] ."</a>"; ?></td>
					<td><?php echo $house['size']; ?></td>
					<td><?php echo $house['beds']; ?></td>
					<td><?php echo $house['rent']; ?></td>
					<?php
					// Status:
					if ($house['owner'] != 0)
						echo "<td><a href='characterprofile.php?name=". $house['ownername'] ."' target='_BLANK'>". $house['ownername'] ."</a></td>";
					else
						echo ($house['highest_bidder'] == 0 ? '<td>None</td>' : '<td><b>Selling</b></td>');
					?>
					<td><?php
					$town_name = &$towns[$house['town_id']];
					echo ($town_name ? $town_name : 'Specify town id ' . $house['town_id'] . ' name in config.php first.');
					?></td>
				</tr>
				<?php
			}
		}
		?>
	</table>

	<?php
} else {
	echo "<h1>Failed to fetch data from sql->houses table.</h1><p>Is the table empty?</p>";
}

include 'layout/overall/footer.php'; ?>

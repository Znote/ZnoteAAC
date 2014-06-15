<?php require_once 'engine/init.php'; include 'layout/overall/header.php';

if ($config['log_ip']) {
	znote_visitor_insert_detailed_data(3);
}

// Fetch highscore type
$type = (isset($_GET['type'])) ? (int)getValue($_GET['type']) : 7;
if ($type > 9) $type = 7;

// Fetch highscore page
$page = getValue(@$_GET['page']);
if (!$page || $page == 0) $page = 1;
else $page = (int)$page;

$highscore = $config['highscore'];

$rows = $highscore['rows'];
$rowsPerPage = $highscore['rowsPerPage'];

function skillName($type) {
	$types = array(
		1 => "Club",
		2 => "Sword",
		3 => "Axe",
		4 => "Distance",
		5 => "Shield",
		6 => "Fish",
		7 => "Experience", // Hardcoded
		8 => "Magic Level", // Hardcoded
		9 => "Fist", // Since 0 returns false I will make 9 = 0. :)
	);
	return $types[(int)$type];
}

function pageCheck($index, $page, $rowPerPage) {
	return ($index < ($page * $rowPerPage) && $index >= ($page * $rowPerPage) - $rowPerPage) ? true : false;
}

$cache = new Cache('engine/cache/highscores');
if ($cache->hasExpired()) {
	$scores = fetchAllScores($rows, $config['TFSVersion'], $highscore['ignoreGroupId']);
	
	$cache->setContent($scores);
	$cache->save();
} else {
	$scores = $cache->load();
}

if ($scores) {
	?>
	<h1>Ranking for <?php echo skillName($type); ?>.</h1>
	<form action="" method="GET">
		<select name="type">
			<option value="7" <?php if ($type == 7) echo "selected"; ?>>Experience</option>
			<option value="8" <?php if ($type == 8) echo "selected"; ?>>Magic</option>
			<option value="5" <?php if ($type == 5) echo "selected"; ?>>Shield</option>
			<option value="2" <?php if ($type == 2) echo "selected"; ?>>Sword</option>
			<option value="1" <?php if ($type == 1) echo "selected"; ?>>Club</option>
			<option value="3" <?php if ($type == 3) echo "selected"; ?>>Axe</option>
			<option value="4" <?php if ($type == 4) echo "selected"; ?>>Distance</option>
			<option value="6" <?php if ($type == 6) echo "selected"; ?>>Fish</option>
			<option value="9" <?php if ($type == 9) echo "selected"; ?>>Fist</option>
		</select>
		<select name="page">
			<?php
			$pages = (int)($highscore['rows'] / $highscore['rowsPerPage']) + 1;
			for ($i = 0; $i < $pages; $i++) {
				$x = $i + 1;
				if ($x == $page) echo "<option value='".$x."' selected>Page: ".$x."</option>";
				else echo "<option value='".$x."'>Page: ".$x."</option>";
			}
			?>
		</select>
		<input type="submit" value=" View " class="btn btn-info">
	</form>
	<table id="highscoresTable" class="table table-striped table-hover">
		<tr class="yellow">
			<td>Rank</td>
			<td>Name</td>
			<td>Vocation</td>
			<td>Level</td>
			<?php if ($type === 7) echo "<td>Points</td>"; ?>
		</tr>
		<?php
		for ($i = 0; $i < count($scores[$type]); $i++) {
			if (pageCheck($i, $page, $rowsPerPage)) {
				?>
				<tr>
					<td><?php echo $i+1; ?></td>
					<td><a href="characterprofile.php?name=<?php echo $scores[$type][$i]['name']; ?>"><?php echo $scores[$type][$i]['name']; ?></a></td>
					<td><?php echo vocation_id_to_name($scores[$type][$i]['vocation']); ?></td>
					<td><?php echo $scores[$type][$i]['value']; ?></td>
					<?php if ($type === 7) echo "<td>". $scores[$type][$i]['experience'] ."</td>"; ?>
				</tr>
				<?php
			}
		}
		?>
	</table>
	<?php
}
include 'layout/overall/footer.php'; ?>
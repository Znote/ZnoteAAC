<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
if ($config['log_ip']) {
	znote_visitor_insert_detailed_data(3);
}
if (empty($_POST) === false && $config['TFSVersion'] === 'TFS_03') {
	
	#if ($_POST['token'] == $_SESSION['token']) {
	
	/* Token used for cross site scripting security */
	if (isset($_POST['token']) && Token::isValid($_POST['token'])) {
		
		$townid = (int)$_POST['selected'];
		$cache = new Cache('engine/cache/houses');
		$array = array();
		if ($cache->hasExpired()) {
			$tmp = fetchAllHouses_03();
			$cache->setContent($tmp);
			$cache->save();
			
			foreach ($tmp as $t) {
				if ($t['town'] == $townid) $array[] = $t;
			}
			$array = isset($array) ? $array : false;
		} else {
			$tmp = $cache->load();
			foreach ($tmp as $t) {
				if ($t['town'] == $townid) $array[] = $t;
			}
			$array = isset($array) ? $array : false;
		}
		
		// Design and present the list
		if ($array) {
			?>
			<h2>
				<?php echo ucfirst(town_id_to_name($townid)); ?> house list.
			</h2>
			<table id="housesTable" class="table table-striped">
				<tr class="yellow">
					<th>Name:</th>
					<th>Size:</th>
					<th>Doors:</th>
					<th>Beds:</th>
					<th>Price:</th>
					<th>Owner:</th>
					
				</tr>
					<?php
					foreach ($array as $value) {
						// start foreach
						echo '<tr>';
						echo "<td>". $value['name'] ."</td>";
						echo "<td>". $value['size'] ."</td>";
						echo "<td>". $value['doors'] ."</td>";
						echo "<td>". $value['beds'] ."</td>";
						echo "<td>". $value['price'] ."</td>";
						if ($value['owner'] == 0) echo "<td>None</td>";
						else {
							$data = user_character_data($value['owner'], 'name');
							echo '<td><a href="characterprofile.php?name='. $data['name'] .'">'. $data['name'] .'</a></td>';
						}
						echo '</tr>';
						// end foreach
					}
					?>
			</table>
			<?php
		} else {
			echo 'Empty list, it appears no houses are listed in this town.';
		}
		//Done.
	} else {
		echo 'Token appears to be incorrect.<br><br>';
		//Token::debug($_POST['token']);
		echo 'Please clear your web cache/cookies <b>OR</b> use another web browser<br>';
	}
} else {
	if (empty($_POST) === true && $config['TFSVersion'] === 'TFS_03') {
		if ($config['allowSubPages']) header('Location: sub.php?page=houses');
		else echo 'Sub page system disabled.';
	} else if ($config['TFSVersion'] === 'TFS_02') {
		$house = $config['house'];
		if (!is_file($house['house_file'])) {
			echo("<h3>House file not found</h3><p>FAILED TO LOCATE/READ FILE AT:<br><font color='red'>". $house['house_file'] ."</font><br><br>LINUX users: Make sure www-data have read access to file.<br>WINDOWS users: Learn to write correct file path.</p>");
			exit();
		}

		// Load and cache SQL house data:
		$cache = new Cache('engine/cache/houses/sqldata');
		if ($cache->hasExpired()) {
			$house_query = mysql_select_multi('SELECT `players`.`name`, `houses`.`id` FROM `players`, `houses` WHERE `houses`.`owner` = `players`.`id`;');
			
			$cache->setContent($house_query);
			$cache->save();
		} else {
			$house_query = $cache->load();
		}

		$sqmPrice = $house['price_sqm'];
		$house_load = simplexml_load_file($house['house_file']);
		if ($house_query !== false && $house_load !== false) {
			?>
			<h2>House list</h2>
			<table>
				<tr class="yellow">
					<td><b>House</b></td>
					<td><b>Location</b></td>
					<td><b>Owner</b></td>
					<td><b>Size</b></td><td><b>Rent</b></td>
				</tr>

				<?php
				//execute code.
				foreach($house_query as $row) {
					$house_info[(int)$row['id']] = '<a href="characterprofile.php?name='. $row['name'] .'">'. $row['name'] .'</a>';
				}
				foreach ($house_load as $house_fetch){
					$house_price = (int)$house_fetch['size'] * $sqmPrice;
					?>
					<tr>
						<td><?php echo htmlspecialchars($house_fetch['name']); ?></td>
						<td>
							<?php
							if (isset($config['towns'][(int)$house_fetch['townid']])) echo htmlspecialchars($config['towns'][(int)$house_fetch['townid']]);
							else echo '(Missing town)';
							?>
						</td>
						<td>
							<?php
							if (isset($house_info[(int)$house_fetch['houseid']])) echo $house_info[(int)$house_fetch['houseid']];
							else echo 'None [Available]';
							?>
						</td>
						<td><?php echo $house_fetch['size']; ?></td>
						<td><?php echo $house_price; ?></td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		} else echo '<p><font color="red">Something is wrong with the cache.</font></p>';
	}
}
include 'layout/overall/footer.php'; ?>
<?php require_once 'engine/init.php';

if ($config['log_ip']) {
	znote_visitor_insert_detailed_data(3);
}

function fetchAllScoresh($rows, $tfs, $g, $v = -1) {
	// Return scores ordered by type and vocation (if set)
	$data = array();
	if ($tfs == 'TFS_10') {

		// Generate SQL WHERE-clause for vocation if $v is set
		$v = ($v > -1) ? 'AND `vocation` = '. intval($v) : NULL;

		$data[1] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `skill_club` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_club` DESC LIMIT 0, $rows;");
		$data[2] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `skill_sword` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_sword` DESC LIMIT 0, $rows;");
		$data[3] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `skill_axe` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_axe` DESC LIMIT 0, $rows;");
		$data[4] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `skill_dist` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_dist` DESC LIMIT 0, $rows;");
		$data[5] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `skill_shielding` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_shielding` DESC LIMIT 0, $rows;");
		$data[6] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `skill_fishing` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_fishing` DESC LIMIT 0, $rows;");
		$data[7] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `experience`, `level` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `experience` DESC LIMIT 0, $rows;");
		$data[8] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `maglevel` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `maglevel` DESC LIMIT 0, $rows;");
		$data[9] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `skill_fist` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_fist` DESC LIMIT 0, $rows;");
	} else {

		// Generate SQL WHERE-clause for vocation if $v is set
		$v = ($v > -1) ? 'AND `vocation` = '. intval($v) : NULL;

		$data[9] = mysql_select_multi("SELECT `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 0 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
		$data[1] = mysql_select_multi("SELECT `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 1 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
		$data[2] = mysql_select_multi("SELECT `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 2 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
		$data[3] = mysql_select_multi("SELECT `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 3 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
		$data[4] = mysql_select_multi("SELECT `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 4 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
		$data[5] = mysql_select_multi("SELECT `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 5 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
		$data[6] = mysql_select_multi("SELECT `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 6 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
		$data[7] = mysql_select_multi("SELECT `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `id`, `name`, `vocation`, `experience`, `level` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `experience` DESC limit 0, $rows;");
		$data[8] = mysql_select_multi("SELECT `looktype`, `lookhead`, `lookbody`, `looklegs`, `lookfeet`, `id`, `name`, `vocation`, `maglevel` AS `value` FROM `players` WHERE `group_id` < $g $v ORDER BY `maglevel` DESC limit 0, $rows;");
	}
	return $data;
}

// Fetch highscore type
$type = (isset($_GET['type'])) ? (int)getValue($_GET['type']) : 7;
if ($type > 9) $type = 7;

// Fetch highscore vocation
$vocation = (isset($_GET['vocation'])) ? (int)getValue($_GET['vocation']) : -1;
if ($vocation > 8) $vocation = -1;

// Fetch highscore page
$page = getValue(@$_GET['pag']);
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
	$scores = fetchAllScoresh($rows, $config['TFSVersion'], $highscore['ignoreGroupId'], $vocation);
	
	$cache->setContent($scores);
	$cache->save();
} else {
	$scores = $cache->load();
}

if ($scores) {
	?>
		<style>
			.newstext img
			{
				max-width: 562px;
				height: auto;
				margin: 0 5px;
			}
			.pagination
			{
				text-align: center;
				margin: 10px 0;
			}
			.pagination a
			{
				display: inline-block;
				color: #52412f;
				font-size: 14px;
				line-height: 24px;
				height: 24px;
				width: 24px;
				font-weight: bold;
				text-align: center;
				border: 1px solid #886a4d;
				margin: 0 5px 0 0;
				border-radius: 24px;
				box-shadow: 0 2px 3px rgb(255, 214, 175) inset;
				background: #dcaf83;
				transition: opacity 0.1s linear;
			}
			.pagination a:hover
			{
				opacity: 0.7;
			}
			.pagination a.current
			{
				background: #ad1a1a;
				color: #fff;
				border: 1px solid #000000;
				box-shadow: 0 2px 3px rgb(255, 62, 62) inset;
			}
		</style>
		
	<form type="submit" action="" method="GET">
	<br>
	<center>Skill: <input type="hidden" name="page" value="highscores"/>
		<select style="vertical-align:middle;" name="type">
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
		&nbsp;&nbsp;Vocation: <select style="vertical-align:middle;" name="vocation">
			<option value="-1" <?php if ($vocation < 0) echo "selected"; ?>>Any vocation</option>

			<?php
			foreach (config('vocations') as $v_id => $v_name) {
				$selected = ($vocation == $v_id) ? " selected" : NULL;

				echo '<option value="'. $v_id .'"'. $selected .'>'. $v_name .'</option>';
			}
			?>
		</select>
		
		 <input type="submit" class="hover" value=""
    style="background-image: url(layout/tibia_img/sbutton_submit.gif); vertical-align:middle; border: solid 0px #000000; width: 120px; height: 18px;" />
		</center>
	</form>
	
	
		<br><center><span class="pagination">
			<?php
			$pages = ceil(min(($highscore['rows'] / $highscore['rowsPerPage']), (count($scores[$type]) / $highscore['rowsPerPage'])));
			for ($i = 0; $i < $pages; $i++) {
				$x = $i + 1;
				if ($x == $page) echo "<a href=\"?".$_SERVER['QUERY_STRING']."&pag=".$x."\" class=\"current\">".$x."</a>";
				else echo "<a href=\"?".$_SERVER['QUERY_STRING']."&pag=".$x."\">".$x."</a>";
			}
			?>
		</span></center>
		
		
	<table id="highscoresTable" cellpadding="4" class="stripped">
		<tr>	
			<th colspan="7">Ranking for <?php echo skillName($type) .", ". (($vocation < 0) ? 'any vocation' : vocation_id_to_name($vocation)) ?></th>
		</tr>
		<tr>
			<td width="8%"><center>#</center></td>
			<td width="8%"><center><strong>outfit</strong></center></td>
			<?php if ($config['country_flags'] === true) echo'<td width="5%"><center><strong>flag</strong></center></td>'; ?>
			<td><strong>Name</strong></td>
			<td><center><strong>Vocation</strong></center></td>
			<td width="15%"><center><strong>Level</strong></center></td>
			<?php if ($type === 7) echo "<td width=\"15%\"><center><strong>Points</strong></center></td>"; ?>
		</tr>
		<?php
		for ($i = 0; $i < count($scores[$type]); $i++) {
			if ($scores[$type] === false) {
				?>
				<tr>
					<td colspan="7">Nothing to show here yet.</td>
				</tr>
				<?php
			} else {
				if (pageCheck($i, $page, $rowsPerPage)) {
					$profile_data = user_character_data($scores[$type][$i]['id'], 'account_id');

					$account_data = user_znote_account_data($profile_data['account_id'], 'flag');
					if ($config['country_flags'] === true) $flag = '<td><center><img src="flags/' . $account_data['flag'] . '.png"></center></td>';
					else $flag = '';
					?>
					<tr>
						<td><center><?php echo $i+1; ?>.</center></td>
						<td>
						<?php
							echo '<div style="position:relative; left:-32px; top:-32px;width: 32px;height:32px;">
							<div style="background-image: url(layout/outfitter/outfit.php?id='.$scores[$type][$i]['looktype'].'&head='.$scores[$type][$i]['lookhead'].'&body='.$scores[$type][$i]['lookbody'].'&legs='.$scores[$type][$i]['looklegs'].'&feet='.$scores[$type][$i]['lookfeet'].');
								width:64px;height:64px;position:absolute;background-repeat:no-repeat;background-position:right bottom;">
							</div>';
						?>
					
						</td>
						<?php echo $flag; ?>
						<td><strong><a href="characterprofile.php?name=<?php echo $scores[$type][$i]['name']; ?>"><?php echo $scores[$type][$i]['name']; ?></a></strong></td>
						<td><center><?php echo vocation_id_to_name($scores[$type][$i]['vocation']); ?></center></td>
						<td><center><?php echo $scores[$type][$i]['value']; ?></center></td>
						<?php if ($type === 7) echo "<td><center>". $scores[$type][$i]['experience'] ."</center></td>"; ?>
					</tr>
					<?php
				}
			}
		}
		?>
	</table>
		<center><span class="pagination">
			<?php
			$pages = ceil(min(($highscore['rows'] / $highscore['rowsPerPage']), (count($scores[$type]) / $highscore['rowsPerPage'])));
			for ($i = 0; $i < $pages; $i++) {
				$x = $i + 1;
				if ($x == $page) echo "<a href=\"?".$_SERVER['QUERY_STRING']."&pag=".$x."\" class=\"current\">".$x."</a>";
				else echo "<a  href=\"?".$_SERVER['QUERY_STRING']."&pag=".$x."\">".$x."</a>";
			}
			?>
		</span></center>
	<?php
}
?>

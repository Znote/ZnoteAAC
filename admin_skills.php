<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; 
protect_page();
admin_only($user_data);

// PREP: Create a function that echos player skills
function playerSkill($skills, $id) {
	if (!$skills) return 0;
	else {
		return $skills[$id]['value'];
	}
}

// UPDATE SKILLS POST
if (isset($_POST['pid']) && (int)$_POST['pid'] > 0) {
	$pid = (int)$_POST['pid'];
	if ($config['ServerEngine'] != 'TFS_10') $status = user_is_online($pid);
	else $status = user_is_online_10($pid);

	if (!$status) {
		// New player level
		$level = (int)$_POST['level'];
		
		// Fetch stat gain for vocation
		$statgain = $config['vocations_gain'][(int)$_POST['vocation']];
		$playercnf = $config['player'];

		/*
		if ((int)$_POST['vocation'] !== 0) {
			// Fetch base level and stats:
			$baselevel = $config['level'];
			$basehealth = $config['health'];
			$basemana = $config['mana'];
			$basecap = $config['cap'];
		} else { // No vocation stats
			// Fetch base level and stats:
			$baselevel = $config['nvlevel'];
			$basehealth = $config['nvHealth'];
			$basemana = $config['nvMana'];
			$basecap = $config['nvCap'];
		}
		*/
		
		$LevelsFromBase = $level - $playercnf['base']['level'];
		$newhp = $playercnf['base']['health'] + ($statgain['hp'] * $LevelsFromBase);
		$newmp = $playercnf['base']['mana'] + ($statgain['mp'] * $LevelsFromBase);
		$newcap = $playercnf['base']['cap'] + ($statgain['cap'] * $LevelsFromBase);

		// Calibrate hp/mana/cap
		if ($config['ServerEngine'] != 'TFS_10') {
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['fist'] ."' WHERE `player_id`='$pid' AND `skillid`='0' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['club'] ."' WHERE `player_id`='$pid' AND `skillid`='1' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['sword'] ."' WHERE `player_id`='$pid' AND `skillid`='2' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['axe'] ."' WHERE `player_id`='$pid' AND `skillid`='3' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['dist'] ."' WHERE `player_id`='$pid' AND `skillid`='4' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['shield'] ."' WHERE `player_id`='$pid' AND `skillid`='5' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['fish'] ."' WHERE `player_id`='$pid' AND `skillid`='6' LIMIT 1;");
mysql_update("UPDATE `players` SET `maglevel`='". (int)$_POST['magic'] ."' WHERE `id`='$pid' LIMIT 1;");
mysql_update("UPDATE `players` SET `vocation`='". (int)$_POST['vocation'] ."' WHERE `id`='$pid' LIMIT 1;");
mysql_update("UPDATE `players` SET `level`='". $level ."' WHERE `id`='$pid' LIMIT 1;");
mysql_update("UPDATE `players` SET `experience`='". level_to_experience($level) ."' WHERE `id`='$pid' LIMIT 1;");
// Update HP/mana/cap accordingly to level & vocation
mysql_update("UPDATE `players` SET `health`='". $newhp ."', `healthmax`='". $newhp ."', `mana`='". $newmp ."', `manamax`='". $newmp ."', `cap`='". $newcap ."' WHERE `id`='$pid' LIMIT 1;");
		} else {
			mysql_update("UPDATE `players` SET `health`='". $newhp ."', `healthmax`='". $newhp ."', `mana`='". $newmp ."', `manamax`='". $newmp ."', `cap`='". $newcap ."', `vocation`='". (int)$_POST['vocation'] ."', `skill_fist`='". (int)$_POST['fist'] ."', `skill_club`='". (int)$_POST['club'] ."', `skill_sword`='". (int)$_POST['sword'] ."', `skill_axe`='". (int)$_POST['axe'] ."', `skill_dist`='". (int)$_POST['dist'] ."', `skill_shielding`='". (int)$_POST['shield'] ."', `skill_fishing`='". (int)$_POST['fish'] ."', `maglevel`='". (int)$_POST['magic'] ."', `level`='". $level ."', `experience`='". level_to_experience($level) ."' WHERE `id`='$pid' LIMIT 1;");
		}
?>
<h1>Player skills updated!</h1>
<?php
	} else {
		?>
		<font color="red" size="7">Player must be offline!</font>
		<?php
	}
}

// Stage 1: Fetch name
if (isset($_GET['name'])) {
	$name = getValue($_GET['name']);
} else $name = false;
//if (isset($_POST['name'])) $name = getValue($_POST['name']);

// Stage 2: Fetch user id and skills
$skills = false;
$pid = 0;
if ($name !== false) {
	if (user_character_exist($name)) {
		$pid = user_character_id($name);

		if ($config['ServerEngine'] != 'TFS_10') {
			$skills = mysql_select_multi("SELECT `value` FROM `player_skills` WHERE `player_id`='$pid' LIMIT 7;");
			$player = mysql_select_single("SELECT `maglevel`, `level`, `vocation` FROM `players` WHERE `id`='$pid' LIMIT 1;");
			$skills[] = array('value' => $player['maglevel']);
			$skills[] = array('value' => $player['level']);
			$skills[] = array('value' => $player['vocation']);
		} else {
			$player = mysql_select_single("SELECT `skill_fist`, `skill_club`, `skill_sword`, `skill_axe`, `skill_dist`, `skill_shielding`, `skill_fishing`, `maglevel`, `level`, `vocation` FROM `players` WHERE `id`='$pid' LIMIT 1;");
			$skills = array(
				0 => array('value' => $player['skill_fist']),
				1 => array('value' => $player['skill_club']),
				2 => array('value' => $player['skill_sword']),
				3 => array('value' => $player['skill_axe']),
				4 => array('value' => $player['skill_dist']),
				5 => array('value' => $player['skill_shielding']),
				6 => array('value' => $player['skill_fishing']),
				7 => array('value' => $player['maglevel']),
				8 => array('value' => $player['level']),
				9 => array('value' => $player['vocation'])
			);
		}

		//data_dump($skills, false, "Player skills");
	} else $name = false;
}

?>
<form action="" method="<?php if (!$name) echo "get"; else echo "post";?>">
	<input type="hidden" name="pid" value="<?php echo $pid; ?>">
	<table class="table">
		<tr class="yellow">
			<td colspan="2"><center><font size="6">Player skills administration</font></center></td>
		</tr>
		<tr>
			<td>
				<input name="name" type="text" placeholder="Character name" <?php if ($name !== false) echo "value='$name' disabled";?>>
				<br><br>
				Vocation:<br>
				<select name="vocation" <?php if (!$name) echo "disabled";?>>
					<?php
					$vocations = $config['vocations'];
					foreach ($vocations as $vid => $vname) {
						?>
						<option value="<?php echo $vid; ?>" <?php if ($vid == playerSkill($skills, 9)) echo "selected"?> ><?php echo $vname['name']; ?></option>
						<?php
					}
					?>
				</select>
				<br><br>
				Fist fighting:<br>
				<input name="fist" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 0); ?>">
				<br><br>
				Club fighting:<br>
				<input name="club" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 1); ?>">
				<br><br>
				Sword fighting:<br>
				<input name="sword" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 2); ?>">
				<br><br>
				Axe fighting:<br>
				<input name="axe" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 3); ?>">
				<br><br>
			</td>
			<td>
				Dist fighting:<br>
				<input name="dist" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 4); ?>">
				<br><br>
				Shield fighting:<br>
				<input name="shield" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 5); ?>">
				<br><br>
				Fish fighting:<br>
				<input name="fish" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 6); ?>">
				<br><br>
				Level:<br>
				<input name="level" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 8); ?>">
				<br><br>
				Magic level:<br>
				<input name="magic" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 7); ?>">
				<br><br>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php
					if (!$name) {
						?>
						<input class="btn btn-primary" type="submit" value="Fetch character skills info">
						<?php
					} else {
						?>
						<input class="btn btn-success" type="submit" value="UPDATE SKILLS">
						<?php
					}
				?>
			</td>
		</tr>
	</table>
	<a href="admin_skills.php">Reset fields / search new character</a>
</form>
<?php
// end
 include 'layout/overall/footer.php'; ?>
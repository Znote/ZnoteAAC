<?php require_once 'engine/init.php'; 
if ($config['require_login']['guildwars']) protect_page();
if ($config['log_ip']) znote_visitor_insert_detailed_data(3);
if ($config['guildwar_enabled'] === false) {
	header('Location: guilds.php');
	exit();
}
$isOtx = ($config['CustomVersion'] == 'OTX') ? true : false;
include 'layout/overall/header.php';

if (!empty($_GET['warid'])) {
	$warid = (int)$_GET['warid']; // Sanitizing GET.
	
	if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'OTHIRE' || $config['ServerEngine'] == 'TFS_10') $war = get_guild_war($warid);
	else if ($config['ServerEngine'] == 'TFS_03') $war = get_guild_war03($warid);
	else die("Can't recognize TFS version. It has to be either TFS_02 or TFS_03. Correct this in config.php");
	
	if ($war != false) {
		// Kills data for this specific war entry
		if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'OTHIRE' || $config['ServerEngine'] == 'TFS_10') $kills = get_war_kills($warid);
		else if ($config['ServerEngine'] == 'TFS_03') $kills = get_war_kills03($warid);
		?>
		<h1><?php echo $war['name1']; ?> - VERSUS - <?php echo $war['name2']; ?></h1>
		
		<?php
		// Collecting <ul> data:
		$guild1 = $war['guild1'];
		$g1c = 0; // kill count
		
		$guild2 = $war['guild2'];
		$g2c = 0; // kill count
		
		if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'OTHIRE' || $config['ServerEngine'] == 'TFS_10') {
			foreach (($kills ? $kills : array()) as $kill) {
				if ($kill['killerguild'] == $guild1)
					$g1c++;
				else
					$g2c++;
			}
			
			$green = false;
			if ($g1c > $g2c) {
				$leading = $war['name1'];
				$green = true;
			} else if ($g2c > $g1c) $leading = $war['name2'];
			else $leading = "Tie";
		}
		?>
		<ul class="war_list">
			<li>
				War status: <?php echo $config['war_status'][$war['status']]; ?>.
			</li>
			<?php if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') { ?>
			<li>
				Leading guild: <?php echo $leading; ?>.
			</li>
			<li>
				<?php
				if ($green)
					echo 'Score: <font color="green">'. $g1c .'</font>-<font color="red">'. $g2c .'</font>';
				else if ($g1c == $g2c)
					echo 'Score: <font color="orange">'. $g1c .'</font>-<font color="orange">'. $g2c .'</font>';
				else
					echo 'Score: <font color="red">'. $g1c .'</font>-<font color="green">'. $g2c .'</font>';
				?>
			</li>
			<?php } ?>
		</ul>
		<?php
		if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') {
		?>
			<table id="guildwarTable" class="table table-striped table-hover">
				<tr class="yellow">
					<th>Killer's guild:</th>
					<th>Killer:</th>
					<th>Victim:</th>
					<th>Time:</th>
				</tr>
					<?php
					foreach (($kills ? $kills : array()) as $kill) {
						echo '<tr>';
						//echo '<td>'. get_guild_name($kill['killerguild']) .'</td>';
						echo '<td><a href="guilds.php?name='. get_guild_name($kill['killerguild']) .'">'. get_guild_name($kill['killerguild']) .'</a></td>';
						echo '<td><a href="characterprofile.php?name='. $kill['killer'] .'">'. $kill['killer'] .'</a></td>';
						echo '<td><a href="characterprofile.php?name='. $kill['target'] .'">'. $kill['target'] .'</a></td>';
						echo '<td>'. getClock($kill['time'], true) .'</td>';
						echo '</tr>';
					}
					?>
			</table>
		<?php
		}
		if ($config['ServerEngine'] == 'TFS_03') {
			// BORROWED FROM GESIOR (and ported to work on Znote AAC).
			$main_content = "";
			$deaths = gesior_sql_death($warid);
			if($deaths !== false)
			{
				//die(print_r($deaths));
				foreach($deaths as $death)
				{
					$killers = gesior_sql_killer((int)$death['id']);
					$count = count($killers); $i = 0;

					$others = false;
					$main_content .= date("j M Y, H:i", $death['date']) . " <span style=\"font-weight: bold; color: " . ($death['enemy'] == $war['guild_id'] ? "red" : "lime") . ";\">+</span>
<a href=\"characterprofile.php?name=" . urlencode($death['name']) . "\"><b>".$death['name']."</b></a> ";
					foreach($killers as $killer)
					{
						$i++;
						if($killer['is_war'] != 0)
						{
							if($i == 1)
								$main_content .= "killed at level <b>".$death['level']."</b> by ";
							else if($i == $count && $others == false)
								$main_content .= " and by ";
							else
								$main_content .= ", ";
							if($killer['player_exists'] == 0)
								$main_content .= "<a href=\"characterprofile.php?name=".urlencode($killer['player_name'])."\">";

							$main_content .= $killer['player_name'];
							if($killer['player_exists'] == 0)
								$main_content .= "</a>";
						}
						else
							$others = true;

						if($i == $count)
						{
							if($others == true)
									$main_content .= " and few others";
							$main_content .= ".<br />";
						}
					}
				}
			}
			else
				$main_content .= "<center>There were no frags on this war so far.</center>";
			echo $main_content;
			// END BORROWED FROM GESIOR
		}
	}
	
} else {
	// Display current wars.
	
	// Fetch list of wars
	if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') $wardata = get_guild_wars();
	else if ($config['ServerEngine'] == 'TFS_03') $wardata = get_guild_wars03();
	else die("Can't recognize TFS version. It has to be either TFS_02 or TFS_03. Correct this in config.php");
	//echo $wardata[0]['name1'];
	//die(var_dump($wardata));
	if ($wardata != false) {
	// kills data
	$killsdata = array(); // killsdata[guildid] => array(warid) => array info about the selected war entry
	foreach ($wardata as $wars) {
		if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') $killsdata[$wars['id']] = get_war_kills($wars['id']);
		else if ($config['ServerEngine'] == 'TFS_03') $killsdata[$wars['id']] = get_war_kills03($wars['id']);
	} 
		?>
		
		<table id="guildwarViewTable" class="table table-striped table-hover">
			<tr class="yellow">
				<th>Attacking Guild:</th>
				<th>Death Count:</th>
				<th>Defending Guild:</th>
			</tr>
				<?php
				foreach ($wardata as $wars) {
					$guild_1_kills = 0;
					$guild_2_kills = 0;
					foreach (($killsdata[$wars['id']] ? $killsdata[$wars['id']] : array()) as $kill) {

						if ($isOtx && $kill['guild_id'] == $wars['guild1'] || !$isOtx && $kill['killerguild'] == $wars['guild1'])
							$guild_1_kills++;
						else
							$guild_2_kills++;
					}
					$url = url("guildwar.php?warid=". $wars['id']);
					$guildname1 = url("guilds.php?name=". $wars['name1']);
					$guildname2 = url("guilds.php?name=". $wars['name2']);
					echo '<tr>';
					echo '<td><a href="' . $guildname1 . '">'. $wars['name1'] .'</a></td>';
					echo '<td>'. $guild_1_kills .' - ' . $guild_2_kills . '</td>';
					echo '<td><a href="' . $guildname2 . '">'. $wars['name2'] .'</a></td>';
					echo '<td><a href="' . $url . '">View</a></td>';
					echo '</tr>';
				}
				?>
		</table>

		<?php
	} else {
		echo 'There have not been any pending wars on this server.';
	}
}
// GET links sample:
// guildwar.php?warid=1
include 'layout/overall/footer.php'; ?>

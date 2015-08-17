<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<h1>Server Information</h1>
Here you will find all basic information about <?php echo '<b>'.$config['site_title'].'</b>'; ?>
<?php

// Check if PATH is correct
if (file_exists($config['server_path'].'/config.lua')) {
	// Checks OS from config.php ($config['os'])
	if ($config['os'] == 'LINUX') {
		if (file_exists($config['server_path'].'/data/XML/stages.xml')) {
			$stages_path = simplexml_load_file($config['server_path'].'/data/XML/stages.xml');
		} else {
			echo 'Couldn\'t locate stages.xml';
		}
	} else {
		if (file_exists($config['server_path'].'/data/XML/stages.xml')) {
			$stages_path = simplexml_load_file($config['server_path'].'/data/XML/stages.xml');
		} else {
			echo 'Couldn\'t locate stages.xml';
		}
	}
	
	$lua_path = parse_ini_file($config['server_path'].'/config.lua');
	echo '<h2>Server rates</h2>';
	if ($stages_path->config['enabled'] != 0) {
		// Stages are beeing used
		echo "<table class='table table-striped table-hover'>
		<tbody><tr class='yellow'><td>Minium level</td><td>Maximun level</td><td>Multiplier</td></tr>";

		foreach ($stages_path->children()->stage as $stages) {

			if($stages['maxlevel'] === NULL) {
				echo '<tr><td><center>'.$stages['minlevel'].'</center></td><td><center>Infinite</center></td><td><center>x'.$stages['multiplier'].'</center></td></tr>';
			} else {
				echo '<tr><td><center>'.$stages['minlevel'].'</center></td><td><center>'.$stages['maxlevel'].'</center></td><td><center>x'.$stages['multiplier'].'</center></td></tr>';
			}
		}
		echo '</tbody></table>';
		
	} else {
		// Not using stages
		echo "<table class='table table-striped table-hover'>
		<tbody><tr class='yellow'><td>Experience rate</td></tr>
		<tr><td><center>x".$lua_path['rateExp']."</center></td></tr>
		</tbody></table>";
	}
	echo "<table class='table table-striped table-hover'>
		<tbody><tr class='yellow'><td>Skills rate</td><td>Magic rate</td><td>Loot rate</td></tr>
		<tr><td><center>x".$lua_path['rateSkill']."</center></td><td><center>x".$lua_path['rateMagic']."</center></td><td><center>x".$lua_path['rateLoot']."</center></td></tr>
		</tbody></table>";

	// General info 
	$information = array(
		'World type'             => $lua_path['worldType'],
		'Protection level'       => $lua_path['protectionLevel'],
		'Kills to red skull'     => $lua_path['killsToRedSkull'],
		'Kills to black skull'   => $lua_path['killsToBlackSkull'],
		'Remove ammo'            => $lua_path['removeAmmoWhenUsingDistanceWeapon'],
		'Remove runes' 		 	 => $lua_path['removeChargesFromRunes'],
		'Time to decrease frags' => $lua_path['timeToDecreaseFrags'],
		'House rent period'      => $lua_path['houseRentPeriod'],
		'AFK Kickout minutes'    => $lua_path['kickIdlePlayerAfterMinutes'],
		'Location'               => $lua_path['location'],
		'Owner name'             => $lua_path['ownerName']
	);

	echo "<h2>Server general information</h2><ul>";
	foreach ($information as $key => $value) {
		echo "<li>".$key." - <b>".$value."</b></li>";
	}

	echo '</ul>';

} else {
	echo '<h1>Invliad PATH, please check your config file</h1>';
}

include 'layout/overall/footer.php'; ?>

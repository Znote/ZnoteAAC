<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
// Calculate integer values into days, hours, minutes, seconds
function toDuration($ms) {
	$duration['day'] = $ms / (24 * 60 * 60 * 1000);
	if (($duration['day'] - (int)$duration['day']) > 0) 
		$duration['hour'] = ($duration['day'] - (int)$duration['day']) * 24;
	if (isset($duration['hour'])) {
		if (($duration['hour'] - (int)$duration['hour']) > 0) 
			$duration['minute'] = ($duration['hour'] - (int)$duration['hour']) * 60;
		if (isset($duration['minute'])) {
			if (($duration['minute'] - (int)$duration['minute']) > 0) 
				$duration['second'] = ($duration['minute'] - (int)$duration['minute']) * 60;
		}
	}
	$tmp = array();
	foreach ($duration as $type => $value) {
		if ($value >= 1) {
			$pluralType = ((int)$value === 1) ? $type : $type . 's';
			if ($type !== 'second') $tmp[] = (int)$value . " $pluralType";
			else $tmp[] = $value . " $pluralType";
		}
	}
	return implode(', ', $tmp);
}
function toYesNo($bool) {
	return ($bool) ? 'Yes' : 'No';
}
// Loading stage list
$cache = new Cache('engine/cache/stages');
if (user_logged_in() && is_admin($user_data)) {
	if (isset($_GET['loadStages'])) {
		echo "<p><strong>Logged in as admin, loading engine/XML/stages.xml file and updating cache.</strong></p>";
		// STAGES XML TO PHP ARRAY
		$stagesXML = simplexml_load_file("engine/XML/stages.xml");
		if ($stagesXML !== false) {
			$stagesData = array();
			// Load config (stages enabled or disabled)
			if ($config['ServerEngine'] == 'TFS_10')
				foreach ($stagesXML->config->attributes() as $name => $value)
					$stagesData["$name"] = "$value";
			// Load stage levels
			// Each stage XML object
			if ($config['ServerEngine'] == 'TFS_10') {
				foreach ($stagesXML->stage as $stage) {
					$rowData = array();
					// Each attribute name and values on current stage object
					foreach ($stage->attributes() as $name => $value) {
						$rowData["$name"] = "$value";
					}
					// Populate XML assoc array
					$stagesData['stages'][] = $rowData;
				}
			} else {
				// TFS 0.3/4
				foreach ($stagesXML->world as $world) {
					foreach ($world->stage as $stage) {
						$rowData = array();
						// Each attribute name and values on current stage object
						foreach ($stage->attributes() as $name => $value) {
							$rowData["$name"] = "$value";
						}
						// Populate XML assoc array
						$stagesData['stages'][] = $rowData;
					}
				}
			}
			$cache->setContent($stagesData);
			$cache->save();
		}
	} else {
		$stagesData = $cache->load();
		?>
		<form action="">
			<input type="submit" name="loadStages" value="Load stages.xml">
		</form>
		<?php
	}
	// END STAGES XML TO PHP ARRAY
} else {
	$stagesData = $cache->load();
}
// End loading stage list

// Loading config.lua
$cache = new Cache('engine/cache/luaconfig');
if (user_logged_in() && is_admin($user_data)) {
	if (isset($_POST['loadConfig']) && isset($_POST['configData'])) {
		// Whitelist for values we are interested in
		$whitelist = array( // Etc 'maxPlayers'
			'worldType',
			'hotkeyAimbotEnabled',
			'protectionLevel',
			'killsToRedSkull',
			'killsToBlackSkull',
			'pzLocked',
			'removeChargesFromRunes',
			'timeToDecreaseFrags',
			'whiteSkullTime',
			'stairJumpExhaustion',
			'experienceByKillingPlayers',
			'expFromPlayersLevelRange',
			'loginProtocolPort',
			'maxPlayers',
			'motd',
			'onePlayerOnlinePerAccount',
			'deathLosePercent',
			'housePriceEachSQM',
			'houseRentPeriod',
			'marketOfferDuration',
			'premiumToCreateMarketOffer',
			'maxMarketOffersAtATimePerPlayer',
			'allowChangeOutfit',
			'freePremium',
			'kickIdlePlayerAfterMinutes',
			'rateExp',
			'rateSkill',
			'rateLoot',
			'rateMagic',
			'rateSpawn',
			'staminaSystem',
			'experienceStages'
		);
		// TFS 0.3/4 compatibility, convert config value names to TFS 1.0 values
		$tfs03to10 = array(
			// TFS 0.3/4		  TFS 1.0
			'rateExperience' 			=> 'rateExp',
			'loginPort' 				=> 'loginProtocolPort',
			'rateExperienceFromPlayers' => 'experienceByKillingPlayers',
			'dailyFragsToRedSkull' 		=> 'killsToRedSkull',
			'dailyFragsToBlackSkull' 	=> 'killsToBlackSkull',
			'removeRuneCharges' 		=> 'removeChargesFromRunes',
			'stairhopDelay' 			=> 'stairJumpExhaustion',
			'housePriceEachSquare' 		=> 'housePriceEachSQM',
			'idleKickTime' 				=> 'kickIdlePlayerAfterMinutes',
		);
		// This will be the populated array with filtered relevant data
		$luaConfig = array();
		// Explode the string into string array by newline
		$rawLua = explode("\n", $_POST['configData']);
		// Clean up the array
		$length = count($rawLua);
		for ($i = 0; $i < $length; $i++) {
			// We only care about lines that have the = symbol
			if (strpos($rawLua[$i], '=') !== false) {
				// Look for inline Lua comments and remove them
				$comment = strpos($rawLua[$i], '--');
				if ($comment !== false)
					$rawLua[$i] = substr($rawLua[$i], 0, $comment);
				$rawLua[$i] = trim($rawLua[$i]); // Remove unnecessary whitespace
				// If for some reason the line is empty, ignore it. (Could be a "=" symbol inside an inline Lua comment that we sliced away)
				if (!empty($rawLua[$i])) {
					// Built a relevant data array
					$data = explode('=', $rawLua[$i]);
					// Remove unnecessary whitespace
					$data[0] = trim($data[0]);
					$data[1] = trim($data[1]);
					// TFS 0.3/4 compatibility
					if (isset($tfs03to10[$data[0]])) {
						$data[0] = $tfs03to10[$data[0]];
						if (isset($tfs03to10[$data[1]])) {
							$data[1] = $tfs03to10[$data[1]];
						}
					}
					if (in_array($data[0], $whitelist)) {
						// Type cast: boolean
						if (in_array(strtolower($data[1]), array('true', 'false'))) {
							$data[1] = (strtolower($data[1]) === 'true') ? true : false;
						} else {
							if (strpos($data[1], '"') === false) {
								if (!in_array($data[1], array_keys($luaConfig))) {
									// Type cast: integer
									$data[1] = eval('return (' . $data[1] . ');');
								} else {
									// Type cast: Load value from another key
									$data[1] = (isset($luaConfig[$data[1]])) ? $luaConfig[$data[1]] : null;
								}
							} else {
								// Type cast: string, just remove the quote we earlier used to determine if it was a string.
								$data[1] = str_replace('"', '', $data[1]);
							}
						}
						// Add the results
						$luaConfig[$data[0]] = $data[1];
					} // End whitelisted row
				} // End not empty row
			} // Line has \= symbol
		} // for loop
		$cache->setContent($luaConfig);
		$cache->save();
	} else {
		$luaConfig = $cache->load();
		?>
		<br>
		<form action="" method="POST">
			<label for="configData">Find your OT server folder, put the text inside config.lua into this text field:</label><br>
			<textarea name="configData" placeholder="Open config.lua and copy the content into this text area."></textarea><br>
			<input type="submit" name="loadConfig" value="Load config data">
		</form>
		<?php
	}
} else {
	$luaConfig = $cache->load();
}
// End loading config.lua

$stages = false;

// Render HTML
?>

<h1>Server Information</h1>
<p>Here you will find all basic information about <b><?php echo $config['site_title']; ?></b></p>

<?php if (($stagesData && isset($stagesData['enabled']) && $stagesData['enabled']) || (isset($luaConfig['experienceStages']) && $luaConfig['experienceStages'] === true)): $stages = true; ?>
	<h2>Server rates</h2>
	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<td>Minimum level</td>
				<td>Maximum level</td>
				<td>Multiplier</td>
			</tr>
			<?php foreach ($stagesData['stages'] as $stage): ?>
				<tr>
					<td><?php echo $stage['minlevel']; ?></td>
					<td><?php echo (isset($stage['maxlevel'])) ? $stage['maxlevel'] : "Unlimited"; ?></td>
					<td><?php echo $stage['multiplier']; ?>x</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php if ($luaConfig): ?>
	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<?php if (!$stages): ?>
					<td>Experience rate</td>
				<?php endif; ?>
				<td>Skills rate</td>
				<td>Magic rate</td>
				<td>Loot rate</td>
			</tr>
			<tr>
				<?php if (!$stages): ?>
					<td><?php echo $luaConfig['rateExp']; ?></td>
				<?php endif; ?>
				<td><?php echo $luaConfig['rateSkill']; ?></td>
				<td><?php echo $luaConfig['rateMagic']; ?></td>
				<td><?php echo $luaConfig['rateLoot']; ?></td>
			</tr>
		</tbody>
	</table>

	<h2>Miscellaneous information</h2>
	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<td colspan="2">Connection information</td>
			</tr>
			<tr>
				<td>Client</td>
				<td><?php echo ($config['client'] / 100); ?></td>
			</tr>
			<tr>
				<td>IP</td>
				<td><?php echo $_SERVER['SERVER_NAME']; ?></td>
			</tr>
			<tr>
				<td>Port</td>
				<td><?php echo $luaConfig['loginProtocolPort']; ?></td>
			</tr>
		</tbody>
	</table>

	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<td colspan="2">PvP information</td>
			</tr>
			<tr>
				<td>World type</td>
				<td><?php echo $luaConfig['worldType']; ?></td>
			</tr>
			<tr>
				<td>Hotkey aimbot</td>
				<td><?php echo toYesNo($luaConfig['hotkeyAimbotEnabled']); ?></td>
			</tr>
			<tr>
				<td>Protection level</td>
				<td><?php echo $luaConfig['protectionLevel']; ?></td>
			</tr>
			<tr>
				<td>Kills to red skull</td>
				<td><?php echo $luaConfig['killsToRedSkull']; ?></td>
			</tr>
			<tr>
				<td>Kills to black skull</td>
				<td><?php echo $luaConfig['killsToBlackSkull']; ?></td>
			</tr>
			<tr>
				<td>Remove rune charges</td>
				<td><?php echo toYesNo($luaConfig['removeChargesFromRunes']); ?></td>
			</tr>
			<?php if (isset($luaConfig['timeToDecreaseFrags'])): ?>
				<tr>
					<td>Time to decrease frags</td>
					<td><?php echo toDuration($luaConfig['timeToDecreaseFrags']); ?></td>
				</tr>
			<?php endif; ?>
			<tr>
				<td>Experience by killing players</td>
				<td><?php echo toYesNo($luaConfig['experienceByKillingPlayers']); ?></td>
			</tr>

			<?php if ($luaConfig['experienceByKillingPlayers']): ?>
				<tr>
					<td>Experience gain kill threshold:</td>
					<td><?php echo $luaConfig['expFromPlayersLevelRange']; ?>% of your level</td>
				</tr>
			<?php endif; ?>

			<tr>
				<td>White skull duration</td>
				<td><?php echo toDuration($luaConfig['whiteSkullTime']); ?></td>
			</tr>
			<tr>
				<td>Protection zone lock (non lethal attack)</td>
				<td><?php echo toDuration($luaConfig['pzLocked']); ?></td>
			</tr>
			<tr>
				<td>Stair jump exhaust</td>
				<td><?php echo toDuration($luaConfig['stairJumpExhaustion']); ?></td>
			</tr>
		</tbody>
	</table>

	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<td colspan="2">Other information</td>
			</tr>
			<tr>
				<td>Free premium</td>
				<td><?php echo toYesNo($luaConfig['freePremium']); ?></td>
			</tr>
			<tr>
				<td>House rent period</td>
				<td><?php echo $luaConfig['houseRentPeriod']; ?></td>
			</tr>
			<tr>
				<td>House SQM price</td>
				<td><?php echo $luaConfig['housePriceEachSQM']; ?> gp</td>
			</tr>
			<tr>
				<td>AFK kickout</td>
				<td><?php echo toDuration($luaConfig['kickIdlePlayerAfterMinutes'] * 60 * 1000); ?></td>
			</tr>
			<tr>
				<td>One player online per account</td>
				<td><?php echo toYesNo($luaConfig['stairJumpExhaustion']); ?></td>
			</tr>
			<tr>
				<td>Max players online server limit</td>
				<td><?php echo ($luaConfig['maxPlayers'] > 0) ? $luaConfig['maxPlayers'] : 'Unlimited'; ?></td>
			</tr>
			<tr>
				<td>Allow outfit change</td>
				<td><?php echo toYesNo($luaConfig['allowChangeOutfit']); ?></td>
			</tr>
			<?php if (isset($luaConfig['staminaSystem'])): ?>
				<tr>
					<td>Stamina system</td>
					<td><?php echo toYesNo($luaConfig['staminaSystem']); ?></td>
				</tr>
			<?php endif; ?>
			<?php if (isset($luaConfig['premiumToCreateMarketOffer'])): ?>
				<tr>
					<td>Premium to add items to market</td>
					<td><?php echo toYesNo($luaConfig['premiumToCreateMarketOffer']); ?></td>
				</tr>
			<?php endif; ?>
			<?php if (isset($luaConfig['marketOfferDuration'])): ?>
				<tr>
					<td>Market offer duration</td>
					<td><?php echo toDuration($luaConfig['marketOfferDuration'] * 1000); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
<?php else: ?>
	<p>The server administrator has yet to import server information to this page.</p>
<?php endif;
include 'layout/overall/footer.php'; ?>
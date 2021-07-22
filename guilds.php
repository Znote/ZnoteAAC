<?php require_once 'engine/init.php';
if ($config['require_login']['guilds']) protect_page();

// These functions are page-specific and only used in this file. 
function guild_list($TFSVersion) {
	$cache = new Cache('engine/cache/guildlist');
	if ($cache->hasExpired()) {
		$guilds = mysql_select_multi("
			SELECT 
				`id`, 
				`name`, 
				`creationdata`, 
				`motd`, 
				(	SELECT 
						COUNT('guild_id') 
					FROM `guild_membership` 
					WHERE `guild_id`=`id`
				) AS `total` 
			FROM `guilds` 
			ORDER BY `name`;
		");

		// Add level data info to guilds
		if ($guilds !== false)
			for ($i = 0; $i < count($guilds); $i++)
				$guilds[$i]['level'] = get_guild_level_data($guilds[$i]['id']);

		$cache->setContent($guilds);
		$cache->save();
	} else {
		$guilds = $cache->load();
	}
	return $guilds;
}

function get_guild_position($rid) {
	$rid = (int)$rid;
	$data = mysql_select_single("SELECT `level` FROM `guild_ranks` WHERE `id`={$rid};");
	return ($data !== false) ? $data['level'] : false;
}

// Check guild logo
function logo_exists($guild) {
	$guild = sanitize($guild);
	echo (file_exists('engine/guildimg/'.$guild.'.gif')) ? 'engine/guildimg/'.$guild.'.gif' : 'engine/guildimg/default@logo.gif';
}

// Remove cid invitation from guild (gid)
function guild_remove_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	mysql_delete("
		DELETE FROM `guild_invites` 
		WHERE `player_id`='{$cid}' 
		AND `guild_id`='{$gid}'
	;");
}

function update_player_guildnick_10($cid, $nick) {
	$cid = (int)$cid;
	$nick = sanitize($nick);
	mysql_update("UPDATE `guild_membership` SET `nick`='{$nick}' WHERE `player_id`={$cid}");
}

function guild_player_join($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;

	// Find rank id for regular member in this guild
	$guildrank = mysql_select_single("
		SELECT `id` 
		FROM `guild_ranks` 
		WHERE `guild_id`='{$gid}' 
		AND `level`='1' LIMIT 1;
	");

	if ($guildrank !== false) {
		$rid = $guildrank['id'];
		// Remove invite
		mysql_delete("DELETE FROM `guild_invites` WHERE `player_id`='{$cid}';");
		// Add to guild
		mysql_insert("
			INSERT INTO `guild_membership` (`player_id`, `guild_id`, `rank_id`, `nick`) 
			VALUES ('{$cid}', '{$gid}', '{$rid}', '')
		;");
		// Return success
		return true;
	}

	return false;
}

function guild_player_leave_10($cid) {
	$cid = (int)$cid;
	mysql_delete("DELETE FROM `guild_membership` WHERE `player_id`='{$cid}' LIMIT 1;");
}

function update_player_guild_position_10($cid, $rid) {
	$cid = (int)$cid;
	$rid = (int)$rid;
	mysql_update("UPDATE `guild_membership` SET `rank_id`='{$rid}' WHERE `player_id`={$cid}");
}

function guild_invite_player($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	mysql_insert("INSERT INTO `guild_invites` (`player_id`, `guild_id`) VALUES ('{$cid}', '{$gid}')");
}

function guild_remove_invites($gid) {
	$gid = (int)$gid;
	mysql_delete("DELETE FROM `guild_invites` WHERE `guild_id`='{$gid}';");
}

function guild_delete($gid) {
	$gid = (int)$gid;
	mysql_delete("DELETE FROM `guilds` WHERE `id`='{$gid}';");
}

// Get guild data, using guild id.
function get_guild_rank_data($gid) {
	$gid = (int)$gid;
	return mysql_select_multi("SELECT `id`, `guild_id`, `name`, `level` FROM `guild_ranks` WHERE `guild_id`='{$gid}' ORDER BY `id` DESC LIMIT 0, 30");
}

// Changes leadership of a guild to player_id
function guild_new_leader($new_leader, $gid) {
	$new_leader = (int)$new_leader;
	$gid = (int)$gid;
	mysql_update("UPDATE `guilds` SET `ownerid`='{$new_leader}' WHERE `id`={$gid}");
}

// Change guild leader (parameters: cid, new and old leader).
function guild_change_leader($nCid, $oCid) {
	$nCid = (int)$nCid;
	$oCid = (int)$oCid;
	$gid = guild_leader_gid($oCid);
	$ranks = get_guild_rank_data($gid);
	$leader_rid = 0;
	$vice_rid = 0;

	// Get rank id for leader and vice leader.
	foreach ($ranks as $rank) {
		if ($rank['level'] == 3) $leader_rid = $rank['id'];
		if ($rank['level'] == 2) $vice_rid = $rank['id'];
	}

	$status = ($leader_rid > 0 && $vice_rid > 0) ? true : false;

	if ($status) {
		// Update players and set their new rank id
		mysql_update("UPDATE `guild_membership` SET `rank_id`='{$leader_rid}' WHERE `player_id`={$nCid} LIMIT 1;");
		mysql_update("UPDATE `guild_membership` SET `rank_id`='{$vice_rid}' WHERE `player_id`={$oCid} LIMIT 1;");

		// Update guilds set new ownerid
		guild_new_leader($nCid, $gid);
	}

	return $status;
}

// Creates a guild, where cid is the owner of the guild, and name is the name of guild.
function create_guild($cid, $name) {
	$cid = (int)$cid;
	$name = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", sanitize($name))));
	$time = time();

	// Create the guild
	mysql_insert("INSERT INTO `guilds` (`name`, `ownerid`, `creationdata`, `motd`) VALUES ('{$name}', '{$cid}', '{$time}', '');");

	// Get guild id
	$gid = get_guild_id($name);

	// Get rank id for guild leader
	$data = mysql_select_single("SELECT `id` FROM `guild_ranks` WHERE `guild_id`='{$gid}' AND `level`='3' LIMIT 1;");
	$rid = ($data !== false) ? $data['id'] : false;

	// Give player rank id for leader of his guild
	mysql_insert("INSERT INTO `guild_membership` (`player_id`, `guild_id`, `rank_id`, `nick`) VALUES ('{$cid}', '{$gid}', '{$rid}', '');");
}

// Returns total members in a guild (integer)
function count_guild_members($gid) {
	$gid = (int)$gid;
	$data = mysql_select_single("SELECT COUNT('guild_id') AS `total` FROM `guild_membership` WHERE `guild_id`='{$gid}';");
	return ($data !== false) ? $data['total'] : false;
}

// Guildwar functions
function guild_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	$gname = get_guild_name($cid);
	$ename = get_guild_name($gid);
	$time = time();
	mysql_insert("
		INSERT INTO `guild_wars` (`guild1`, `guild2`, `name1`, `name2`, `status`, `started`, `ended`) 
		VALUES ('{$cid}', '{$gid}', '{$gname}', '{$ename}', '0', '{$time}', '0')
	;");
}

function accept_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	mysql_update("UPDATE `guild_wars` SET `status` = 1 WHERE `guild1` = '{$cid}' AND `guild2` = '{$gid}' AND `status` = 0;");
}

function reject_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	$time = time();
	mysql_update("UPDATE `guild_wars` SET `status` = 2, `ended` = '{$time}' WHERE `guild1` = '{$cid}' AND `guild2` = '{$gid}';");
}

function cancel_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	$time = time();
	mysql_update("UPDATE `guild_wars` SET `status` = 3, `ended` = '{$time}' WHERE `guild2` = '{$cid}' AND `guild1` = '{$gid}';");
}

include 'layout/overall/header.php';

if (user_logged_in() === true) {
	// fetch data
	$char_count = user_character_list_count($session_user_id);
	$char_array = user_character_list($user_data['id']);
	$characters = array();
	$charactersId = array();
	$charactersRank = array();
	if ($char_array !== false) {
		foreach ($char_array as $value) {
			$characters[] = $value['name'];
			$charactersId[] = $value['id'];
			$charactersRank[] = $value['rank_id'];
		}
	}
} else {
	$char_count = 0;
}

// Display the guild list
if (empty($_GET['name'])) {
//data_dump($guild, false, "guild data");
$guilds = guild_list($config['ServerEngine']);

if (isset($guilds) && !empty($guilds) && $guilds !== false) {
	//data_dump($guilds, false, "Guilds");
	?>
	<style type="text/css">
		#guildsTable tr {
			cursor: pointer;
		}
	</style>
	<table id="guildsTable" class="table table-striped table-hover">
		<tr class="yellow">
			<th>Logo</th>
			<th>Description</th>
			<th>Guild data</th>
		</tr>
			<?php
			foreach ($guilds as $guild) {
				if ($guild['total'] >= 1) {
					$url = url("guilds.php?name=". $guild['name']);
					?>
					<tr class="special" onclick="javascript:window.location.href='<?php echo $url; ?>'">
						<td style="width: 100px;">
							<img style="max-height: 100px; margin: auto; display: block;" 
								src="<?php logo_exists($guild['name']); ?>">
						</td>
						<td>
							<b><?php echo $guild['name']; ?></b>
							<?php if (strlen($guild['motd']) > 0) echo '<br>'.$guild['motd']; ?>
						</td>
						<td>
							<?php echo "Total members: ".$guild['level']['players']; ?>
							<br><?php echo "Average level: ".$guild['level']['avg'].""; ?>
							<br><?php echo "Guild level: ".$guild['level']['total']; ?>
						</td>
					</tr>
					<?php
					//echo '<td>'. getClock($guild['creationdata'], true) .'</td>';
				}
			}
			?>
	</table>
	<?php 
} else echo '<p>Guild list is empty.</p>';?>

<!-- POST action: create guild -->
<?php
if (user_logged_in() === true) {
	// post verifications
	// CREATE GUILD
	if (!empty($_POST['selected_char']) && !empty($_POST['guild_name'])) {
		data_dump($_POST, false, "debug");
		if ((int)user_character_account_id($_POST['selected_char']) === $session_user_id) {
			data_dump($_POST, false, "debug2");
			//code here
			$name = sanitize($_POST['selected_char']);
			$user_id = user_character_id($name);
			
			$char_data = user_character_data($user_id, 'level');
			$char_data['online'] = (user_is_online_10($user_id)) ? 1 : 0;

			// If character level is high enough
			if ($char_data['level'] >= $config['create_guild_level']) {

				// If character is offline
				if ($char_data['online'] == 0) {

					// If character is premium
					if ($config['guild_require_premium'] == false || $user_data['premdays'] > 0) {

						if (get_character_guild_rank($user_id) < 1) {

							if (preg_match("/^[a-zA-Z_ ]+$/", $_POST['guild_name'])) {
							// Only allow normal symbols as guild name
								if (strlen($_POST['guild_name']) < 31) {

								$guildname = sanitize($_POST['guild_name']);

								$gid = get_guild_id($guildname);
								if ($gid === false) {
									create_guild($user_id, $guildname);
									// Re-cache the guild list
									$guilds = guild_list($config['ServerEngine']);
									header('Location: success.php');
									exit();
									} else echo 'A guild with that name already exist.';
								} else echo 'Guild name is to long. It can has to be 30 or less characters long.';
							} else echo 'Guild name may only contain a-z, A-Z and spaces.';
						} else echo 'You are already in a guild.';
					} else echo 'You need a premium account to create a guild.';
				} else echo 'Your character must be offline to create a guild.';
			} else echo $name .' is level '. $char_data['level'] .'. But you need level '. $config['create_guild_level'] .'+ to create your own guild!';
		}
	}
	// end
	?>


	<!-- FORMS TO CREATE GUILD-->
	<form action="" method="post">
		<ul>
			<li>
				Create Guild:<br>
				<select name="selected_char">
				<?php
				for ($i = 0; $i < $char_count; $i++) {
					echo '<option value="'. $characters[$i] .'">'. $characters[$i] .'</option>';
				}
				?>
				</select>
				<input type="text" name="guild_name">
				<input type="submit" value="Create Guild">
			</li>
		</ul>
	</form>

	<?php
} else echo 'You need to be logged in to create guilds.';
?>
<!-- end user-->

<?php
} else { // GUILD OVERVIEW
	$guild = get_guild_data($_GET['name']);
	$gid = (int)$guild['id'];
	if ($gid === false) {
		header('Location: guilds.php');
		exit();
	}
	$gcount = count_guild_members($gid);
	if ($gcount < 1) {
		header('Location: guilds.php');
		exit();
	}
	$inv_data = guild_invite_list($gid);
	$players = get_guild_players($gid);
	$inv_count = 0;

	// Calculate invite count
	if ($inv_data !== false) {
		foreach ($inv_data as $inv) {
			++$inv_count;
		}
	}

	// calculate visitor access
	$highest_access = 0;
	if (user_logged_in() === true) {
		// Get visitor access in this guild
		foreach ($players as $player) {
			$rid = $player['rank_id'];

			for ($i = 0; $i < $char_count; $i++) {

				$playerRank = $charactersRank[$i];

				if ($playerRank == $rid) {
					// Get a player guild position ID, using his rank_id
					$access = get_guild_position($playerRank);
					if ($access == 2 || $access == 3) { //If player got access level vice leader or leader
						if ($access > $highest_access) $highest_access = $access;
					}
				}

			}
		}
	}
	// Display the specific guild page
	?>

	<!-- Guild information above table -->
	<div id="guildTitleDiv">
		<?php 
		if (isset($_GET['error'])) 
			echo "<font size='5' color='red'>".sanitize($_GET['error'])."</font><br><br>";

		if ($config['use_guild_logos']): ?>
			<div id="guildImageDiv" style="float: left; margin-right: 10px;">
				<img style="max-width: 100px; max-height: 100px;" src="<?php logo_exists(sanitize($_GET['name'])); ?>">
			</div>
			<?php 
		endif; ?>
		<div id="guildDescription">
			<h1>Guild: <?php echo sanitize($_GET['name']); ?></h1>
			<p><?php echo $guild['motd']; ?></p>
		</div>
	</div>

	<!-- Guild information -->
	<table id="guildViewTable" class="table table-striped">
		<tr class="yellow">
			<th>Rank:</th>
			<th>Name:</th>
			<th>Level:</th>
			<th>Vocation:</th>
			<th>Status:</th>
		</tr>
			<?php
			$onlinelist = array();
			$gplayers = array();
			foreach ($players as $player) {
				$gplayers[] = $player['id'];
			}
			$gplayers = join(',',$gplayers);
			$onlinequery = mysql_select_multi("
				SELECT `player_id` 
				FROM `players_online` 
				WHERE `player_id` IN({$gplayers});
			");
			if ($onlinequery !== false) foreach ($onlinequery as $online) {
				$onlinelist[] = $online['player_id'];
			}
			//data_dump($players, false, "Data");
			$rankName = '';
			foreach ($players as $player) {
				$chardata['online'] = (in_array($player['id'], $onlinelist)) ? 1 : 0;
				echo '<tr>';
				echo '<td>' . ($rankName !== $player['rank_name'] ? $player['rank_name'] : '') . '</td>';
				$rankName = $player['rank_name'];
				echo '<td><a href="characterprofile.php?name='. $player['name'] .'">'. $player['name'] .'</a>';
				if (!empty($player['guildnick'])) {
					echo ' ('. $player['guildnick'] .')';
				}
				echo '</td>';
				echo '<td>'. $player['level'] .'</td>';
				echo '<td>'. $config['vocations'][$player['vocation']]['name'] .'</td>';
				if ($chardata['online'] == 1) echo '<td> <b><font color="green"> Online </font></b></td>';
				else echo '<td> Offline </td>';
				echo '</tr>';
			}
			?>
	</table>

	<?php // Invited characters
	if ($inv_count > 0): ?>
		<h3>Invited characters</h3>
		<table>
			<tr class="yellow">
				<td>Name:</td>
				<?php
				if ($highest_access == 2 || $highest_access == 3) {
					echo '<td>Remove:</td>';
				}
				// Shuffle through visitor characters
				for ($i = 0; $i < $char_count; $i++) {
					$exist = false;
					// Shuffle through invited character, see if they match your character.
					if ($inv_data !== false) foreach ($inv_data as $inv) {
						if ($charactersId[$i] == $inv['player_id']) {
							$exist = true;
						}
					}
					if ($exist) echo '<td>Join Guild:</td><td>Reject Invitation:</td>';
				}
				?>
			</tr>
				<?php
				$bool = false;
				if ($inv_data !== false) foreach ($inv_data as $inv) {
					echo '<tr>';
					echo '<td>'. $inv['name'] .'</td>';
					// Remove invitation
					if ($highest_access == 2 || $highest_access == 3) {
					?> <form action="" method="post"> <?php
						echo '<td>';
						echo '<input type="hidden" name="uninvite" value="' . $inv['player_id'] . '" />';
						echo '<input type="submit" value="Remove Invitation">';
						echo '</td>';
					?> </form> <?php
					}
					// Join Guild
					?> <form action="" method="post"> <?php
						for ($i = 0; $i < $char_count; $i++) {
							if ($charactersId[$i] == $inv['player_id']) {
								echo '<td>';
								echo '<input type="hidden" name="joinguild" value="' . $inv['player_id'] . '" />';
								echo '<input type="submit" value="Join Guild">';
								echo '</td>';
								$bool = true;
							}
						}
						if (isset($bool, $exist) && !$bool && $exist) {
							echo '<td></td>';
							$bool = false;
						}
					?> </form> <?php
					// Reject invitation
					?> <form action="" method="post"> <?php
						for ($i = 0; $i < $char_count; $i++) {
							if ($charactersId[$i] == $inv['player_id']) {
								echo '<td>';
								echo '<input type="hidden" name="uninvite" value="' . $inv['player_id'] . '" />';
								echo '<input type="submit" value="Reject Invitation">';
								echo '</td>';
								$bool = true;
							}
						}
						if (isset($bool, $exist) && !$bool && $exist) {
							echo '<td></td>';
							$bool = false;
						}
					?> </form> <?php
					echo '</tr>';
				}
				?>
		</table>
	<?php endif; ?>

	<!-- Leader stuff -->
	<?php
	if (user_logged_in() === true) {

		// Uninvite and joinguild is also used for visitors who reject their invitation.
		if (!empty($_POST['uninvite'])) {
			// Is this action being triggered by a vice leader+, or the invited player?
			$u_player = (int)$_POST['uninvite'];
			$u_player = user_character_name($u_player);
			$u_player = (int)user_character_account_id($u_player);
			if (in_array($highest_access, array(2,3)) || $u_player === $session_user_id) {
				guild_remove_invitation($_POST['uninvite'], $gid);
				header('Location: guilds.php?name='. $_GET['name']);
				exit();
			}
		}

		// Join guild
		if (!empty($_POST['joinguild'])) {
			$joining_player_id = (int)$_POST['joinguild'];
			$join_account = (int)user_character_account_id(user_character_name($joining_player_id));
			
			if ($join_account !== $session_user_id) {
				echo '<font color="red" size="4">Join guild request sent from wrong account.</font>';
				include 'layout/overall/footer.php';
				exit();
			}

			// Join a guild
			if ($inv_data !== false) foreach ($inv_data as $inv) {
				if ((int)$inv['player_id'] == $joining_player_id) {
					$chardata['online'] = (user_is_online_10($joining_player_id)) ? 1 : 0;
					if ($chardata['online'] == 0) {
						
						// Ensure player is not already a member of another guild
						if (get_character_guild_rank($joining_player_id) === false) {
							if (guild_player_join($joining_player_id, (int)$gid)) {
								header('Location: guilds.php?name='. $_GET['name']);
								exit();
							} else echo '<font color="red" size="4">Failed to find guild position representing member.</font>';
						
						} else {
							$already_guild = get_player_guild_data($joining_player_id);
							$already_guild_name = get_guild_name($already_guild['guild_id']);
							
							echo "<font color='red' size='4'>You are already <strong>{$already_guild['rank_name']}</strong> of another guild: <strong><a href='guilds.php?name={$already_guild_name}'>{$already_guild_name}</a></strong>.<br>You need to leave that guild first before you can join another one.</font>";
						}

					} else echo '<font color="red" size="4">Character must be offline before joining guild.</font>';
				}
			}
		}

		// leave guild
		if (!empty($_POST['leave_guild'])) {
			$name = sanitize($_POST['leave_guild']);
			$cidd = user_character_id($name);
			
			$leave_account = (int)user_character_account_id($name);
			if ($leave_account !== $session_user_id) {
				echo '<font color="red" size="4">Leave guild request sent from wrong account.</font>';
				include 'layout/overall/footer.php';
				exit();
			}

			// If character is offline
			$chardata['online'] = (user_is_online_10($cidd)) ? 1 : 0;
			if ($chardata['online'] == 0) {
				guild_player_leave_10($cidd);
				header('Location: guilds.php?name='. $_GET['name']);
				exit();
			} else echo '<font color="red" size="4">Character must be offline first!</font>';
		}

		// vice leader (and guild leader) actions
		if ($highest_access >= 2) {
			// Vice-leaders and leaders
			// Change Guild Nick
			if (!empty($_POST['player_guildnick'])) {
				if ($config['guild_allow_nicknames']) {
					$p_cid = user_character_id($_POST['player_guildnick']);
					$p_guild = get_player_guild_data($p_cid);
					
					if (preg_match("/^[a-zA-Z_ ]+$/", $_POST['guildnick']) || empty($_POST['guildnick'])) {
						// Only allow normal symbols as guild nick
						$p_nick = sanitize($_POST['guildnick']);
						
						if ($p_guild['guild_id'] == $gid) {
							$chardata['online'] = (user_is_online_10($p_cid)) ? 1 : 0;
							
							if ($chardata['online'] == 0) {
								update_player_guildnick_10($p_cid, $p_nick);
								header('Location: guilds.php?name='. $_GET['name']);
								exit();
							} else echo '<font color="red" size="4">Character not offline.</font>';
						}

					} else echo '<font color="red" size="4">Character guild nick may only contain a-z, A-Z and spaces.</font>';
				} else echo '<font color="red" size="4">Change guild nickname feature has been disabled.</font>';
			}

			// Invite character to guild
			if (!empty($_POST['invite'])) {
				if (user_character_exist($_POST['invite'])) {
					// Make sure they are not in another guild
					$charid = user_character_id($_POST['invite']);
					$membership = mysql_select_single("SELECT `rank_id` FROM `guild_membership` WHERE `player_id`='{$charid}' LIMIT 1;");
					if (!$membership) {
						$status = false;
						if ($inv_data !== false) {
							foreach ($inv_data as $inv) {
								if ($inv['player_id'] == user_character_id($_POST['invite'])) $status = true;
							}
						}
						foreach ($players as $player) {
							if ($player['name'] == $_POST['invite']) $status = true;
						}

						if ($status == false) {
							guild_invite_player($charid, $gid);
							header('Location: guilds.php?name='. $_GET['name']);
							exit();
						} else echo '<font color="red" size="4">That character is already invited(or a member) on this guild.</font>';
					} else echo '<font color="red" size="4">That character is already in a guild.</font>';

				} else echo '<font color="red" size="4">That character name does not exist.</font>';
			}

			// Guild Message (motd)
			if (!empty($_POST['motd'])) {
				$motd = sanitize($_POST['motd']);
				mysql_update("UPDATE `guilds` SET `motd`='{$motd}' WHERE `id`='{$gid}' LIMIT 1;");
				header('Location: guilds.php?name='. $_GET['name']);
				exit();
			}

			// Remove guild member
			if (!empty($_POST['remove_member'])) {
				$name = sanitize($_POST['remove_member']);
				$cid = user_character_id($name);
				
				$p_guild = get_player_guild_data($cid);
				if ($p_guild['guild_id'] == $gid) {
					guild_remove_member_10($cid);
					header('Location: guilds.php?name='. $_GET['name']);
					exit();
				}
			}

			// Create forum guild board
			if (!empty($_POST['forumGuildId'])) {
				
				if ($config['forum']['guildboard'] === true) {
					$forumExist = mysql_select_single("SELECT `id` FROM `znote_forum` WHERE `guild_id`='{$gid}' LIMIT 1;");
						
					if ($forumExist === false) {
						// Insert data
						mysql_insert("
							INSERT INTO `znote_forum` 
							(`name`, `access`, `closed`, `hidden`, `guild_id`)
							VALUES ('Guild','1','0','0','{$gid}')
						;");
						echo '<h1>Guild board has been created.</h1>';
					} else echo '<h1>Guild board already exist.</h1>';

				} else {
					echo '<h1>Error: Guild board system is disabled.</h1>';
				}
			}

			// Guildwar actions
			if ($config['guildwar_enabled'] === true) {

				// Invite another guild to war
				if (!empty($_POST['warinvite'])) {
					$targetGuild = get_guild_id($_POST['warinvite']);
					if ($targetGuild) {
						$status = false;
						
						$war_invite = mysql_select_single("SELECT `id` FROM `guilds` WHERE `id` = '$gid';");
						if ($war_invite !== false) {
							foreach ($war_invite as $inv) {
								if ($inv['id'] == $targetGuild) $status = true;
							}
						}

						$check_guild = get_guild_name($gid);
						foreach ($check_guild as $guild) {
							if ($guild['name'] == $_POST['warinvite']) $status = true;
						}

						if ((int)$gid === (int)$targetGuild) $status = true;

						$wars = mysql_select_multi("
							SELECT 
								`id`, `guild1`, `guild2`, `status` 
							FROM `guild_wars` 
							WHERE (`guild1` = '$gid' OR `guild1` = '$targetGuild') 
							AND (`guild2` = '$gid' OR `guild2` = '$targetGuild') 
							AND `status` IN (0, 1)
						;");

						if ($status == false && $wars == false) {
							guild_war_invitation($gid, $targetGuild);
							$limit = (empty($_POST['limit'])) ? 100 : (int)$_POST['limit'];
							mysql_insert("INSERT INTO `znote_guild_wars` (`limit`) VALUES ('$limit');");
							header('Location: guilds.php?name='. $_GET['name']);
							exit();
						} else echo '<font color="red" size="4">This guild has already been invited to war(or you\'re trying to invite your own).</FONT>';

					} else echo '<font color="red" size="4">That guild name does not exist.</font>';
				}

				if (!empty($_POST['cancel_war_invite'])) {
					cancel_war_invitation($_POST['cancel_war_invite'], $gid);
					header('Location: guilds.php?name='. $_GET['name']);
					exit();
				}

				if (!empty($_POST['reject_war_invite'])) {
					reject_war_invitation($_POST['reject_war_invite'], $gid);
					header('Location: guilds.php?name='. $_GET['name']);
					exit();
				}

				if (!empty($_POST['accept_war_invite'])) {
					accept_war_invitation($_POST['accept_war_invite'], $gid);
					header('Location: guilds.php?name='. $_GET['name']);
					exit();
				}
			}

			$members = count_guild_members($gid);
			$ranks = get_guild_rank_data($gid);
			
			// Form to create guild board
			if ($config['forum']['guildboard'] === true && $config['forum']['enabled'] === true) {
				$forumExist = mysql_select_single("SELECT `id` FROM `znote_forum` WHERE `guild_id`='$gid' LIMIT 1;");
				if ($forumExist === false) {
					?>
					<form action="" method="post">
						<ul>
							<li>Create forum guild board:<br>
							<input type="hidden" name="forumGuildId" value="<?php echo $gid; ?>">
							<input type="submit" value="Create Guild Board">
						</ul>
					</form>
					<?php
				}
			}

			// Form to upload guild logo
			if ($config['use_guild_logos']) {
				?>
				<form action="" method="post" enctype="multipart/form-data">
					<ul>
						<li>Upload guild logo [.gif images only, 100x100px size]:<br>
							<input type="file" name="file" id="file" accept="image/gif">
							<input type="submit" name="submit" value="Upload guild logo">
						</li>
					</ul>
				</form>
				<?php
				if (!empty($_FILES['file'])) {
					check_image($_FILES['file']);
					echo '<br><br>';
				}
			}
			?>

			<!-- forms to invite character -->
			<form action="" method="post">
				<ul>
					<li>Invite Character to guild:<br>
						<input type="text" name="invite" placeholder="Character name">
						<input type="submit" value="Invite Character">
					</li>
				</ul>
			</form>

			<!-- Guild message of the day motd -->
			<form action="" method="post">
				<ul>
					<li>Change guild message:</li>
					<li>
						<textarea name="motd" placeholder="Guild Message" cols="50" rows="3"><?php echo $guild['motd']; ?></textarea><br>
						<input type="submit" value="Update guild message">
					</li>
				</ul>
			</form>

			<?php if ($config['guild_allow_nicknames']): ?>
				<!-- FORMS TO CHANGE GUILD NICK -->
				<form action="" method="post">
					<ul>
						<li>
							Change Guild Nick:<br>
							<select name="player_guildnick">
							<?php
							//$gid = get_guild_id($_GET['name']);
							//$players = get_guild_players($gid);
							foreach ($players as $player) {
								if ($player['rank_level'] != 3) {
									echo '<option value="'. $player['name'] .'">'. $player['name'] .'</option>';
								} else {
									if ($highest_access == 3) {
										echo '<option value="'. $player['name'] .'">'. $player['name'] .'</option>';
									}
								}
							}
							?>
							</select>
							<input type="text" name="guildnick" maxlength="15" placeholder="leave blank to erase">
							<input type="submit" value="Change Nick">
						</li>
					</ul>
				</form>
			<?php endif; ?>
				
			<?php if ($members > 1): ?>
				<!-- FORMS TO PROMOTE CHARACTER-->
				<form action="" method="post">
					<ul>
						<li>
							Promote Character:<br>
							<select name="promote_character">
							<?php
							//$gid = get_guild_id($_GET['name']);
							//$players = get_guild_players($gid);
							foreach ($players as $player) {
								if ($player['rank_level'] != 3) {
									echo '<option value="'. $player['name'] .'">'. $player['name'] .'</option>';
								}
							}
							?>
							</select>
							<select name="promote_position">
								<?php
								foreach ($ranks as $rank) {
									if ($rank['level'] != 3) {
										if ($rank['level'] != 2) {
											echo '<option value="'. $rank['id'] .'">'. $rank['name'] .'</option>';
										} else {
											if ($highest_access == 3) {
												echo '<option value="'. $rank['id'] .'">'. $rank['name'] .'</option>';
											}
										}
									}
								}
								?>
							</select>
							<input type="submit" value="Promote Member">
						</li>
					</ul>
				</form>

				<!-- Remove member from guild -->
				<form action="" method="post">
					<ul>
						<li>
							Kick member from guild:<br>
							<select name="remove_member">
								<?php
								//$gid = get_guild_id($_GET['name']);
								//$players = get_guild_players($gid);
								foreach ($players as $player) {
									if ($player['rank_level'] != 3) {
										if ($player['rank_level'] != 2) {
											echo '<option value="'. $player['name'] .'">'. $player['name'] .'</option>';
										} else {
											if ($highest_access == 3) {
												echo '<option value="'. $player['name'] .'">'. $player['name'] .'</option>';
											}
										}
									}
								}
								?>
							</select>
							<input type="submit" value="Remove member">
						</li>
					</ul>
				</form>
			<?php endif; ?>
			<br><br>
			<?php 
		} // end vice leader+

		// Guild leaders only
		if ($highest_access == 3) { 
			// Promote character to guild position
			if (!empty($_POST['promote_character']) && !empty($_POST['promote_position'])) {
				// Verify that promoted character is from this guild.
				$p_rid = $_POST['promote_position'];
				$p_cid = user_character_id($_POST['promote_character']);
				$p_guild = get_player_guild_data($p_cid);

				if ($p_guild['guild_id'] == $gid) {
					$chardata['online'] = (user_is_online_10($p_cid)) ? 1 : 0;
					if ($chardata['online'] == 0) {
						update_player_guild_position_10($p_cid, $p_rid);
						header('Location: guilds.php?name='. $_GET['name']);
						exit();
					} else echo '<font color="red" size="4">Character not offline.</font>';
				}
			}
			
			// Disband guild
			if (!empty($_POST['disband'])) {
				//$gidd = (int)$_POST['disband'];
				$members = get_guild_players($gid);
				$online = false;

				// First figure out if anyone are online.
				foreach ($members as $member) {
					$chardata['online'] = (user_is_online_10(user_character_id($member['name']))) ? 1 : 0;
					if ($chardata['online'] == 1) {
						$online = true;
					}
				}

				if (!$online) {
					// Then remove guild rank from every player.
					foreach ($members as $member) 
						guild_player_leave_10(user_character_id($member['name']));

					// Remove all guild invitations to this guild
					if ($inv_count > 0) 
						guild_remove_invites($gid);

					// Then remove the guild itself.
					guild_delete($gid);
					header('Location: success.php');
					exit();
				} else echo '<font color="red" size="4">All members must be offline to disband the guild.</font>';
			}
			
			// Change guild leader
			if (!empty($_POST['new_leader'])) {
				$new_leader = (int)$_POST['new_leader'];
				$old_leader = guild_leader($gid);

				$online = false;
				$newData['online'] = (user_is_online_10($new_leader)) ? 1 : 0;
				$oldData['online'] = (user_is_online_10($old_leader)) ? 1 : 0;

				if ($newData['online'] == 1 || $oldData['online'] == 1) $online = true;

				if ($online == false) {
					if (guild_change_leader($new_leader, $old_leader)) {
						header('Location: guilds.php?name='. $_GET['name']);
						exit();
					} else echo '<font color="red" size="4">Something went wrong when attempting to change leadership.</font>';
				} else echo '<font color="red" size="4">The new and old leader must be offline to change leadership.</font>';
			}
			
			// Change guild ranks
			if (!empty($_POST['change_ranks'])) {
				//$c_gid = (int)$_POST['change_ranks'];
				$c_ranks = get_guild_rank_data($gid);
				$rank_data = array();
				$rank_ids = array();

				// Feed new rank data
				foreach ($c_ranks as $rank) {
					$tmp = 'rank_name!'. $rank['level'];
					if (!empty($_POST[$tmp])) {
						$rank_data[$rank['level']] = sanitize($_POST[$tmp]);
						$rank_ids[$rank['level']] = $rank['id'];
					}
				}

				// Change guild rank name.
				foreach ($rank_data as $level => $name) {
					$rid = (int)$rank_ids[$level];
					$name = sanitize($name);
					mysql_update("UPDATE `guild_ranks` SET `name`='{$name}' WHERE `id`={$rid}");
				}

				header('Location: guilds.php?name='. $_GET['name']);
				exit();
			}
			?>
			
			<!-- forms to change rank titles -->
			<form action="" method="post">
				<ul>
					<li><b>Change rank titles:</b><br>
						<?php
						$rank_count = 1;
						foreach ($ranks as $rank) {
							echo '<input type="text" name="rank_name!'. $rank['level'] .'" value="'. $rank['name'] .'">';
						}
						echo '<input type="hidden" name="change_ranks" value="' . $gid . '" />';
						?>
						<input type="submit" value="Update Ranks">
					</li>
				</ul>
			</form>

			<!-- forms to disband guild -->
			<form action="" method="post">
				<ul>
					<li><b>DELETE GUILD (All members must be offline):</b><br>
						<?php echo '<input type="hidden" name="disband" value="' . $gid . '" />'; ?>
						<input type="submit" value="Disband Guild">
					</li>
				</ul>
			</form>

			<!-- forms to change leadership-->
			<?php if ($members > 1): ?>
				<form action="" method="post">
					<ul>
						<li><b>Change Leadership with:</b><br>
							<select name="new_leader">
							<?php
							//$gid = get_guild_id($_GET['name']);
							//$players = get_guild_players($gid);
							foreach ($players as $player) {
								if ($player['rank_level'] != 3) {
									echo '<option value="'. $player['id'] .'">'. $player['name'] .'</option>';
								}
							}
							?>
							</select>
							<input type="submit" value="Change Leadership">
						</li>
					</ul>
				</form>
			<?php endif; ?>

			<!-- Guild war management -->
			<?php if ($config['guildwar_enabled'] === true) { ?>
				<h2>Guild War Management:</h2>
				<form action="" method="post">
					<ul>
						<li>Invite guild to war:<br>
							<input type="text" name="warinvite" placeholder="Guild name">
							<input type="number" min="10" max="999" name="limit">
							<input type="submit" value="Invite Guild">
						</li>
					</ul>
				</form>

				<style type="text/css">
					form {display: inline;}
					#btnspace{margin-left:100px;}
				</style>
				
				<table id="guildsTable" class="table table-striped table-hover">
					<tr class="yellow">
						<th>Aggressor</th>
						<th>Information</th>
						<th>Enemy</th>
					</tr>
					<?php
					$i = 0;
					$wars = mysql_select_multi("
						SELECT 
							`guild1`, `guild2`, `name1`, `name2`, `started`, 
							(	SELECT `limit` 
								FROM `znote_guild_wars` 
								WHERE `znote_guild_wars`.`id` = `guild_wars`.`id`
							) AS `limit` 
						FROM `guild_wars` 
						WHERE (`guild1` = '$gid' OR `guild2` = '$gid') 
						AND `status` = 0 
						ORDER BY `started` DESC
					");
					if (!empty($wars) || $wars !== false) {
						foreach($wars as $war): 
							$i++;
							$row = "<tr>";
							$row .= "<td><a href='guilds.php?name={$war['name1']}'>{$war['name1']}</a></td>";
							
							$row .= '<td><center>';

							$row .= '<b>Pending invitation</b>';
							$row .= '<br />Invited on '. getClock($war['started'], true) .'.';
							$row .= "<br />The frag limit is set to {$war['limit']} frags.<br />";

							if ($war['guild1'] == $gid) {
								$row .= '<br><form action="" method="post">';
								$row .= "<input type='hidden' name='cancel_war_invite' value='{$war['guild2']}' />";
								$row .= '<input type="submit" value="Cancel Invitation">';
								$row .= '</form>';
							} else if ($war['guild2'] == $gid) {
								$row .= '<br><form action="" method="post">';
								$row .= "<input type='hidden' name='accept_war_invite' value='{$war['guild1']}' />";
								$row .= '<input type="submit" value="Accept Invitation">';
								$row .= '</form>';

								$row .= '<form action="" method="post">';
								$row .= "<input type='hidden' name='reject_war_invite' value='{$war['guild1']}' />";
								$row .= '<input type="submit" ID="btnspace" value="Reject Invitation">';
								$row .= '</form>';
							}
							$row .= '</center></td>';
							$row .= "<td><a href='guilds.php?name={$war['name2']}'>{$war['name2']}</a></td>";
							$row .= '</tr>';
							echo $row;
						endforeach;
					}
					
					if ($i == 0) echo '<tr><td colspan="3"><center>Currently there are no pending invitations.</center></td></tr>';
					?>
				</table>
				<?php
			}
		} // end leader+
	} // end user_logged_in() === true
	?>
	<!-- end leader and vice leader -->

	<?php
	if ($config['guildwar_enabled'] === true) {
		$wardata = get_guild_wars();
		$war_exist = false;
		if ($wardata !== false) {
			foreach ($wardata as $wars) {
				if ($wars['guild1'] == $gid || $wars['guild2'] == $gid) $war_exist = true;
			}
		}

		if ($war_exist) {
			?>
			<h2>War overview:</h2>
			<table>
				<tr class="yellow">
					<td>Attacker:</td>
					<td>Defender:</td>
					<td>status:</td>
					<td>started:</td>
				</tr>
				<?php
				foreach ($wardata as $wars): 
					if ($wars['guild1'] == $gid || $wars['guild2'] == $gid): 
						$url = url("guildwar.php?warid=". $wars['id']);
						?>
						<tr class="special" onclick="javascript:window.location.href='<?php echo $url; ?>'">
							<td><?php echo $wars['name1']; ?></td>
							<td><?php echo $wars['name2']; ?></td>
							<td><?php echo $config['war_status'][$wars['status']]; ?></td>
							<td><?php echo getClock($wars['started'], true); ?></td>
						</tr>
						<?php
					endif;
				endforeach;
				?>
			</table>
			<?php
		}
	}
	?>

	<!-- leave guild with character -->
	<?php
	$bool = false;
	if (user_logged_in() === true) {
		for ($i = 0; $i < $char_count; $i++) {
			foreach ($players as $player) {
				if ($player['name'] == $characters[$i]) $bool = true;
			}
		}
		if ($bool) {
			$forumExist = mysql_select_single("SELECT `id` FROM `znote_forum` WHERE `guild_id`='{$gid}' LIMIT 1;");
			if ($forumExist !== false) {
				?> - <font size="4"><a href="forum.php?cat=<?php echo $forumExist['id']; ?>">Visit Guild Board</a></font><br><br><br><?php
			}
			?>
			<form action="" method="post">
				<ul>
					<li>
						Leave Guild:<br>
						<select name="leave_guild">
							<option disabled>With...</option>
						<?php
						for ($i = 0; $i < $char_count; $i++) {
							foreach ($players as $player) {
								if ($player['name'] == $characters[$i]) {
									$data = get_player_guild_data($player['id']);
									if ($data['rank_level'] != 3) echo '<option value="'. $characters[$i] .'">'. $characters[$i] .'</option>';
									else echo '<option disabled>'. $characters[$i] .' [disabled:Leader]</option>';
								}
							}
						}
						?>
						</select>
						<input type="submit" value="Leave Guild">
					</li>
				</ul>
			</form>
		<?php
		} // display form if user has a character in guild
	} // user logged in
} // end view specific guild
include 'layout/overall/footer.php'; ?>
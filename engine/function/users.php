<?php
// TFS 1.0 functions ///

////////END 1.0 ////////

// Fetch Images
function fetchImages($status) {
	$status = (int)$status;
	return mysql_select_multi("SELECT `id`, `title`, `desc`, `date`, `status`, `image`, `account_id` FROM znote_images WHERE `status`='$status' ORDER BY `date` DESC;");
}

// Insert image data
function insertImage($account_id, $title, $desc, $image) {
	$title = sanitize($title);
	$desc = sanitize($desc);
	$image = sanitize($image);
	$account_id = (int)$account_id;
	$time = time();
	mysql_query("INSERT INTO `znote_images` (`title`, `desc`, `date`, `status`, `image`, `account_id`) VALUES ('$title', '$desc', '$time', '1', '$image', '$account_id');");
}

function updateImage($id, $status) {
	$id = (int)$id;
	$status = (int)$status;
	mysql_query("UPDATE `znote_images` SET `status`='$status' WHERE `id`='$id';");
}

// Fetch killers score
function fetchMurders() {
	return mysql_select_multi("SELECT `killed_by`, COUNT(`killed_by`) AS `kills` FROM `player_deaths` WHERE `is_player`='1' GROUP BY `killed_by` ORDER BY COUNT(`killed_by`) DESC LIMIT 0, 10;");
}

// Fetch deaths score
function fetchLoosers() {
	$array = mysql_select_multi("SELECT `player_id`, (SELECT `name` FROM `players` WHERE `id` = `player_id`) AS `name`, COUNT(`player_id`) AS `Deaths` FROM `player_deaths` GROUP BY `player_id` ORDER BY COUNT(`player_id`) DESC LIMIT 0, 10;");
	if ($array !== false) {
		for ($i = 0; $i < count($array); $i++) {
			unset($array[$i]['player_id']);
		}
	}
	return $array;
}

// Fetch latest deaths
function fetchLatestDeaths($from = 0, $to = 30) {
	$array = mysql_select_multi("SELECT * FROM `player_deaths` ORDER BY `time` DESC LIMIT $from, $to;");
	if ($array !== false) {
		for ($i = 0; $i < count($array); $i++) {
			$data = user_character_data($array[$i]['player_id'], 'name');
			$array[$i]['victim'] = $data['name'];
			unset($array[$i]['player_id']);
		}
	}
	return $array;
}

// latest deaths .3 (Based on code from Hauni@otland.net).
function fetchLatestDeaths_03($rowz = 30, $killers = false) {
	$countz = 0;
	if ($rowz === false || $killers === true) $getdeaths = mysql_select_multi("SELECT * FROM player_deaths ORDER BY date DESC;");
	else $getdeaths = mysql_select_multi("SELECT * FROM `player_deaths` ORDER BY `date` DESC LIMIT 0, $rowz;");
    $data = false;
	//while ($showdeaths = mysql_fetch_assoc($getdeaths)) {
    if ($getdeaths !== false) {
    	for ($i = 0; $i < count($getdeaths); $i++) {
	        $pid = $getdeaths[$i]['player_id'];  
	        $level = $getdeaths[$i]['level'];  
	        $kid = user_get_kid($getdeaths[$i]['id']); 
			
			$killedby = user_name(user_get_killer_id($kid)); 
			
			if ($killedby == false) {
				$killedby = user_get_killer_m_name($kid); 
				$player = 0; 
			} else {
				$player = 1; 
			}
			if ($killedby === false) {
				$player = 2;
				$killedby = "Deleted player.";
			}
	        $getname = mysql_select_single("SELECT `name` FROM `players` WHERE `id` = '$pid' LIMIT 1;");  
			$name = $getname['name'];
			$row = array();
			$row['level'] = $level;
			$row['victim'] = $name;
			$row['time'] = $getdeaths[$i]['date'];
			$row['is_player'] = $player;
			$row['killed_by'] = $killedby;
			if ($killers) {
				if ($player == 1) {
					if ($rowz !== false) {
						if ($countz < $rowz) {
							$data[] = $row;
							$countz++;
						}
					} else {
						$data[] = $row;
					}
				}
			} else $data[] = $row;
	    }
    }
	return $data;
}

// Bomberman Highscores
function bomberman_highscores() {
	// Fetch all players who have played bomberman more than 1 time:
	$totalGames = getPlayerStorageList(5006, 1);
	foreach ($totalGames as $game) {
		$char = array();
		$data = user_character_data($game['player_id'], 'name');
		$char['name'] = $data['name'];
		$char['wins'] = getPlayerStorage($game['player_id'], 5004);
		$char['losses'] = getPlayerStorage($game['player_id'], 5005);
		$char['frags'] = getPlayerStorage($game['player_id'], 5007);
		$char['deaths'] = getPlayerStorage($game['player_id'], 5008);
		$char['total_games'] = $game['value'];
		$array[] = $char;
	}
	if (!empty($array)) {return $array; } else {return false;}
}

// Support list
function support_list() {
	$TFS = Config('TFSVersion');
	if ($TFS == 'TFS_10') $staffs = mysql_select_multi("SELECT `id`, `group_id`, `name`, `account_id` FROM `players` WHERE `group_id` > 1 ORDER BY `group_id` ASC;");
	else $staffs = mysql_select_multi("SELECT `group_id`, `name`, `online`, `account_id` FROM `players` WHERE `group_id` > 1 ORDER BY `group_id` ASC;");
	for ($i = 0; $i < count($staffs); $i++) {
		// $staffs[$i]['']
		if ($TFS == 'TFS_02' || $TFS == 'TFS_10') {
			$account = mysql_select_single("SELECT `type` FROM `accounts` WHERE `id` ='". $staffs[$i]['account_id'] ."';");
			$staffs[$i]['group_id'] = $account['type'];
			if ($TFS == 'TFS_10') {
				// Fix online status on TFS 1.0
				if (user_is_online_10($staffs[$i]['id'])) $staffs[$i]['online'] = 1;
				else $staffs[$i]['online'] = 0;
				unset($staffs[$i]['id']);
			}
		}
		unset($staffs[$i]['account_id']);
	}
	return $staffs;
}

// NEWS
function fetchAllNews() {
	$query = mysql_query("SELECT * FROM `znote_news` ORDER BY `id` DESC;");
	$array = array();
	while($row = mysql_fetch_assoc($query)) {
		$data = user_character_data($row['pid'], 'name');
		$row['name'] = $data['name'];
		unset($row['pid']);
		$array[] = $row;
	}
	return !empty($array) ? $array : false;
}

// HOUSES
function fetchAllHouses_03() {
	$query = mysql_query("SELECT * FROM `houses`;") or die("ERROR");
	$array = array();
	while($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	}
	return !empty($array) ? $array : false;
}

// TFS Storage value functions (Warning, I think these things are saved in cache, 
// and thus require server to be offline, or affected players to be offline while using)

// Get player storage list
function getPlayerStorageList($storage, $minValue) {
	$minValue = (int)$minValue;
	$storage = (int)$storage;
	$query = mysql_query("SELECT `player_id`, `value` FROM `player_storage` WHERE `key`='$storage' AND `value`>='$minValue' ORDER BY `value` DESC;");
	$array = array();
	while($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	}
	return !empty($array) ? $array : false;
}

// Get global storage value
function getGlobalStorage($storage) {
	$storage = (int)$storage;
	$query = mysql_query("SELECT `value` FROM `global_storage` WHERE `key`='$storage';");
	$row = mysql_fetch_assoc($query);
	return !empty($row) ? $row['value'] : false;
}

// Set global storage value
function setGlobalStorage($storage, $value) {
	$storage = (int)$storage;
	$value = (int)$value;
	
	// If the storage does not exist yet
	if (getGlobalStorage($storage) === false) {
		mysql_query("INSERT INTO `global_storage` (`key`, `world_id`, `value`) VALUES ('$storage', 0, '$value')") or die(mysql_error());
	} else {// If the storage exist
		mysql_query("UPDATE `global_storage` SET `value`='$value' WHERE `key`='$storage'") or die(mysql_error());
	}
}

// Get player storage value.
function getPlayerStorage($player_id, $storage, $online = false) {
	if ($online) $online = user_is_online($player_id);
	if (!$online) {
		// user is offline (false), we may safely proceed:
		$player_id = (int)$player_id;
		$storage = (int)$storage;
		$query = mysql_query("SELECT `value` FROM `player_storage` WHERE `key`='$storage' AND `player_id`='$player_id';");
		$row = mysql_fetch_assoc($query);
		return !empty($row) ? $row['value'] : false;
	} else return false;
}

// Set player storage value
function setPlayerStorage($player_id, $storage, $value) {
	$storage = (int)$storage;
	$value = (int)$value;
	$player_id = (int)$player_id;
	
	// If the storage does not exist yet
	if (getPlayerStorage($storage) === false) {
		mysql_query("INSERT INTO `player_storage` (`player_id`, `key`, `value`) VALUES ('$player_id', '$storage', '$value')") or die(mysql_error());
	} else {// If the storage exist
		mysql_query("UPDATE `player_storage` SET `value`='$value' WHERE `key`='$storage' AND `player_id`='$player_id'") or die(mysql_error());
	}
}

// Is player online
function user_is_online($player_id) {
	$status = user_character_data($player_id, 'online');
	if ($status !== false) {
		if ($status['online'] == 1) $status = true;
		else $status = false;
	}
	return $status;
}
// For TFS 1.0
function user_is_online_10($player_id) {
	$player_id = (int)$player_id;
	$status = mysql_select_single("SELECT `player_id` FROM `players_online` WHERE `player_id`='$player_id' LIMIT 1;");
	return !$status ? $status : true;
}

// Shop
// Gets a list of tickets and ticket ids
function shop_delete_row_order($rowid) {
	$rowid = (int)$rowid;
	mysql_query("DELETE FROM `znote_shop_orders` WHERE `id`='$rowid';") or die(mysql_error());
}

function shop_update_row_count($rowid, $count) {
	$rowid = (int)$rowid;
	$count = (int)$count;
	mysql_query("UPDATE `znote_shop_orders` SET `count`='$count' WHERE `id`='$rowid'") or die(mysql_error());
}

function shop_account_gender_tickets($accid) {
	$accid = (int)$accid;
	$query = mysql_query("SELECT `id`, `count` FROM `znote_shop_orders` WHERE `account_id`='$accid' AND `type`='3';");
	$array = array();
	while($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	}
	return !empty($array) ? $array : false;
}

// GUILDS
//
function guild_remove_member($cid) {
	$cid = (int)$cid;
	mysql_update("UPDATE `players` SET `rank_id`='0' WHERE `id`=$cid");
}
function guild_remove_member_10($cid) {
	$cid = (int)$cid;
	mysql_update("DELETE FROM `guild_membership` WHERE `player_id`='$cid' LIMIT 1;");
}

// Change guild rank name.
function guild_change_rank($rid, $name) {
	$rid = (int)$rid;
	$name = sanitize($name);
	
	mysql_query("UPDATE `guild_ranks` SET `name`='$name' WHERE `id`=$rid") or die(mysql_error());
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
	
	$status = false;
	if ($leader_rid > 0 && $vice_rid > 0) $status = true;
	
	// Verify that we found the rank ids for vice leader and leader.
	if ($status) {
		
		// Update players and set their new rank id
		if (config('TFSVersion') !== 'TFS_10') {
			mysql_update("UPDATE `players` SET `rank_id`='$leader_rid' WHERE `id`=$nCid LIMIT 1;");
			mysql_update("UPDATE `players` SET `rank_id`='$vice_rid' WHERE `id`=$oCid LIMIT 1;");
		} else {
			mysql_update("UPDATE `guild_membership` SET `rank_id`='$leader_rid' WHERE `player_id`=$nCid LIMIT 1;");
			mysql_update("UPDATE `guild_membership` SET `rank_id`='$vice_rid' WHERE `player_id`=$oCid LIMIT 1;");
		}
		
		// Update guilds set new ownerid
		guild_new_leader($nCid, $gid);
	}
	
	return $status;
}

// Changes leadership of aguild to player_id
function guild_new_leader($new_leader, $gid) {
	$new_leader = (int)$new_leader;
	$gid = (int)$gid;
	mysql_query("UPDATE `guilds` SET `ownerid`='$new_leader' WHERE `id`=$gid") or die(mysql_error());
}

// Returns $gid of a guild leader($cid).
function guild_leader_gid($leader) {
	$leader = (int)$leader;
	$query = mysql_query("SELECT `id` FROM `guilds` WHERE `ownerid`='$leader';");
	$row = mysql_fetch_assoc($query);
	return !empty($row) ? $row['id'] : false;
}

// Returns guild leader(charID) of a guild. (parameter: guild_ID)
function guild_leader($gid) {
	$gid = (int)$gid;
	return mysql_result(mysql_query("SELECT `ownerid` FROM `guilds` WHERE `id`='$gid';"), 0, 'ownerid');
}

// Disband guild
function guild_remove_invites($gid) {
	$gid = (int)$gid;
	mysql_query("DELETE FROM `guild_invites` WHERE `guild_id`='$gid';");
}

// Remove guild invites
function guild_delete($gid) {
	$gid = (int)$gid;
	mysql_query("DELETE FROM `guilds` WHERE `id`='$gid';");
}

// Player leave guild
function guild_player_leave($cid) {
	$cid = (int)$cid;
	mysql_update("UPDATE `players` SET `rank_id`='0' WHERE `id`=$cid LIMIT 1;");
}
function guild_player_leave_10($cid) {
	$cid = (int)$cid;
	mysql_delete("DELETE FROM `guild_membership` WHERE `player_id`='$cid' LIMIT 1;");
}

// Player join guild
function guild_player_join($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	// Create a status we can return depending on results.
	$status = false;

	if (config('TFSVersion') !== 'TFS_10') {
		// Get rank data
		$ranks = get_guild_rank_data($gid);
		// Locate rank id for regular member position in this guild
		$rid = false;
		foreach ($ranks as $rank) {
			if ($rank['level'] == 1) $rid = $rank['id'];
		}
		// Add to guild if rank id was found:
		if ($rid != false) {
			// Remove the invite:
			guild_remove_invitation($cid, $gid);
			// Add to guild:
			mysql_update("UPDATE `players` SET `rank_id`='$rid' WHERE `id`=$cid");
			$status = true;
		}

	} else {
		// Find rank id for regular member in this guild
		$guildrank = mysql_select_single("SELECT `id` FROM `guild_ranks` WHERE `guild_id`='$gid' AND `level`='1' LIMIT 1;");
		if ($guildrank !== false) {
			$rid = $guildrank['id'];
			// Remove invite
			guild_remove_invitation($cid, $gid);
			// Add to guild
			mysql_insert("INSERT INTO `guild_membership` (`player_id`, `guild_id`, `rank_id`, `nick`) VALUES ('$cid', '$gid', '$rid', '');");
			// Return success
			return true;
		} return false;
	}
	return $status;
}

// Remove cid invitation from guild (gid)
function guild_remove_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	mysql_delete("DELETE FROM `guild_invites` WHERE `player_id`='$cid' AND `guild_id`='$gid';");
}

// Invite character to guild
function guild_invite_player($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	mysql_query("INSERT INTO `guild_invites` (`player_id`, `guild_id`) VALUES ('$cid', '$gid')") or die(mysql_error());
}

// Gets a list of invited players to a particular guild.
function guild_invite_list($gid) {
	$gid = (int)$gid;
	return mysql_select_multi("SELECT `player_id`, `guild_id` FROM `guild_invites` WHERE `guild_id`='$gid';");
}

// Update player's guild position
function update_player_guild_position($cid, $rid) {
	$cid = (int)$cid;
	$rid = (int)$rid;
	mysql_update("UPDATE `players` SET `rank_id`='$rid' WHERE `id`=$cid");
}
function update_player_guild_position_10($cid, $rid) {
	$cid = (int)$cid;
	$rid = (int)$rid;
	mysql_update("UPDATE `guild_membership` SET `rank_id`='$rid' WHERE `player_id`=$cid");
}

// Get guild data, using guild id.
function get_guild_rank_data($gid) {
	$gid = (int)$gid;
	$query = mysql_query("SELECT `id`, `guild_id`, `name`, `level` FROM `guild_ranks` WHERE `guild_id`='$gid' ORDER BY `id` DESC LIMIT 0, 30");
	$array = array();
	while($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	}
	return !empty($array) ? $array : false;
}

// Creates a guild, where cid is the owner of the guild, and name is the name of guild.
function create_guild($cid, $name) {
	$cid = (int)$cid;
	$name = sanitize($name);
	$time = time();
	
	// Create the guild
	mysql_insert("INSERT INTO `guilds` (`name`, `ownerid`, `creationdata`, `motd`) VALUES ('$name', '$cid', '$time', 'The guild has been created!');");

	// Get guild id
	$gid = get_guild_id($name);
	
	// Get rank id for guild leader
	$data = mysql_select_single("SELECT `id` FROM `guild_ranks` WHERE `guild_id`='$gid' AND `level`='3' LIMIT 1;");
	$rid = ($data !== false) ? $data['id'] : false;

	// Give player rank id for leader of his guild
	if (config('TFSVersion') !== 'TFS_10') mysql_update("UPDATE `players` SET `rank_id`='$rid' WHERE `id`='$cid' LIMIT 1;");
	else mysql_insert("INSERT INTO `guild_membership` (`player_id`, `guild_id`, `rank_id`, `nick`) VALUES ('$cid', '$gid', '$rid', '');");
}

// Search player table on cid for his rank_id, returns rank_id
function get_character_guild_rank($cid) {
	$cid = (int)$cid;
	if (config('TFSVersion') !== 'TFS_10') {
		$rid = mysql_result(mysql_query("SELECT `rank_id` FROM `players` WHERE `id`='$cid';"), 0, 'rank_id');
		return ($rid > 0) ? $rid : false;
	} else {
		$data = mysql_select_single("SELECT `rank_id` FROM `guild_membership` WHERE `player_id`='$cid' LIMIT 1;");
		return ($data !== false) ? $data['rank_id'] : false;
	}
}

// Get a player guild rank, using his rank_id
function get_player_guild_rank($rank_id) {
	$rank_id = (int)$rank_id;
	$data = mysql_select_single("SELECT `name` FROM `guild_ranks` WHERE `id`=$rank_id LIMIT 1;");
	return ($data !== false) ? $data['name'] : false;
}

// Get a player guild position ID, using his rank_id
function get_guild_position($rid) {
	$rid = (int)$rid;
	return mysql_result(mysql_query("SELECT `level` FROM `guild_ranks` WHERE `id`=$rid;"), 0, 'level');
}

// Get a players rank_id, guild_id, rank_level(ID), rank_name(string), using cid(player id)
function get_player_guild_data($cid) {
	$cid = (int)$cid;
	if (config('TFSVersion') !== 'TFS_10') $playerdata = mysql_select_single("SELECT `rank_id` FROM `players` WHERE `id`='$cid' LIMIT 1;");
	else $playerdata = mysql_select_single("SELECT `rank_id` FROM `guild_membership` WHERE `player_id`='$cid' LIMIT 1;");
	if ($playerdata !== false) {
		$rankdata = mysql_select_single("SELECT `guild_id`, `level` AS `rank_level`, `name` AS `rank_name` FROM `guild_ranks` WHERE `id`='". $playerdata['rank_id'] ."' LIMIT 1;");
		if ($rankdata !== false) {
			$rankdata['rank_id'] = $playerdata['rank_id'];
			return $rankdata;
		} else return false;
	} else return false;
}

// Returns guild name of guild id
function get_guild_name($gid) {
	$gid = (int)$gid;
	$guild = mysql_select_single("SELECT `name` FROM `guilds` WHERE `id`=$gid LIMIT 1;");
	if ($guild !== false) return $guild['name'];
	else return false;
}

// Returns guild id from name
function get_guild_id($name) {
	$name = sanitize($name);
	$query = mysql_query("SELECT `id` FROM `guilds` WHERE `name`='$name';");
	$row = mysql_fetch_assoc($query);
	
	return !empty($row) ? $row['id'] : false;
}

// Get complete list of guilds
function get_guilds_list() {
	return mysql_select_multi("SELECT `id`, `name`, `creationdata` FROM `guilds` ORDER BY `name`;");
}

// Get array of player data related to a guild.
function get_guild_players($gid) {
	$gid = (int)$gid; // Sanitizing the parameter id
	if (config('TFSVersion') !== 'TFS_10') return mysql_select_multi("SELECT p.rank_id, p.name, p.level, p.vocation FROM players AS p LEFT JOIN guild_ranks AS gr ON gr.id = p.rank_id WHERE gr.guild_id ='$gid';");
	else return mysql_select_multi("SELECT p.id, p.name, p.level, p.vocation, gm.rank_id FROM players AS p LEFT JOIN guild_membership AS gm ON gm.player_id = p.id WHERE gm.guild_id = '$gid';");
}

// Returns total members in a guild (integer)
function count_guild_members($gid) {
	$gid = (int)$gid;
	if (config('TFSVersion') !== 'TFS_10') {
		$data = mysql_select_single("SELECT COUNT(p.id) AS total FROM players AS p LEFT JOIN guild_ranks AS gr ON gr.id = p.rank_id WHERE gr.guild_id =$gid");
		return ($data !== false) ? $data['total'] : false;
	} else {
		$data = mysql_select_single("SELECT COUNT('guild_id') AS `total` FROM `guild_membership` WHERE `guild_id`='$gid';");
		return ($data !== false) ? $data['total'] : false;
	}
}

//
// GUILD WAR
//
// Returns guild war entry for id
function get_guild_war($warid) {
	$warid = (int)$warid; // Sanitizing the parameter id
	$query = mysql_query("SELECT `id`, `guild1`, `guild2`, `name1`, `name2`, `status`, `started`, `ended` FROM `guild_wars` WHERE `id`=$warid ORDER BY `started`;");
	$row = mysql_fetch_assoc($query);
	
	return !empty($row) ? $row : false;
}

// TFS 0.3 compatibility
function get_guild_war03($warid) {
	$warid = (int)$warid; // Sanitizing the parameter id
	$query = mysql_query("SELECT `id`, `guild_id`, `enemy_id`, `status`, `begin`, `end` FROM `guild_wars` ORDER BY `begin` DESC LIMIT 0, 30");
	$row = mysql_fetch_assoc($query);
	
	if (!empty($row)) {
		$row['guild1'] = $row['guild_id'];
		$row['guild2'] = $row['enemy_id'];
		$row['name1'] = get_guild_name($row['guild_id']);
		$row['name2'] = get_guild_name($row['enemy_id']);
		$row['started'] = $row['begin'];
		$row['ended'] = $row['end'];
	}
	
	return !empty($row) ? $row : false;
}

// List all war entries
function get_guild_wars() {
	return mysql_select_multi("SELECT `id`, `guild1`, `guild2`, `name1`, `name2`, `status`, `started`, `ended` FROM `guild_wars` ORDER BY `started` DESC LIMIT 0, 30");
}

/* TFS 0.3 compatibility
function get_guild_wars03() {
	$query = mysql_query("SELECT `id`, `guild_id`, `enemy_id`, `status`, `begin`, `end` FROM `guild_wars` ORDER BY `begin` DESC LIMIT 0, 30");
	$array = array();
	while($row = mysql_fetch_assoc($query)) {
		// Generating TFS 0.2 key values for this 0.3 query for web cross compatibility
		$row['guild1'] = $row['guild_id'];
		$row['guild2'] = $row['enemy_id'];
		$row['name1'] = get_guild_name($row['guild_id']);
		$row['name2'] = get_guild_name($row['enemy_id']);
		$row['started'] = $row['begin'];
		$row['ended'] = $row['end'];
		$array[] = $row;
	}
	return !empty($array) ? $array : false;
}*/

// Untested. (TFS 0.3 compatibility)
function get_guild_wars03() {
	$array = mysql_select_multi("SELECT `id`, `guild_id`, `enemy_id`, `status`, `begin`, `end` FROM `guild_wars` ORDER BY `begin` DESC LIMIT 0, 30");
	if ($array !== false) {
		for ($i = 0; $i < count($array); $i++) {
			// Generating TFS 0.2 key values for this 0.3 query for web cross compatibility
			$array[$i]['guild1'] = $array[$i]['guild_id'];
			$array[$i]['guild2'] = $array[$i]['enemy_id'];
			$array[$i]['name1'] = get_guild_name($array[$i]['guild_id']);
			$array[$i]['name2'] = get_guild_name($array[$i]['enemy_id']);
			$array[$i]['started'] = $array[$i]['begin'];
			$array[$i]['ended'] = $array[$i]['end'];
		}
	}
	return $array;
}

// List kill activity in wars.
function get_war_kills($war_id) {
	$war_id = (int)$war_id;// Sanitize - verify its an integer.
	
	$query = mysql_query("SELECT `id`, `killer`, `target`, `killerguild`, `targetguild`, `warid`, `time` FROM `guildwar_kills` WHERE `warid`=$war_id ORDER BY `time` DESC LIMIT 0, 30") or die("02 q");
	$array = array();
	while($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	} 
	return !empty($array) ? $array : false;
}

// TFS 0.3 compatibility
function get_war_kills03($war_id) {
	$war_id = (int)$war_id;// Sanitize - verify its an integer.
	
	$query = mysql_query("SELECT `id`, `guild_id`, `war_id`, `death_id` FROM `guild_kills` WHERE `war_id`=$war_id ORDER BY `id` DESC LIMIT 0, 30") or die("03 q");
	$array = array();
	while($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	} 
	return !empty($array) ? $array : false;
}

function get_death_data($did) {
	$did = (int)$did; // Sanitizing the parameter id
	$query = mysql_query("SELECT `id`, `guild_id`, `enemy_id`, `status`, `begin`, `end` FROM `guild_wars` ORDER BY `begin` DESC LIMIT 0, 30");
	$row = mysql_fetch_assoc($query);
	
	return !empty($row) ? $row : false;
}

// Gesior compatibility port TFS .3
function gesior_sql_death($warid) {
	$warid = (int)$warid; // Sanitizing the parameter id
	$query = mysql_query('SELECT `pd`.`id`, `pd`.`date`, `gk`.`guild_id` AS `enemy`, `p`.`name`, `pd`.`level` FROM `guild_kills` gk LEFT JOIN `player_deaths` pd ON `gk`.`death_id` = `pd`.`id` LEFT JOIN `players` p ON `pd`.`player_id` = `p`.`id` WHERE `gk`.`war_id` = ' . $warid . ' AND `p`.`deleted` = 0 ORDER BY `pd`.`date` DESC');
	while($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	} 
	return !empty($array) ? $array : false;
}
function gesior_sql_killer($did) {
	$did = (int)$did; // Sanitizing the parameter id
	$query = mysql_query('SELECT `p`.`name` AS `player_name`, `p`.`deleted` AS `player_exists`, `k`.`war` AS `is_war` FROM `killers` k LEFT JOIN `player_killers` pk ON `k`.`id` = `pk`.`kill_id` LEFT JOIN `players` p ON `p`.`id` = `pk`.`player_id` WHERE `k`.`death_id` = ' . $did . ' ORDER BY `k`.`final_hit` DESC, `k`.`id` ASC');
	while($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	} 
	return !empty($array) ? $array : false;
}
// end gesior
// END GUILD WAR
// ADMIN FUNCTIONS
function set_ingame_position($name, $acctype) {
	$acctype = (int)$acctype;
	$name = sanitize($name);
	
	$acc_id = user_character_account_id($name);
	$char_id = user_character_id($name);
	
	$group_id = 2;
	if ($acctype == 1) {
		$group_id = 1;
	}
	mysql_query("UPDATE `accounts` SET `type` = '$acctype' WHERE `id` =$acc_id;");
	mysql_query("UPDATE `players` SET `group_id` = '$group_id' WHERE `id` =$char_id;");
}

// .3
function set_ingame_position03($name, $acctype) {
	$acctype = (int)$acctype;
	$name = sanitize($name);
	
	$acc_id = user_character_account_id($name);
	$char_id = user_character_id($name);
	
	$group_id = 2;
	if ($acctype == 1) {
		$group_id = 1;
	}
	mysql_query("UPDATE `players` SET `group_id` = '$acctype' WHERE `id` =$char_id;");
}

// Set rule violation. 
// Return true if success, query error die if failed, and false if $config['website_char'] is not recognized.
function set_rule_violation($charname, $typeid, $actionid, $reasonid, $time, $comment) {
	$charid = user_character_id($charname);
	$typeid = (int)$typeid;
	$actionid = (int)$actionid;
	$reasonid = (int)$reasonid;
	$time = (int)($time + time());
	
	$data = user_character_data($charid, 'account_id', 'lastip');
	
	$accountid = $data['account_id'];
	$charip = $data['lastip'];
	
	$comment = sanitize($comment);
	
	// ...
	$bannedby = config('website_char');
	if (user_character_exist($bannedby)) {
		$bannedby = user_character_id($bannedby);
		
		if (Config('TFSVersion') === 'TFS_02')
		mysql_query("INSERT INTO `bans` (`type` ,`ip` ,`mask` ,`player` ,`account` ,`time` ,`reason_id` ,`action_id` ,`comment` ,`banned_by`) VALUES ('$typeid', '$charip', '4294967295', '$charid', '$accountid', '$time', '$reasonid', '$actionid', '$comment', '$bannedby');") or die(mysql_error());
		if (Config('TFSVersion') === 'TFS_03') {
			$now = time();
			switch ($typeid) {
				case 1: // IP ban
					mysql_query("INSERT INTO `bans` (`type`, `value`, `param`, `active`, `expires`, `added`, `admin_id`, `comment`) VALUES ('$typeid', '$charip', '4294967295', '1', '$time', '$now', '$bannedby', '$comment');") or die(mysql_error());
				break;
				
				case 2: // namelock
					mysql_query("INSERT INTO `bans` (`type`, `value`, `param`, `active`, `expires`, `added`, `admin_id`, `comment`) VALUES ('$typeid', '$charid', '4294967295', '1', '$time', '$now', '$bannedby', '$comment');") or die(mysql_error());
				break;
				
				case 3: // acc ban
					mysql_query("INSERT INTO `bans` (`type`, `value`, `param`, `active`, `expires`, `added`, `admin_id`, `comment`) VALUES ('$typeid', '$accountid', '4294967295', '1', '$time', '$now', '$bannedby', '$comment');") or die(mysql_error());
				break;
				
				case 4: // notation
					mysql_query("INSERT INTO `bans` (`type`, `value`, `param`, `active`, `expires`, `added`, `admin_id`, `comment`) VALUES ('$typeid', '$charid', '4294967295', '1', '$time', '$now', '$bannedby', '$comment');") or die(mysql_error());
				break;
				
				case 5: // deletion
					mysql_query("INSERT INTO `bans` (`type`, `value`, `param`, `active`, `expires`, `added`, `admin_id`, `comment`) VALUES ('$typeid', '$charid', '4294967295', '1', '$time', '$now', '$bannedby', '$comment');") or die(mysql_error());
				break;
			}
			
		}
		
		return true;
	} else {
		return false;
	}
}

// -- END admin
// Fetch deathlist
function user_fetch_deathlist($char_id) {
	$char_id = (int)$char_id;
	return mysql_select_multi("SELECT * FROM `player_deaths` WHERE `player_id`='$char_id' order by `time` DESC LIMIT 0, 10");
}

// TFS .3 compatibility
function user_fetch_deathlist03($char_id) {
	$char_id = (int)$char_id;
	$query = mysql_query("SELECT * FROM `player_deaths` WHERE `player_id`='$char_id' order by `date` DESC LIMIT 0, 10") or die(mysql_error());
	
	while($row = mysql_fetch_assoc($query)) {
		$row['time'] = $row['date'];
		$array[] = $row;
	} 
	return !empty($array) ? $array : false;
}

// same (death id ---> killer id)
function user_get_kid($did) {
	$did = (int)$did;
	return mysql_result(mysql_query("SELECT `id` FROM `killers` WHERE `death_id`='$did';"), 0, 'id');
}
// same (killer id ---> player id)
function user_get_killer_id($kn) {
	$kn = (int)$kn;
	$query = mysql_query("SELECT `player_id` FROM `player_killers` WHERE `kill_id`='$kn';") or die(mysql_error());
	$count = mysql_num_rows($query);
	for ($i = 0; $i < $count; $i++) {
		$row = mysql_fetch_row($query);
	}
	
	if (isset($row)) { return $row[0]; } else {return false;}
}
// same (killer id ---> monster name)
function user_get_killer_m_name($mn) {
	$mn = (int)$mn;
	
	$query = mysql_query("SELECT `name` FROM `environment_killers` WHERE `kill_id`='$mn';");
	$data = mysql_fetch_assoc($query);
	
	//return $data;
	return mysql_num_rows($query) !== 1 ? false : $data['name'];
}

// Count character deaths. Counts up 10.
function user_count_deathlist($char_id) {
	$char_id = (int)$char_id;
	return mysql_result(mysql_query("SELECT COUNT('id') FROM `player_deaths` WHERE `player_id`='$char_id' order by `time` DESC LIMIT 0, 10"), 0);
}

// MY ACCOUNT RELATED \\
function user_update_comment($char_id, $comment) {
	$char_id = sanitize($char_id);
	$comment = sanitize($comment);
	mysql_query("UPDATE `znote_players` SET `comment`='$comment' WHERE `player_id`='$char_id'");
}

// Permamently delete character id. (parameter: character id)
function user_delete_character($char_id) {
	$char_id = (int)$char_id;
	mysql_query("DELETE FROM `players` WHERE `id`='$char_id';");
	mysql_query("DELETE FROM `znote_players` WHERE `player_id`='$char_id';");
}

// Parameter: accounts.id returns: An array containing detailed information of every character on the account.
// Array: [0] = name, [1] = level, [2] = vocation, [3] = town_id, [4] = lastlogin, [5] = online
function user_character_list($account_id) {
	//$count = user_character_list_count($account_id);
	$account_id = (int)$account_id;

	if (config('TFSVersion') == 'TFS_10') {
		$characters = mysql_select_multi("SELECT `id`, `name`, `level`, `vocation`, `town_id`, `lastlogin` FROM `players` WHERE `account_id`='$account_id' ORDER BY `level` DESC");
		if ($characters !== false) {
			$onlineArray = mysql_select_multi("SELECT `player_id` FROM `players_online`;");
			$onlineIds = array();
			if ($onlineArray !== false) foreach ($onlineArray as $row) $onlineIds[] = $row['player_id'];
			for ($i = 0; $i < count($characters); $i++) {
				$online = in_array($characters[$i]['id'], $onlineIds);
				if ($online) $characters[$i]['online'] = 1;
				else $characters[$i]['online'] = 0;
				unset($characters[$i]['id']);
			}
		}

	} else $characters = mysql_select_multi("SELECT `name`, `level`, `vocation`, `town_id`, `lastlogin`, `online` FROM `players` WHERE `account_id`='$account_id' ORDER BY `level` DESC");

	if ($characters !== false) {
		$count = count($characters);
		for ($i = 0; $i < $count; $i++) {
			$characters[$i]['vocation'] = vocation_id_to_name($characters[$i]['vocation']); // Change vocation id to vocation name
			$characters[$i]['town_id'] = town_id_to_name($characters[$i]['town_id']); // Change town id to town name
			
			// Make lastlogin human read-able. 
			if ($characters[$i]['lastlogin'] != 0) {
				$characters[$i]['lastlogin'] = getClock($characters[$i]['lastlogin'], true, false);
			} else {
				$characters[$i]['lastlogin'] = 'Never.';
			}
			
			$characters[$i]['online'] = online_id_to_name($characters[$i]['online']); // 0 to "offline", 1 to "ONLINE". 
		}
	}
	
	return $characters;
}

// Returns an array containing all(up to 30) player_IDs an account have. (parameter: account_ID).
function user_character_list_player_id($account_id) {
	//$count = user_character_list_count($account_id);
	$account_id = sanitize($account_id);
	$query = mysql_query("SELECT `id` FROM `players` WHERE `account_id`='$account_id' ORDER BY `level` DESC LIMIT 0, 30");
	$count = mysql_num_rows($query);
	for ($i = 0; $i < $count; $i++) {
		$row = mysql_fetch_row($query);
		$array[] = $row[0];
	}
	if (isset($array)) {return $array; } else {return false;}
}

// Parameter: accounts.id returns: number of characters on the account.
function user_character_list_count($account_id) {
	$account_id = sanitize($account_id);
	return mysql_result(mysql_query("SELECT COUNT('id') FROM `players` WHERE `account_id`='$account_id'"), 0);
}

// END MY ACCOUNT RELATED

// HIGHSCORE FUNCTIONS \\(I think I will move this to an own file later)
function highscore_getAll() {
	$result = array();
	for ($i = 0; $i <= 6; $i++) {
		$result[$i] = highscore_skills($i);
	}
	$result[7] = highscore_experience();
	$result[8] = highscore_maglevel();

	return $result;
}

// TFS 1.0 highscore
function highscore_getAll_10($from = 0, $to = 30) {
	$result = array();
	for ($i = 0; $i <= 8; $i++) {
		$result[$i] = highscore_getSkill_10($i, $from, $to);
	}
	return $result;
}
function highscore_getSkill_10($id = 8, $from = 0, $to = 30) {
	$skills = array(
		0 => 'skill_fist',
		1 => 'skill_club',
		2 => 'skill_sword',
		3 => 'skill_axe',
		4 => 'skill_dist',
		5 => 'skill_shielding',
		6 => 'skill_fishing',
		8 => 'maglevel',
		7 => 'level',
	);
	
	if ($id < 7 || $id > 7) $scores = mysql_select_multi("SELECT `". $skills[$id] ."` AS `value`, `name`, `vocation` FROM `players` WHERE `group_id`<'2' ORDER BY `". $skills[$id] ."` DESC LIMIT {$from}, {$to};");
	else $scores = mysql_select_multi("SELECT `". $skills[$id] ."` AS `level`, `experience` AS `value`, `name`, `vocation` FROM `players` WHERE `group_id`<'2' ORDER BY `experience` DESC LIMIT {$from}, {$to};");
	for ($i = 0; $i < count($scores); $i++) $scores[$i]['vocation'] = vocation_id_to_name($scores[$i]['vocation']);
	return $scores;
}

// Returns an array containing up to 30 best players in terms of (selected skillid). Returns player ID and skill value.
function highscore_skills($skillid) {
	$skillid = (int)$skillid;
	$query = mysql_query("SELECT `player_id`, `value` FROM `player_skills` WHERE `skillid`='$skillid' ORDER BY `value` DESC LIMIT 0, 30");
	while ($row = mysql_fetch_assoc($query)) {
		if ($skillid == 6 || $skillid == 5) {// If skillid is fish fighting, lets display vocation name instead of id.
			$row['vocation'] = vocation_id_to_name(mysql_result(mysql_query("SELECT `vocation` FROM `players` WHERE `id` = '". $row['player_id'] ."';"), 0));
		}
		$row['group_id'] = mysql_result(mysql_query("SELECT `group_id` FROM `players` WHERE `id` = '". $row['player_id'] ."';"), 0);
		$row['name'] = mysql_result(mysql_query("SELECT `name` FROM `players` WHERE `id` = '". $row['player_id'] ."';"), 0);
		unset($row['player_id']);
		$array[] = $row;
	}
	if (isset($array)) {return $array; } else {return false;}
}

// Returns an array containing up to 30 best players in terms of experience. Returns name, experience, vocation and level. 
function highscore_experience() {
	//$count = highscore_experience_count();
	$query = mysql_query("SELECT `name`, `experience` as `value`, `vocation`, `level`, `group_id` FROM `players` WHERE `experience`>500 ORDER BY `experience` DESC LIMIT 0, 30");
	while ($row = mysql_fetch_assoc($query)) {
		$row['vocation'] = vocation_id_to_name($row['vocation']);
		$array[] = $row;
	}
	if (isset($array)) {return $array; } else {return false;}
}

// Returns an array containing up to 30 best players with high magic level (returns their name and magic level)
function highscore_maglevel() {
	//$count = highscore_experience_count(); // Dosn't matter if I count exp, maglvl is on same table.
	$query = mysql_query("SELECT `name`, `maglevel` as `value`, `group_id` FROM `players` WHERE `experience`>500 ORDER BY `maglevel` DESC LIMIT 0, 30");
	while ($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	}
	if (isset($array)) {return $array; } else {return false;}
}

// Count how many skill entries are in the db for a certain skillid (this can relate to how many players exist). 
function highscore_count($skillid) {
	return mysql_result(mysql_query("SELECT COUNT(`player_id`) FROM `player_skills` WHERE `skillid`='$skillid' LIMIT 0, 30"), 0);
}

// Count how many players have higher exp than 500
function highscore_experience_count() {
	return mysql_result(mysql_query("SELECT COUNT(`id`) FROM `players` WHERE `experience`>'500' LIMIT 0, 30"), 0);
}

// END HIGHSCORE FUNCTIONS
function user_recover($mode, $edom, $email, $character, $ip) {
/* -- Lost account function - user_recovery --

	$mode = username/password recovery definition
	$edom = The remembered value. (if mode is username, edom is players password and vice versa)
	$email = email address
	$character = character name
	$ip = character IP
*/
	// Structure verify array data correctly
	if ($mode === 'username') {
		$verify_data = array(
			'password' => sha1($edom),
			'email' => $email
		);
	} else {
		$verify_data = array(
			'name' => $edom,
			'email' => $email
		);
	}
	// Determine if the submitted information is correct and herit from same account
	if (user_account_fields_verify_value($verify_data)) {
		
		// Structure account id fetch method correctly
		if ($mode == 'username') {
			$account_id = user_account_id_from_password($verify_data['password']);
		} else {
			$account_id = user_id($verify_data['name']);
		}
		// get account id from character name
		$player_account_id = user_character_account_id($character);
		
		//Verify that players.account_id matches account.id
		if ($player_account_id == $account_id) {
			// verify IP match (IP = accounts.email_new) \\
			// Fetch IP data
			$ip_data = user_znote_account_data($account_id, 'ip');
			if ($ip == $ip_data['ip']) {
				// IP Match, time to stop verifying SHIT and get on
				// With giving the visitor his goddamn username/password!
				if ($mode == 'username') {
					$name_data = user_data($account_id, 'name');
					echo '<br><p>Your username is:</p> <h3>'. $name_data['name'] .'</h3>';
				} else {
					$newpass = substr(sha1(rand(1000000, 99999999)), 8);
					echo '<br><p>Your new password is:</p> <h3>'. $newpass .'</h3><p>Remember to login and change it!</p>';
					user_change_password($account_id, $newpass);
				}
				// END?! no, but almost. :)
			} else { echo'IP does not match.'; }
		} else { echo'Account data does not match.'; }
	} else { echo'Account data does not match.'; }
}

// Get account id from password. This can be inaccurate considering several people may have same password.
function user_account_id_from_password($password) {
	$password = sanitize($password);
	$tmp = mysql_select_single("SELECT `id` FROM `accounts` WHERE `password`='".$password."' LIMIT 1;");
	return $tmp['id'];
}

// Add additional premium days to account id
function user_account_add_premdays($accid, $days) {
	$accid = (int)$accid;
	$days = (int)$days;
	$tmp = mysql_result(mysql_query("SELECT `premdays` FROM `accounts` WHERE `id`='$accid';"), 0, 'premdays');
	$tmp += $days;
	mysql_query("UPDATE `accounts` SET `premdays`='$tmp' WHERE `id`='$accid'");
}

// Name = char name. Changes from male to female & vice versa.
function user_character_change_gender($name) {
	$user_id = user_character_id($name);
	$gender = mysql_result(mysql_query("SELECT `sex` FROM `players` WHERE `id`='$user_id';"), 0, 'sex');
	if ($gender == 1) mysql_query("UPDATE `players` SET `sex`='0' WHERE `id`='$user_id'");
	else mysql_query("UPDATE `players` SET `sex`='1' WHERE `id`='$user_id'");
}

// Fetch account ID from player NAME
function user_character_account_id($character) {
	$character = sanitize($character);
	return mysql_result(mysql_query("SELECT `account_id` FROM `players` WHERE `name`='$character';"), 0, 'account_id');
}

// Verify data from accounts table. Parameter is an array of <columnName> - <data to verify>
// etc array('id' = 4, 'password' = 'test') will verify that logged in user have id 4 and password test.
function user_account_fields_verify_value($verify_data) {
	$verify = array();
	array_walk($verify_data, 'array_sanitize');
	
	foreach ($verify_data as $field=>$data) {
		$verify[] = '`'. $field .'` = \''. $data .'\'';
	}
	return (mysql_result(mysql_query("SELECT COUNT('id') FROM `accounts` WHERE ". implode(' AND ', $verify) .";"), 0) == 1) ? true : false;
}

// Update accounts, make sure user is logged in first.
function user_update_account($update_data) {
	$update = array();
	array_walk($update_data, 'array_sanitize');
	
	foreach ($update_data as $field=>$data) {
		$update[] = '`'. $field .'` = \''. $data .'\'';
	}
	
	$user_id = sanitize($_SESSION['user_id']);
	
	mysql_query("UPDATE `accounts` SET ". implode(', ', $update) ." WHERE `id`=". $user_id .";");
}

// Update znote_accounts table, make sure user is logged in for this. This is used to etc update lastIP
function user_update_znote_account($update_data) {
	$update = array();
	array_walk($update_data, 'array_sanitize');
	
	foreach ($update_data as $field=>$data) {
		$update[] = '`'. $field .'` = \''. $data .'\'';
	}
	
	$user_id = sanitize($_SESSION['user_id']);
	
	mysql_query("UPDATE `znote_accounts` SET ". implode(', ', $update) ." WHERE `account_id`=". $user_id .";");
}

// Change password on account_id (Note: You should verify that he knows the old password before doing this)
function user_change_password($user_id, $password) {
	$user_id = sanitize($user_id);
	$password = sha1($password);
	
	mysql_query("UPDATE `accounts` SET `password`='$password' WHERE `id`=$user_id");
}
// .3 compatibility
function user_change_password03($user_id, $password) {
	if (config('salt') === true) {
		$user_id = sanitize($user_id);
		$salt = user_data($user_id, 'salt');
		$password = sha1($salt['salt'].$password);
		
		mysql_query("UPDATE `accounts` SET `password`='$password' WHERE `id`=$user_id");
	} else {
		user_change_password($user_id, $password);
	}
}

// Parameter: players.id, value[0 or 1]. Togge hide.
function user_character_set_hide($char_id, $value) {
	$char_id = sanitize($char_id);
	$value = sanitize($value);
	
	mysql_query("UPDATE `znote_players` SET `hide_char`='$value' WHERE `player_id`=$char_id");
}

// CREATE ACCOUNT
function user_create_account($register_data) {
	array_walk($register_data, 'array_sanitize');
	
	if (config('TFSVersion') == 'TFS_03' && config('salt') === true) {
		$register_data['salt'] = generate_recovery_key(18);
		$register_data['password'] = sha1($register_data['salt'].$register_data['password']);
	} else $register_data['password'] = sha1($register_data['password']);
	
	$ip = $register_data['ip'];
	$created = $register_data['created'];
	
	unset($register_data['ip']);
	unset($register_data['created']);
	
	if (config('TFSVersion') == 'TFS_10') $register_data['creation'] = $created;

	$fields = '`'. implode('`, `', array_keys($register_data)) .'`';
	$data = '\''. implode('\', \'', $register_data) .'\'';

	mysql_query("INSERT INTO `accounts` ($fields) VALUES ($data)") or die(mysql_error());
	
	$account_id = user_id($register_data['name']);
	mysql_query("INSERT INTO `znote_accounts` (`account_id`, `ip`, `created`) VALUES ('$account_id', '$ip', '$created')") or die(mysql_error());
	
	//TO-DO: mail server and verification.
	// http://www.web-development-blog.com/archives/send-e-mail-messages-via-smtp-with-phpmailer-and-gmail/
}

// CREATE CHARACTER
function user_create_character($character_data) {
	array_walk($character_data, 'array_sanitize');
	$cnf = fullConfig();
	
	if ($character_data['sex'] == 1) {
		$outfit_type = $cnf['maleOutfitId'];
	} else {
		$outfit_type = $cnf['femaleOutfitId'];
	}
	
	// This is TFS 0.2 compatible import data with Znote AAC mysql schema
	$import_data = array(
		'name'	=>	$character_data['name'],
		'group_id' => 1,
		'account_id' => $character_data['account_id'],
		'level' => $cnf['level'],
		'vocation'	=>	$character_data['vocation'],
		'health' => $cnf['health'],
		'healthmax' => $cnf['health'],
		'experience' => 0, /* Will automatically be configured according to level after creating this array*/
		'lookbody' => 0, /* STARTER OUTFITS */
		'lookfeet' => 0,
		'lookhead' => 0,
		'looklegs' => 0,
		'looktype' => $outfit_type,
		'lookaddons' => 0,
		'maglevel' => 0,
		'mana' => $cnf['mana'],
		'manamax' => $cnf['mana'],
		'manaspent' => 0,
		'soul' => $cnf['soul'],
		'town_id'	=>	$character_data['town_id'],
		'posx' => $cnf['default_pos']['x'],
		'posy' => $cnf['default_pos']['y'],
		'posz' => $cnf['default_pos']['z'],
		'conditions' => '',
		'cap' => $cnf['cap'],
		'sex' => $character_data['sex'],
		'lastlogin' => 0,
		'lastip'	=>	$character_data['lastip'],
		'save' => 1,
		'skull' => 0,
		'skulltime' => 0,
		'rank_id' => 0,
		'guildnick' => '',
		'lastlogout' => 0,
		'blessings' => 0,
		'direction' => 0,
		'loss_experience' => 10,
		'loss_mana' => 10,
		'loss_skills' => 10,
		'premend' => 0,
		'online' => 0,
		'balance' => 0
	);
	
	// TFS 1.0 rules
	if (Config('TFSVersion') === 'TFS_10') {
		unset($import_data['rank_id']);
		unset($import_data['guildnick']);
		unset($import_data['direction']);
		unset($import_data['loss_experience']);
		unset($import_data['loss_mana']);
		unset($import_data['loss_skills']);
		unset($import_data['loss_mana']);
		unset($import_data['premend']);
		unset($import_data['online']);
	}

	// Set correct experience for level
	$import_data['experience'] = level_to_experience($import_data['level']);
	
	// If you are no vocation (id 0), use these details instead:
	if ($character_data['vocation'] === '0') {
		$import_data['level'] = $cnf['nvlevel'];
		$import_data['experience'] = level_to_experience($cnf['nvlevel']);
		$import_data['health'] = $cnf['nvHealth'];
		$import_data['healthmax'] = $cnf['nvHealth'];
		$import_data['cap'] = $cnf['nvCap'];
		$import_data['mana'] = $cnf['nvMana'];
		$import_data['manamax'] = $cnf['nvMana'];
		$import_data['soul'] = $cnf['nvSoul'];
		
		if ($cnf['nvForceTown'] == 1) {
			$import_data['town_id'] = $cnf['nvTown'];
		}
	}
	
	$fields = array_keys($import_data); // Fetch select fields
	$data = array_values($import_data); // Fetch insert data
	
	$fields_sql = implode("`, `", $fields); // Convert array into SQL compatible string
	$data_sql = implode("', '", $data); // Convert array into SQL compatible string
	echo 1;
	mysql_query("INSERT INTO `players`(`$fields_sql`) VALUES ('$data_sql');") or die("INSERT ERROR: ". mysql_error());
	
	$created = time();
	$charid = user_character_id($import_data['name']);
	echo 2;
	mysql_query("INSERT INTO `znote_players`(`player_id`, `created`, `hide_char`, `comment`) VALUES ('$charid', '$created', '0', '');") or die(mysql_error());
}

// Returns counted value of all players online
function user_count_online() {
	if (config('TFSVersion') == 'TFS_10') {
		$online = mysql_select_single("SELECT COUNT(`player_id`) AS `value` FROM `players_online`;");
		return $online['value'];
	} else return mysql_result(mysql_query("SELECT COUNT(`id`) from `players` WHERE `online` = 1;"), 0);
}

// Returns counted value of all accounts.
function user_count_accounts() {
	return mysql_result(mysql_query("SELECT COUNT(`id`) from `accounts`;"), 0);
}

/* user_character_data (fetches whatever data you want from players table)!
	Usage:
	$player = user_data(player_ID, 'name', 'level');
	echo "Character name: ". $player['name'] .". Level: ". $player['level'];
*/
function user_character_data($user_id) {
	$data = array();
	$user_id = sanitize($user_id);
	$func_num_args = func_num_args();
	$func_get_args = func_get_args();
	if ($func_num_args > 1)  {
		unset($func_get_args[0]);
		$fields = '`'. implode('`, `', $func_get_args) .'`';
		$data = mysql_select_single("SELECT $fields FROM `players` WHERE `id` = $user_id;");
		return $data;
	}
}

// return query data from znote_players table
function user_znote_character_data($character_id) {
	$data = array();
	$charid = (int)$character_id;
	
	$func_num_args = func_num_args();
	$func_get_args = func_get_args();
	
	if ($func_num_args > 1)  {
		unset($func_get_args[0]);
		
		$fields = '`'. implode('`, `', $func_get_args) .'`';
		$data = mysql_select_single("SELECT $fields FROM `znote_players` WHERE `player_id` = $charid;");
		return $data;
	}
}

// return query data from znote table
// usage: $znoteAAC = user_znote_data('version');
// echo $znoteAAC['version'];
function user_znote_data() {
	$data = array();
	
	$func_num_args = func_num_args();
	$func_get_args = func_get_args();
	
	if ($func_num_args > 0)  {
		
		$fields = '`'. implode('`, `', $func_get_args) .'`';
		$data = mysql_fetch_assoc(mysql_query("SELECT $fields FROM `znote`;"));
		return $data;
	} else return false;
}

// return query data from znote_accounts table
// See documentation on user_data. This fetches information from znote_accounts table.
function user_znote_account_data($account_id) {
	$data = array();
	$accid = (int)$account_id;
	
	$func_num_args = func_num_args();
	$func_get_args = func_get_args();
	
	if ($func_num_args > 1)  {
		unset($func_get_args[0]);
		
		$fields = '`'. implode('`, `', $func_get_args) .'`';
		$data = mysql_select_single("SELECT $fields FROM `znote_accounts` WHERE `account_id` = $accid LIMIT 1;");
		return $data;
	}
}

// return query data from znote_visitors table
// See documentation on user_data, but this uses $longip instead. 
function user_znote_visitor_data($longip) {
	$data = array();
	$longip = (int)$longip;
	
	$func_num_args = func_num_args();
	$func_get_args = func_get_args();
	
	if ($func_num_args > 1)  {
		unset($func_get_args[0]);
		
		$fields = '`'. implode('`, `', $func_get_args) .'`';
		$data = mysql_fetch_assoc(mysql_query("SELECT $fields FROM `znote_visitors` WHERE `ip` = $longip;"));
		return $data;
	}
}

// return query data from znote_visitors_details table
// See documentation on user_data, but this uses $longip instead. 
function user_znote_visitor_details_data($longip) {
	$data = array();
	$longip = (int)$longip;
	
	$func_num_args = func_num_args();
	$func_get_args = func_get_args();
	
	if ($func_num_args > 1)  {
		unset($func_get_args[0]);
		
		$fields = '`'. implode('`, `', $func_get_args) .'`';
		$data = mysql_fetch_assoc(mysql_query("SELECT $fields FROM `znote_visitors_details` WHERE `ip` = $longip;"));
		return $data;
	}
}

/* user_data (fetches whatever data you want from accounts table)!
	Usage:
	$account = user_data(account_ID, 'password', 'email');
	echo $account['email']; //Will then echo out that accounts mail address.
*/
function user_data($user_id) {
	$data = array();
	$user_id = sanitize($user_id);
	
	$func_num_args = func_num_args();
	$func_get_args = func_get_args();
	
	if ($func_num_args > 1)  {
		unset($func_get_args[0]);
		
		$fields = '`'. implode('`, `', $func_get_args) .'`';
		$data = mysql_select_single("SELECT $fields FROM `accounts` WHERE `id` = $user_id LIMIT 1;");
		return $data;
	}
}

// Checks if user is activated (Not in use atm)
function user_activated($username) {
	$username = sanitize($username);
	// Deprecated, removed from DB.
	//return (mysql_result(mysql_query("SELECT COUNT('id') FROM `accounts` WHERE `name`='$username' AND `email_new_time`=1;"), 0) == 1) ? true : false;
	return false;
}

// Checks that username exist in database
function user_exist($username) {
	$username = sanitize($username);
	return (mysql_result(mysql_query("SELECT COUNT('id') FROM `accounts` WHERE `name`='$username';"), 0) == 1) ? true : false;
}

function user_name($id) { //USERNAME FROM PLAYER ID
    $id = (int)$id; 
    $name = mysql_select_single("SELECT `name` FROM `players` WHERE `id`='$id';");
    if ($name !== false) return $name['name'];
    else return false;
}

// Checks that character name exist
function user_character_exist($username) {
	$username = sanitize($username);
	return (mysql_result(mysql_query("SELECT COUNT('id') FROM `players` WHERE `name`='$username';"), 0) == 1) ? true : false;
}

// Checks that this email exist.
function user_email_exist($email) {
	$email = sanitize($email);
	return (mysql_result(mysql_query("SELECT COUNT('id') FROM `accounts` WHERE `email`='$email';"), 0) >= 1) ? true : false;
}

// Fetch user account ID from registered email. (this is used by etc lost account)
function user_id_from_email($email) {
	$email = sanitize($email);
	 $account_id = mysql_result(mysql_query("SELECT `id` FROM `accounts` WHERE `email`='$email';"), 0, 'id');
	 return $account_id;
}

// Checks that a password exist in the database.
function user_password_exist($password) {
	$password = sha1($password); // No need to sanitize passwords since we encrypt them.
	return (mysql_result(mysql_query("SELECT COUNT('id') FROM `accounts` WHERE `password`='$password';"), 0) == 1) ? true : false;
}

// Verify that submitted password match stored password in account id
function user_password_match($password, $account_id) {
	$password = sha1($password); // No need to sanitize passwords since we encrypt them.
	$account_id = (int)$account_id;
	return (mysql_result(mysql_query("SELECT COUNT('id') FROM `accounts` WHERE `password`='$password' AND `id`='$account_id';"), 0) == 1) ? true : false;
}

// Get user ID from name
function user_id($username) {
	$username = sanitize($username);
	$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `name`='$username' LIMIT 1;");
	if ($data !== false) return $data['id'];
	else return false;
}

// Get user login ID from username and password
function user_login_id($username, $password) {
	$username = sanitize($username);
	$password = sha1($password);
	$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `name`='$username' AND `password`='$password' LIMIT 1;");
	if ($data !== false) return $data['id'];
	else return false;
}

// TFS 0.3+ compatibility.
function user_login_id_03($username, $password) {
	if (config('salt') === true) {
		if (user_exist($username)) {
			$user_id = user_id($username);
			$username = sanitize($username);
			
			$salt = mysql_result(mysql_query("SELECT `salt` FROM `accounts` WHERE `id`='$user_id';"), 0, 'salt');
			if (!empty($salt)) $password = sha1($salt.$password);
			else $password = sha1($password);
			return mysql_result(mysql_query("SELECT `id` FROM `accounts` WHERE `name`='$username' AND `password`='$password';"), 0, 'id');
		}
	} else return user_login_id($username, $password);
}

// Get character ID from character name
function user_character_id($charname) {
	$charname = sanitize($charname);
	$char = mysql_select_single("SELECT `id` FROM `players` WHERE `name`='$charname';");
	if ($char !== false) return $char['id'];
	else return false;
}

// Hide user character.
function user_character_hide($username) {
	$username = sanitize($username);
	$username = user_character_id($username);
	$char = mysql_select_single("SELECT `hide_char` FROM `znote_players` WHERE `player_id`='$username';");
	if ($char !== false) return $char['hide_char'];
	else return false;
}

// Login with a user. (TFS 0.2)
function user_login($username, $password) {
	$user_id = user_login_id($username, $password);

	$username = sanitize($username);
	$password = sha1($password);
	return (mysql_result(mysql_query("SELECT COUNT('id') FROM accounts WHERE name='$username' AND password='$password';"), 0) == 1) ? $user_id : false;
}

// Login a user with TFS 0.3 compatibility
function user_login_03($username, $password) {
	if (config('salt') === true) {
		$user_id = user_login_id_03($username, $password);

		$username = sanitize($username);
		
		$salt = mysql_result(mysql_query("SELECT `salt` FROM `accounts` WHERE `id`='$user_id';"), 0, 'salt');
		if (!empty($salt)) $password = sha1($salt.$password);
		else $password = sha1($password);
		return (mysql_result(mysql_query("SELECT COUNT('id') FROM accounts WHERE name='$username' AND password='$password';"), 0) == 1) ? $user_id : false;
	} else return user_login($username, $password);
}

// Verify that user is logged in
function user_logged_in() {
	return (isset($_SESSION['user_id'])) ? true : false;
}
?>
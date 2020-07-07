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

	// Insert only if image dosn't already exist there
	$exist = mysql_select_single("SELECT `id` FROM `znote_images` WHERE `image`='$image' LIMIT 1;");
	if ($exist === false) {
		mysql_insert("INSERT INTO `znote_images` (`title`, `desc`, `date`, `status`, `image`, `account_id`) VALUES ('$title', '$desc', '$time', '1', '$image', '$account_id');");
		return true;
	}
	return false;
}

function updateImage($id, $status) {
	$id = (int)$id;
	$status = (int)$status;
	mysql_update("UPDATE `znote_images` SET `status`='$status' WHERE `id`='$id';");
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
	return mysql_select_multi("SELECT `d`.`level`, `p`.`name` AS `victim`, `d`.`time`, `d`.`is_player`, `d`.`killed_by` FROM `player_deaths` AS `d` INNER JOIN `players` AS `p` ON `d`.`player_id` = `p`.`id` ORDER BY `time` DESC LIMIT $from, $to;");
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

// Support list
function support_list() {
    $TFS = Config('ServerEngine');
    if ($TFS == 'TFS_10') $staffs = mysql_select_multi("SELECT `p`.`id`, `a`.`type` as `group_id`, `p`.`name`, `p`.`account_id` FROM `players` AS `p` INNER JOIN `accounts` AS `a` ON `p`.`account_id` = `a`.`id` WHERE `a`.`type` > 1 ORDER BY `p`.`account_id` DESC, `p`.`group_id` ASC, `p`.`level` ASC;");
    else $staffs = mysql_select_multi("SELECT `a`.`type` as `group_id`, `p`.`name`, `p`.`online`, `p`.`account_id` FROM `players` AS `p` INNER JOIN `accounts` AS `a` ON `a`.`id` = `p`.`account_id` WHERE `a`.`type` > 1 ORDER BY `p`.`account_id` DESC, `p`.`group_id` ASC, `p`.`level` ASC;");
	if ($staffs !== false) {
		foreach($staffs as $k => $v)  {
	        foreach($staffs as $key => $value)  {
	            if($k != $key && $v['account_id'] == $value['account_id']) {
	                unset($staffs[$k]);
	            }
	        }
	    }
	    $staffs = array_values($staffs);
	    if ($TFS == 'TFS_10') {
	        for ($i = 0; $i < count($staffs); $i++) {
	            // Fix online status on TFS 1.0
	            $staffs[$i]['online'] = (isset($staffs[$i]['id']) && user_is_online_10($staffs[$i]['id'])) ? 1 : 0;
	            unset($staffs[$i]['id']);
	        }
	    }
	}
    return $staffs;
}

function support_list03() {
	$staffs = mysql_select_multi("SELECT `group_id`, `name`, `online`, `account_id` FROM `players` WHERE `group_id` > 1 ORDER BY `group_id` ASC;");

	if ($staffs !== false) {
		for ($i = 0; $i < count($staffs); $i++) {
			// $staffs[$i]['']
			unset($staffs[$i]['account_id']);
		}
	}
	return $staffs;
}

// NEWS
function fetchAllNews() {
	return mysql_select_multi("SELECT `n`.`id`, `n`.`title`, `n`.`text`, `n`.`date`, `p`.`name` FROM `znote_news` AS `n` INNER JOIN `players` AS `p` ON `n`.`pid` = `p`.`id` ORDER BY `n`.`id` DESC;");
}

// HOUSES
function fetchAllHouses_03() {
	return mysql_select_multi("SELECT * FROM `houses`;");
}

// TFS Storage value functions (Warning, I think these things are saved in cache,
// and thus require server to be offline, or affected players to be offline while using)

// Get player storage list
function getPlayerStorageList($storage, $minValue) {
	$minValue = (int)$minValue;
	$storage = (int)$storage;
	return mysql_select_multi("SELECT `player_id`, `value` FROM `player_storage` WHERE `key`='$storage' AND `value`>='$minValue' ORDER BY `value` DESC;");
}

// Get global storage value
function getGlobalStorage($storage) {
	$storage = (int)$storage;
	return mysql_select_single("SELECT `value` FROM `global_storage` WHERE `key`='$storage';");
}

// Set global storage value
function setGlobalStorage($storage, $value) {
	$storage = (int)$storage;
	$value = (int)$value;

	// If the storage does not exist yet
	if (getGlobalStorage($storage) === false) {
		mysql_insert("INSERT INTO `global_storage` (`key`, `world_id`, `value`) VALUES ('$storage', 0, '$value')");
	} else {// If the storage exist
		mysql_update("UPDATE `global_storage` SET `value`='$value' WHERE `key`='$storage'");
	}
}

// Get player storage value.
function getPlayerStorage($player_id, $storage, $online = false) {
	if ($online) $online = user_is_online($player_id);
	if (!$online) {
		// user is offline (false), we may safely proceed:
		$player_id = (int)$player_id;
		$storage = (int)$storage;
		return mysql_select_single("SELECT `value` FROM `player_storage` WHERE `key`='$storage' AND `player_id`='$player_id';");
	} else return false;
}

// Set player storage value
function setPlayerStorage($player_id, $storage, $value) {
	$storage = (int)$storage;
	$value = (int)$value;
	$player_id = (int)$player_id;

	// If the storage does not exist yet
	if (getPlayerStorage($storage) === false) {
		mysql_insert("INSERT INTO `player_storage` (`player_id`, `key`, `value`) VALUES ('$player_id', '$storage', '$value')");
	} else {// If the storage exist
		mysql_update("UPDATE `player_storage` SET `value`='$value' WHERE `key`='$storage' AND `player_id`='$player_id'");
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
	mysql_delete("DELETE FROM `znote_shop_orders` WHERE `id`='$rowid';");
}

function shop_update_row_count($rowid, $count) {
	$rowid = (int)$rowid;
	$count = (int)$count;
	mysql_update("UPDATE `znote_shop_orders` SET `count`='$count' WHERE `id`='$rowid'");
}

function shop_account_gender_tickets($accid) {
	$accid = (int)$accid;
	return mysql_select_multi("SELECT `id`, `count` FROM `znote_shop_orders` WHERE `account_id`='$accid' AND `type`='3';");
}

// GUILDS
//
function guild_remove_member($cid) {
	$cid = (int)$cid;
	mysql_update("UPDATE `players` SET `rank_id`='0', `guildnick`= NULL WHERE `id`=$cid");
}
function guild_remove_member_10($cid) {
	$cid = (int)$cid;
	mysql_update("DELETE FROM `guild_membership` WHERE `player_id`='$cid' LIMIT 1;");
}

// Change guild rank name.
function guild_change_rank($rid, $name) {
	$rid = (int)$rid;
	$name = sanitize($name);

	mysql_update("UPDATE `guild_ranks` SET `name`='$name' WHERE `id`=$rid");
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
		if (config('ServerEngine') !== 'TFS_10') {
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
	if (config('ServerEngine') !== 'OTHIRE')
		mysql_update("UPDATE `guilds` SET `ownerid`='$new_leader' WHERE `id`=$gid");
	else
		mysql_update("UPDATE `guilds` SET `owner_id`='$new_leader' WHERE `id`=$gid");
}

// Returns $gid of a guild leader($cid).
function guild_leader_gid($leader) {
	$leader = (int)$leader;
	if (config('ServerEngine') !== 'OTHIRE')
		$data = mysql_select_single("SELECT `id` FROM `guilds` WHERE `ownerid`='$leader';");
	else
		$data = mysql_select_single("SELECT `id` FROM `guilds` WHERE `owner_id`='$leader';");
	return ($data === false) ? false : $data['id'];
}

// Returns guild leader(charID) of a guild. (parameter: guild_ID)
function guild_leader($gid) {
	$gid = (int)$gid;
	if (config('ServerEngine') !== 'OTHIRE')
		$data = mysql_select_single("SELECT `ownerid` FROM `guilds` WHERE `id`='$gid';");
	else
		$data = mysql_select_single("SELECT `owner_id` FROM `guilds` WHERE `id`='$gid';");
	return ($data !== false) ? $data['ownerid'] : false;
}

// Disband guild
function guild_remove_invites($gid) {
	$gid = (int)$gid;
	mysql_delete("DELETE FROM `guild_invites` WHERE `guild_id`='$gid';");
}

// Remove guild invites
function guild_delete($gid) {
	$gid = (int)$gid;
	mysql_delete("DELETE FROM `guilds` WHERE `id`='$gid';");
}

// Player leave guild
function guild_player_leave($cid) {
	$cid = (int)$cid;
	mysql_update("UPDATE `players` SET `rank_id`='0', `guildnick`= NULL WHERE `id`=$cid LIMIT 1;");
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

	if (config('ServerEngine') !== 'TFS_10') {
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
			//guild_remove_invitation($cid, $gid);
			guild_remove_all_invitations($cid);
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
			//guild_remove_invitation($cid, $gid);
			guild_remove_all_invitations($cid);
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

// Remove ALL invitations
function guild_remove_all_invitations($cid) {
	$cid = (int)$cid;
	mysql_delete("DELETE FROM `guild_invites` WHERE `player_id`='$cid';");
}

// Invite character to guild
function guild_invite_player($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	mysql_insert("INSERT INTO `guild_invites` (`player_id`, `guild_id`) VALUES ('$cid', '$gid')");
}

// Gets a list of invited players to a particular guild.
function guild_invite_list($gid) {
	$gid = (int)$gid;
	return mysql_select_multi("SELECT `gi`.`player_id`, `gi`.`guild_id`, `p`.`name` FROM `guild_invites` AS `gi` INNER JOIN `players` AS `p` ON `gi`.`player_id`=`p`.`id` WHERE `gi`.`guild_id`='$gid';");
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

// Update player's guild nick
function update_player_guildnick($cid, $nick) {
	$cid = (int)$cid;
	$nick = sanitize($nick);
	if (!empty($nick)) {
		mysql_update("UPDATE `players` SET `guildnick`='$nick' WHERE `id`=$cid");
	} else {
		mysql_update("UPDATE `players` SET `guildnick`='' WHERE `id`=$cid");
	}
}
function update_player_guildnick_10($cid, $nick) {
	$cid = (int)$cid;
	$nick = sanitize($nick);
	if (!empty($nick)) {
		mysql_update("UPDATE `guild_membership` SET `nick`='$nick' WHERE `player_id`=$cid");
	} else {
		mysql_update("UPDATE `guild_membership` SET `nick`='' WHERE `player_id`=$cid");
	}
}

// Get guild data, using guild id.
function get_guild_rank_data($gid) {
	$gid = (int)$gid;
	return mysql_select_multi("SELECT `id`, `guild_id`, `name`, `level` FROM `guild_ranks` WHERE `guild_id`='$gid' ORDER BY `id` DESC LIMIT 0, 30");
}

// Creates a guild, where cid is the owner of the guild, and name is the name of guild.
function create_guild($cid, $name) {
	$cid = (int)$cid;
	$name = sanitize($name);
	$time = time();

	// Create the guild
	if (config('ServerEngine') !== 'OTHIRE')
		mysql_insert("INSERT INTO `guilds` (`name`, `ownerid`, `creationdata`, `motd`) VALUES ('$name', '$cid', '$time', '');");
	else
		mysql_insert("INSERT INTO `guilds` (`name`, `owner_id`, `creationdate`) VALUES ('$name', '$cid', '$time');");

	// Get guild id
	$gid = get_guild_id($name);

	// Get rank id for guild leader
	$data = mysql_select_single("SELECT `id` FROM `guild_ranks` WHERE `guild_id`='$gid' AND `level`='3' LIMIT 1;");
	$rid = ($data !== false) ? $data['id'] : false;

	// Give player rank id for leader of his guild
	if (config('ServerEngine') !== 'TFS_10') mysql_update("UPDATE `players` SET `rank_id`='$rid' WHERE `id`='$cid' LIMIT 1;");
	else mysql_insert("INSERT INTO `guild_membership` (`player_id`, `guild_id`, `rank_id`, `nick`) VALUES ('$cid', '$gid', '$rid', '');");
}

// Search player table on cid for his rank_id, returns rank_id
function get_character_guild_rank($cid) {
	$cid = (int)$cid;
	if (config('ServerEngine') !== 'TFS_10') {
		$data = mysql_select_single("SELECT `rank_id` FROM `players` WHERE `id`='$cid';");
		return ($data !== false && $data['rank_id'] > 0) ? $data['rank_id'] : false;
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
	$data = mysql_select_single("SELECT `level` FROM `guild_ranks` WHERE `id`=$rid;");
	return ($data !== false) ? $data['level'] : false;
}

// Get a players rank_id, guild_id, rank_level(ID), rank_name(string), using cid(player id)
function get_player_guild_data($cid) {
	$cid = (int)$cid;
	if (config('ServerEngine') !== 'TFS_10') $playerdata = mysql_select_single("SELECT `rank_id` FROM `players` WHERE `id`='$cid' LIMIT 1;");
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
	$data = mysql_select_single("SELECT `id` FROM `guilds` WHERE `name`='$name';");
	return ($data !== false) ? $data['id'] : false;
}

// Returns guild data from name
function get_guild_data($name) {
	$name = sanitize($name);
	if (config('ServerEngine') !== 'OTHIRE')
		return mysql_select_single("SELECT `id`, `name`, `ownerid`, `creationdata`, `motd` FROM `guilds` WHERE `name`='$name' LIMIT 1;");
	else
		return mysql_select_single("SELECT `id`, `name`, `owner_id`, `creationdate` FROM `guilds` WHERE `name`='$name' LIMIT 1;");
}

// Get complete list of guilds
function get_guilds_list() {
	if (config('ServerEngine') !== 'OTHIRE')
		return mysql_select_multi("SELECT `id`, `name`, `creationdata` FROM `guilds` ORDER BY `name`;");
	else
		return mysql_select_multi("SELECT `id`, `name`, `creationdate` FROM `guilds` ORDER BY `name`;");
}

// Get array of player data related to a guild.
function get_guild_players($gid) {
	$gid = (int)$gid; // Sanitizing the parameter id
	if (config('ServerEngine') !== 'TFS_10') return mysql_select_multi("SELECT `p`.`id`, `p`.`rank_id`, `p`.`name`, `p`.`level`, `p`.`guildnick`, `p`.`vocation`, `p`.`online`, `gr`.`name` AS `rank_name`, `gr`.`level` AS `rank_level` FROM `players` AS `p` LEFT JOIN `guild_ranks` AS `gr` ON `gr`.`id` = `p`.`rank_id` WHERE `gr`.`guild_id` ='$gid' ORDER BY `gr`.`id`, `p`.`name`;");
	else return mysql_select_multi("SELECT `p`.`id`, `p`.`name`, `p`.`level`, `p`.`vocation`, `gm`.`rank_id`, `gm`.`nick` AS `guildnick`, `gr`.`name` AS `rank_name`, `gr`.`level` AS `rank_level` FROM `players` AS `p` LEFT JOIN `guild_membership` AS `gm` ON `gm`.`player_id` = `p`.`id` LEFT JOIN `guild_ranks` AS `gr` ON `gr`.`id` = `gm`.`rank_id` WHERE `gm`.`guild_id` = '$gid' ORDER BY `gm`.`rank_id`, `p`.`name`");
}

// Get guild level data (avg level, total level, count of players)
function get_guild_level_data($gid) {
	$gid = (int)$gid;
	$data = (config('ServerEngine') !== 'TFS_10') ? mysql_select_multi("SELECT p.level FROM players AS p LEFT JOIN guild_ranks AS gr ON gr.id = p.rank_id WHERE gr.guild_id ='$gid';") : mysql_select_multi("SELECT p.level FROM players AS p LEFT JOIN guild_membership AS gm ON gm.player_id = p.id WHERE gm.guild_id = '$gid' ORDER BY gm.rank_id, p.name;");
	$members = 0;
	$totallevels = 0;
	if ($data !== false) {
		foreach ($data as $player) {
			$members++;
			$totallevels += $player['level'];
		}
		return array('avg' => (int)($totallevels / $members), 'total' => $totallevels, 'players' => $members);
	} else return false;
}

// Returns total members in a guild (integer)
function count_guild_members($gid) {
	$gid = (int)$gid;
	if (config('ServerEngine') !== 'TFS_10') {
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
	return mysql_select_single("SELECT `id`, `guild1`, `guild2`, `name1`, `name2`, `status`, `started`, `ended` FROM `guild_wars` WHERE `id`=$warid ORDER BY `started`;");
}

// TFS 0.3 compatibility
function get_guild_war03($warid) {
	$warid = (int)$warid; // Sanitizing the parameter id

	$war = mysql_select_single("SELECT `id`, `guild_id`, `enemy_id`, `status`, `begin`, `end`
		FROM `guild_wars` WHERE `id`=$warid ORDER BY `begin` DESC LIMIT 0, 30");
	if ($war !== false) {
		$war['guild1'] = $war['guild_id'];
		$war['guild2'] = $war['enemy_id'];
		$war['name1'] = get_guild_name($war['guild_id']);
		$war['name2'] = get_guild_name($war['enemy_id']);
		$war['started'] = $war['begin'];
		$war['ended'] = $war['end'];
	}
	return $war;
}

// List all war entries
function get_guild_wars() {
	return mysql_select_multi("SELECT `id`, `guild1`, `guild2`, `name1`, `name2`, `status`, `started`, `ended` FROM `guild_wars` ORDER BY `started` DESC LIMIT 0, 30");
}

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
	return mysql_select_multi("SELECT `id`, `killer`, `target`, `killerguild`, `targetguild`, `warid`, `time` FROM `guildwar_kills` WHERE `warid`=$war_id ORDER BY `time` DESC");
}

// TFS 0.3 compatibility
function get_war_kills03($war_id) {
	$war_id = (int)$war_id;// Sanitize - verify its an integer.
	return mysql_select_multi("SELECT `id`, `guild_id`, `war_id`, `death_id` FROM `guild_kills` WHERE `war_id`=$war_id ORDER BY `id` DESC LIMIT 0, 30");
}

// Gesior compatibility port TFS .3
function gesior_sql_death($warid) {
	$warid = (int)$warid; // Sanitizing the parameter id
	return mysql_select_multi('SELECT `pd`.`id`, `pd`.`date`, `gk`.`guild_id` AS `enemy`, `p`.`name`, `pd`.`level` FROM `guild_kills` gk LEFT JOIN `player_deaths` pd ON `gk`.`death_id` = `pd`.`id` LEFT JOIN `players` p ON `pd`.`player_id` = `p`.`id` WHERE `gk`.`war_id` = ' . $warid . ' AND `p`.`deleted` = 0 ORDER BY `pd`.`date` DESC');
}
function gesior_sql_killer($did) {
	$did = (int)$did; // Sanitizing the parameter id
	return mysql_select_multi('SELECT `p`.`name` AS `player_name`, `p`.`deleted` AS `player_exists`, `k`.`war` AS `is_war` FROM `killers` k LEFT JOIN `player_killers` pk ON `k`.`id` = `pk`.`kill_id` LEFT JOIN `players` p ON `p`.`id` = `pk`.`player_id` WHERE `k`.`death_id` = ' . $did . ' ORDER BY `k`.`final_hit` DESC, `k`.`id` ASC');
}
// end gesior
// END GUILD WAR

// ADMIN FUNCTIONS
function set_ingame_position($name, $acctype) {
	$acctype = (int)$acctype;
	$name = sanitize($name);

	$acc_id = user_character_account_id($name);
	$char_id = user_character_id($name);

	$group_id = 1;
	if ($acctype == 4) {
		$group_id = 2;
	} elseif ($acctype >= 5) {
		$group_id = 3;
	}
	mysql_update("UPDATE `accounts` SET `type` = '$acctype' WHERE `id` =$acc_id;");
	mysql_update("UPDATE `players` SET `group_id` = '$group_id' WHERE `id` =$char_id;");
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
	mysql_update("UPDATE `players` SET `group_id` = '$acctype' WHERE `id` =$char_id;");
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

		if (Config('ServerEngine') === 'TFS_02')
		mysql_insert("INSERT INTO `bans` (`type` ,`ip` ,`mask` ,`player` ,`account` ,`time` ,`reason_id` ,`action_id` ,`comment` ,`banned_by`) VALUES ('$typeid', '$charip', '4294967295', '$charid', '$accountid', '$time', '$reasonid', '$actionid', '$comment', '$bannedby');");
		elseif (Config('ServerEngine') === 'TFS_03') {
			$now = time();
			switch ($typeid) {
				case 1: // IP ban
					mysql_insert("INSERT INTO `bans` (`type`, `value`, `param`, `active`, `expires`, `added`, `admin_id`, `comment`) VALUES ('$typeid', '$charip', '4294967295', '1', '$time', '$now', '$bannedby', '$comment');");
				break;

				case 2: // namelock
					mysql_insert("INSERT INTO `bans` (`type`, `value`, `param`, `active`, `expires`, `added`, `admin_id`, `comment`) VALUES ('$typeid', '$charid', '4294967295', '1', '$time', '$now', '$bannedby', '$comment');");
				break;

				case 3: // acc ban
					mysql_insert("INSERT INTO `bans` (`type`, `value`, `param`, `active`, `expires`, `added`, `admin_id`, `comment`) VALUES ('$typeid', '$accountid', '4294967295', '1', '$time', '$now', '$bannedby', '$comment');");
				break;

				case 4: // notation
					mysql_insert("INSERT INTO `bans` (`type`, `value`, `param`, `active`, `expires`, `added`, `admin_id`, `comment`) VALUES ('$typeid', '$charid', '4294967295', '1', '$time', '$now', '$bannedby', '$comment');");
				break;

				case 5: // deletion
					mysql_insert("INSERT INTO `bans` (`type`, `value`, `param`, `active`, `expires`, `added`, `admin_id`, `comment`) VALUES ('$typeid', '$charid', '4294967295', '1', '$time', '$now', '$bannedby', '$comment');");
				break;
			}
		}
		elseif (Config('ServerEngine') === 'TFS_10') {
			$now = time();

			switch ($typeid) {
				case 1: // IP ban
					mysql_insert("INSERT INTO `ip_bans` (`ip`, `reason`, `banned_at`, `expires_at`, `banned_by`) VALUES ('$charip', '$comment', '$now', '$time', '$bannedby');");
				break;

				case 2: // namelock
					mysql_insert("INSERT INTO `player_namelocks` (`player_id`, `reason`, `namelocked_at`, `namelocked_by`) VALUES ('$charid', 'comment', '$now', '$bannedby');");
				break;

				case 3: // acc ban
					mysql_insert("INSERT INTO `account_bans` (`account_id`, `reason`, `banned_at`, `expires_at`, `banned_by`) VALUES ('$accountid', '$comment', '$now', '$time', '$bannedby');");
				break;

				case 4: // notation
					data_dump(false, array('status' => false), "Function deprecated. Ban option does not exist in TFS 1.0.");
					die();
				break;

				case 5: // deletion
					data_dump(false, array('status' => false), "Function deprecated. Ban option does not exist in TFS 1.0.");
					die();
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
	$data = mysql_select_multi("SELECT * FROM `player_deaths` WHERE `player_id`='$char_id' order by `date` DESC LIMIT 0, 10");
	if ($data !== false) {
		for ($i = 0; $i < count($data); $i++) {
			$data[$i]['time'] = $data[$i]['date'];
		}
	}
	return $data;
}

// same (death id ---> killer id)
function user_get_kid($did) {
	$did = (int)$did;
	$data = mysql_select_single("SELECT `id` FROM `killers` WHERE `death_id`='$did';");
	return ($data !== false) ? $data['id'] : false;
}
// same (killer id ---> player id)
function user_get_killer_id($kn) {
	$kn = (int)$kn;
	$data = mysql_select_single("SELECT `player_id` FROM `player_killers` WHERE `kill_id`='$kn';");
	return ($data !== false) ? $data['player_id'] : false;
}
// same (killer id ---> monster name)
function user_get_killer_m_name($mn) {
	$mn = (int)$mn;
	$data = mysql_select_single("SELECT `name` FROM `environment_killers` WHERE `kill_id`='$mn';");
	return ($data !== false) ? $data['name'] : false;
}

// Count character deaths. Counts up 10.
function user_count_deathlist($char_id) {
	$char_id = (int)$char_id;
	$data = mysql_select_single("SELECT COUNT('id') AS `id` FROM `player_deaths` WHERE `player_id`='$char_id' order by `time` DESC LIMIT 0, 10");
	return ($data !== false) ? $data['id'] : false;
}

// MY ACCOUNT RELATED \\
function user_update_comment($char_id, $comment) {
	$char_id = sanitize($char_id);
	$comment = sanitize($comment);
	mysql_update("UPDATE `znote_players` SET `comment`='$comment' WHERE `player_id`='$char_id'");
}

// Permamently delete character id. (parameter: character id)
function user_delete_character($char_id) {
	$char_id = (int)$char_id;
	mysql_delete("DELETE FROM `players` WHERE `id`='$char_id';");
	mysql_delete("DELETE FROM `znote_players` WHERE `player_id`='$char_id';");
}

// Delete character with supplied id with a delay.
function user_delete_character_soft($char_id) {
	$char_id = (int)$char_id;

	$char_name = user_character_name($char_id);
	$original_acc_id = user_character_account_id($char_name);
	if(!user_character_pending_delete($char_name))
		mysql_insert('INSERT INTO `znote_deleted_characters`(`original_account_id`, `character_name`, `time`, `done`) VALUES(' . $original_acc_id . ', "' . $char_name . '", (NOW() + INTERVAL ' . config('delete_character_interval') . '), 0)');
	else
		return false;
}

// Check if character will be deleted soon.
function user_character_pending_delete($char_name) {
	$char_name = sanitize($char_name);
	$result = mysql_select_single('SELECT `done` FROM `znote_deleted_characters` WHERE `character_name` = "' . $char_name . '"');
	return ($result === false) ? false : !$result['done'];
}

// Get pending character deletes for supplied account id.
function user_pending_deletes($acc_id) {
	$acc_id = (int)$acc_id;
	return mysql_select_multi('SELECT `id`, `character_name`, `time` FROM `znote_deleted_characters` WHERE `original_account_id` = ' . $acc_id . ' AND `done` = 0');
}

// Parameter: accounts.id returns: An array containing detailed information of every character on the account.
function user_character_list($account_id) {
	//$count = user_character_list_count($account_id);
	$account_id = (int)$account_id;

	if (config('ServerEngine') == 'TFS_10') {
		$characters = mysql_select_multi("SELECT `p`.`id`, `p`.`name`, `p`.`level`, `p`.`vocation`, `p`.`town_id`, `p`.`lastlogin`, `gm`.`rank_id`, `po`.`player_id` AS `online` FROM `players` AS `p` LEFT JOIN `guild_membership` AS `gm` ON `p`.`id`=`gm`.`player_id` LEFT JOIN `players_online` AS `po` ON `p`.`id`=`po`.`player_id` WHERE `p`.`account_id`='$account_id' ORDER BY `p`.`level` DESC");
		if ($characters !== false) {
			for ($i = 0; $i < count($characters); $i++) {
				$characters[$i]['online'] = ($characters[$i]['online'] > 0) ? 1 : 0;
				//unset($characters[$i]['id']);
			}
		}

	} else $characters = mysql_select_multi("SELECT `id`, `name`, `level`, `vocation`, `town_id`, `lastlogin`, `online`, `rank_id` FROM `players` WHERE `account_id`='$account_id' ORDER BY `level` DESC");

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
	return mysql_select_multi("SELECT `id` FROM `players` WHERE `account_id`='$account_id' ORDER BY `level` DESC LIMIT 0, 30");
}

// Parameter: accounts.id returns: number of characters on the account.
function user_character_list_count($account_id) {
	$account_id = sanitize($account_id);
	$data = mysql_select_single("SELECT COUNT('id') AS `id` FROM `players` WHERE `account_id`='$account_id'");
	return ($data !== false) ? $data['id'] : 0;
}

// END MY ACCOUNT RELATED

// HIGHSCORE FUNCTIONS \\
function fetchAllScores($rows, $tfs, $g, $vlist, $v = -1, $flags = false, $outfits = false) {
	if (config('ServerEngine') !== 'OTHIRE') {
		if (config('client') < 780) {
			$outfits = ($outfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`" : "";
		} else {
			$outfits = ($outfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`, `p`.`lookaddons` AS `addons`" : "";
		}
	} else {
		$outfits = ($outfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`" : "";
	}
	// Return scores ordered by type and vocation (if set)
	$data = array();

	// Add "all" as a simulated vocation in vocation_list to represent all vocations and loop through them.
	$vocGroups = array(
		'all' => array()
	);
	foreach ($vlist AS $vid => $vdata) {
		// If this vocation does not have a fromVoc attribute
		if ($vdata['fromVoc'] === false) {
			// Add it as a group
			$vocGroups[(string)$vid] = array();
		} else {
			// Add an extended group for both vocations
			$sharedGroup = (string)$vdata['fromVoc'] . ", " . (string)$vid;
			$vocGroups[$sharedGroup] = array();

			// Make the fromVoc group a reference to the extended group for both vocations
			$vocGroups[(string)$vdata['fromVoc']] = $sharedGroup;
			$vocGroups[(string)$vid] = $sharedGroup;
		}
	}

	foreach ($vocGroups as $voc_id => $key_or_arr) {

		$vGrp = $voc_id;
		// Change to correct vocation group if this vocation id reference a shared vocation group
		if (!is_array($key_or_arr)) $vGrp = $key_or_arr;

		// If this vocation group is empty (then we need to fill it with highscore SQL Data)
		if (empty($vocGroups[$vGrp])) {

			// Generate SQL WHERE-clause for vocation if $v is set
			$v = '';
			if ($vGrp !== 'all')
				$v = (strpos($vGrp, ',') !== false) ? 'AND `p`.`vocation` IN ('. $vGrp . ')' : 'AND `p`.`vocation` = \''.intval($vGrp).'\'';

			if ($tfs == 'TFS_10') {

				if ($flags === false) { // In this case we only need to query players table
					$v = str_replace('`p`.', '', $v);
					$outfits = str_replace('`p`.', '', $outfits);

					$vocGroups[$vGrp][1] = mysql_select_multi("SELECT `name`, `vocation`, `skill_club` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_club` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][2] = mysql_select_multi("SELECT `name`, `vocation`, `skill_sword` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_sword` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][3] = mysql_select_multi("SELECT `name`, `vocation`, `skill_axe` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_axe` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][4] = mysql_select_multi("SELECT `name`, `vocation`, `skill_dist` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_dist` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][5] = mysql_select_multi("SELECT `name`, `vocation`, `skill_shielding` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_shielding` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][6] = mysql_select_multi("SELECT `name`, `vocation`, `skill_fishing` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_fishing` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][7] = mysql_select_multi("SELECT `name`, `vocation`, `experience`, `level` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `experience` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][8] = mysql_select_multi("SELECT `name`, `vocation`, `maglevel` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `maglevel` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][9] = mysql_select_multi("SELECT `name`, `vocation`, `skill_fist` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_fist` DESC LIMIT 0, $rows;");

				} else { // Inner join znote_accounts table to retrieve the flag
					$vocGroups[$vGrp][1] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_club` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_club` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][2] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_sword` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_sword` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][3] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_axe` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_axe` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][4] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_dist` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_dist` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][5] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_shielding` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_shielding` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][6] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_fishing` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_fishing` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][7] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`experience`, `level` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`experience` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][8] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`maglevel` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`maglevel` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][9] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_fist` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_fist` DESC LIMIT 0, $rows;");
				}
			} else { // TFS 0.2, 0.3, 0.4

				if ($flags === false) {
					$vocGroups[$vGrp][9] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 0 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][1] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 1 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][2] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 2 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][3] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 3 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][4] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 4 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][5] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 5 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][6] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 6 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$v = str_replace('`p`.', '', $v);
					$outfits = str_replace('`p`.', '', $outfits);
					$vocGroups[$vGrp][7] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `experience`, `level` AS `value` $outfits $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `experience` DESC limit 0, $rows;");
					$vocGroups[$vGrp][8] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `maglevel` AS `value` $outfits $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `maglevel` DESC limit 0, $rows;");

				} else { // Inner join znote_accounts table to retrieve the flag
					$vocGroups[$vGrp][9] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 0 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][1] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 1 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][2] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 2 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][3] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 3 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][4] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 4 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][5] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 5 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][6] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 6 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][7] = mysql_select_multi("SELECT `p`.`id`, `p`.`name`, `p`.`vocation`, `p`.`experience`, `p`.`level` AS `value`, `za`.`flag` AS `flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`experience` DESC limit 0, $rows;");
					$vocGroups[$vGrp][8] = mysql_select_multi("SELECT `p`.`id`, `p`.`name`, `p`.`vocation`, `p`.`maglevel` AS `value`, `za`.`flag` AS `flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`maglevel` DESC limit 0, $rows;");
				}
			}
		}
	}
	return $vocGroups;
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
	if (config('ServerEngine') !== 'OTHIRE') {
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
	} else {
		if ($mode === 'username') {
			$verify_data = array(
				'password' => sha1($edom),
				'email' => $email
			);
		} else {
			$verify_data = array(
				'id' => $edom,
				'email' => $email
			);
		}
	}
	// Determine if the submitted information is correct and herit from same account
	if (user_account_fields_verify_value($verify_data)) {

		// Structure account id fetch method correctly
		if ($mode == 'username') {
			$account_id = user_account_id_from_password($verify_data['password']);
		} else {
			if (config('ServerEngine') !== 'OTHIRE')
				$account_id = user_id($verify_data['name']);
			else
				$account_id = user_id($verify_data['id']);
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
					if (config('ServerEngine') !== 'OTHIRE') {
						$name_data = user_data($account_id, 'name');
						echo '<br><p>Your username is:</p> <h3>'. $name_data['name'] .'</h3>';
					} else {
						$name_data = user_data($account_id, 'id');
						echo '<br><p>Your account number is:</p> <h3>'. $name_data['id'] .'</h3>';
					}
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

// Get account name from id.
function user_account_id_from_name($id) {
	$id = (int)$id;;
	if (config('ServerEngine') !== 'OTHIRE') {
		$result = mysql_select_single("SELECT `name` FROM `accounts` WHERE `id` = '" . $id . "' LIMIT 1;");
		return $result['name'];
	} else {
		$result = mysql_select_single("SELECT `id` FROM `accounts` WHERE `id` = '" . $id . "' LIMIT 1;");
		return $result['id'];
	}
}

// Add additional premium days to account id
function user_account_add_premdays($accid, $days) {
	$accid = (int)$accid;
	$days = (int)$days;

	if (config('ServerEngine') !== 'OTHIRE') {
		$data = mysql_select_single("SELECT `premdays` FROM `accounts` WHERE `id`='$accid';");
		$tmp = $data['premdays'];
		$tmp += $days;
		mysql_update("UPDATE `accounts` SET `premdays`='$tmp' WHERE `id`='$accid'");
	} else {
		$data = mysql_select_single("SELECT `premend` FROM `accounts` WHERE `id`='$accid';");
		$tmp = $data['premend'];
		if($tmp == 0)
			$tmp = time() + ($days * 24 * 60 * 60);
		else
			$tmp = $tmp + ($days * 24 * 60 * 60);
		mysql_update("UPDATE `accounts` SET `premend`='$tmp' WHERE `id`='$accid'");
	}
}

// Name = char name. Changes from male to female & vice versa.
function user_character_change_gender($name) {
	$user_id = user_character_id($name);
	$data = mysql_select_single("SELECT `sex` FROM `players` WHERE `id`='$user_id';");
	$gender = $data['sex'];
	if ($gender == 1) mysql_update("UPDATE `players` SET `sex`='0' WHERE `id`='$user_id'");
	else mysql_update("UPDATE `players` SET `sex`='1' WHERE `id`='$user_id'");
}

// Fetch account ID from player NAME
function user_character_account_id($character) {
	$character = sanitize($character);
	$data = mysql_select_single("SELECT `account_id` FROM `players` WHERE `name`='$character';");
	return ($data !== false) ? $data['account_id'] : false;
}

// Verify data from accounts table. Parameter is an array of <columnName> - <data to verify>
// etc array('id' = 4, 'password' = 'test') will verify that logged in user have id 4 and password test.
function user_account_fields_verify_value($verify_data) {
	$verify = array();
	array_walk($verify_data, 'array_sanitize');

	foreach ($verify_data as $field=>$data) {
		$verify[] = '`'. $field .'` = \''. $data .'\'';
	}
	$data = mysql_select_single("SELECT COUNT('id') AS `count` FROM `accounts` WHERE ". implode(' AND ', $verify) .";");
	return ($data !== false && $data['count'] == 1) ? true : false;
}

// Update accounts, make sure user is logged in first.
function user_update_account($update_data) {
	$update = array();
	array_walk($update_data, 'array_sanitize');

	foreach ($update_data as $field=>$data) {
		$update[] = '`'. $field .'` = \''. $data .'\'';
	}

	$user_id = (int)getSession('user_id');

	mysql_update("UPDATE `accounts` SET ". implode(', ', $update) ." WHERE `id`=". $user_id .";");
}

// Update znote_accounts table, make sure user is logged in for this. This is used to etc update lastIP
function user_update_znote_account($update_data) {
	$update = array();
	array_walk($update_data, 'array_sanitize');

	foreach ($update_data as $field=>$data) {
		$update[] = '`'. $field .'` = \''. $data .'\'';
	}

	$user_id = (int)getSession('user_id');

	mysql_update("UPDATE `znote_accounts` SET ". implode(', ', $update) ." WHERE `account_id`=". $user_id .";");
}

// Change password on account_id (Note: You should verify that he knows the old password before doing this)
function user_change_password($user_id, $password) {
	$user_id = sanitize($user_id);
	$password = sha1($password);

	mysql_update("UPDATE `accounts` SET `password`='$password' WHERE `id`=$user_id");
}
// .3 compatibility
function user_change_password03($user_id, $password) {
	if (config('salt') === true) {
		$user_id = sanitize($user_id);
		$salt = user_data($user_id, 'salt');
		$password = sha1($salt['salt'].$password);

		mysql_update("UPDATE `accounts` SET `password`='$password' WHERE `id`=$user_id");
	} else {
		user_change_password($user_id, $password);
	}
}

// Parameter: players.id, value[0 or 1]. Togge hide.
function user_character_set_hide($char_id, $value) {
	$char_id = sanitize($char_id);
	$value = sanitize($value);

	mysql_update("UPDATE `znote_players` SET `hide_char`='$value' WHERE `player_id`=$char_id");
}

// CREATE ACCOUNT
function user_create_account($register_data, $maildata) {
	array_walk($register_data, 'array_sanitize');

	if (config('ServerEngine') == 'TFS_03' && config('salt') === true) {
		$register_data['salt'] = generate_recovery_key(18);
		$register_data['password'] = sha1($register_data['salt'].$register_data['password']);
	} else $register_data['password'] = sha1($register_data['password']);

	$ip = $register_data['ip'];
	$created = $register_data['created'];
	$flag = $register_data['flag'];

	unset($register_data['ip']);
	unset($register_data['created']);
	unset($register_data['flag']);

	if (config('ServerEngine') == 'TFS_10') $register_data['creation'] = $created;

	$fields = '`'. implode('`, `', array_keys($register_data)) .'`';
	$data = '\''. implode('\', \'', $register_data) .'\'';

	mysql_insert("INSERT INTO `accounts` ($fields) VALUES ($data)");

	$account_id = (isset($register_data['name'])) ? user_id($register_data['name']) : user_id($register_data['id']);
	$activeKey = rand(100000000,999999999);
	$active = ($maildata['register']) ? 0 : 1;
	mysql_insert("INSERT INTO `znote_accounts` (`account_id`, `ip`, `created`, `active`, `active_email`, `activekey`, `flag`) VALUES ('$account_id', '$ip', '$created', '$active', '0', '$activeKey', '$flag')");

	if ($maildata['register']) {

		$thisurl = config('site_url') . "$_SERVER[REQUEST_URI]";
		$thisurl .= "?authenticate&u=".$account_id."&k=".$activeKey;

		$mailer = new Mail($maildata);

		$title = "Please authenticate your account at $_SERVER[HTTP_HOST].";

		$body = "<h1>Please click on the following link to authenticate your account:</h1>";
		$body .= "<p><a href='$thisurl'>$thisurl</a></p>";
		$body .= "<p>Thank you for registering and enjoy your stay at $maildata[fromName].</p>";
		$body .= "<hr><p>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p>";

		$mailer->sendMail($register_data['email'], $title, $body, $register_data['name']);
	}
}

// CREATE CHARACTER
function user_create_character($character_data) {
	array_walk($character_data, 'array_sanitize');
	$cnf = fullConfig();

	$vocation = (int)$character_data['vocation'];
	$playercnf = $cnf['player'];
	$base = $playercnf['base'];
	$create = $playercnf['create'];
	$skills = $create['skills'][$vocation];

	$outfit = ($character_data['sex'] == 1) ? $create['male_outfit'] : $create['female_outfit'];

	$leveldiff = $create['level'] - $base['level'];

	$gains = $cnf['vocations_gain'][$vocation];

	$health	= $base['health'] + ( $gains['hp']  * $leveldiff );
	$mana	= $base['mana']   + ( $gains['mp']  * $leveldiff );
	$cap	= $base['cap']    + ( $gains['cap'] * $leveldiff );

	// This is TFS 0.2 compatible import data with Znote AAC mysql schema
	if (config('ServerEngine') !== 'OTHIRE') {
		$import_data = array(
			'name' => $character_data['name'],
			'group_id' => 1,
			'account_id' => $character_data['account_id'],
			'level' => $create['level'],
			'vocation' => $vocation,
			'health' => $health,
			'healthmax' => $health,
			'experience' => level_to_experience($create['level']),
			'lookbody' => $outfit['body'], /* STARTER OUTFITS */
			'lookfeet' => $outfit['feet'],
			'lookhead' => $outfit['head'],
			'looklegs' => $outfit['legs'],
			'looktype' => $outfit['id'],
			'lookaddons' => 0,
			'maglevel' => $skills['magic'],
			'mana' => $mana,
			'manamax' => $mana,
			'manaspent' => 0,
			'soul' => $base['soul'],
			'town_id' => $character_data['town_id'],
			'posx' => $cnf['default_pos']['x'],
			'posy' => $cnf['default_pos']['y'],
			'posz' => $cnf['default_pos']['z'],
			'conditions' => '',
			'cap' => $cap,
			'sex' => $character_data['sex'],
			'lastlogin' => 0,
			'lastip' => $character_data['lastip'],
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
	} else {
		$import_data = array(
			'name' => $character_data['name'],
			'group_id' => 1,
			'account_id' => $character_data['account_id'],
			'level' => $create['level'],
			'vocation' => $vocation,
			'health' => $health,
			'healthmax' => $health,
			'experience' => level_to_experience($create['level']),
			'lookbody' => $outfit['body'], /* STARTER OUTFITS */
			'lookfeet' => $outfit['feet'],
			'lookhead' => $outfit['head'],
			'looklegs' => $outfit['legs'],
			'looktype' => $outfit['id'],
			'maglevel' => $skills['magic'],
			'mana' => $mana,
			'manamax' => $mana,
			'manaspent' => 0,
			'soul' => $base['soul'],
			'town_id' => $character_data['town_id'],
			'posx' => $cnf['default_pos']['x'],
			'posy' => $cnf['default_pos']['y'],
			'posz' => $cnf['default_pos']['z'],
			'conditions' => '',
			'cap' => $cap,
			'sex' => $character_data['sex'],
			'lastlogin' => 0,
			'lastip' => $character_data['lastip'],
			'save' => 1,
			'skull_type' => 0,
			'skull_time' => 0,
			'rank_id' => 0,
			'guildnick' => '',
			'lastlogout' => 0,
			'direction' => 0,
			'loss_experience' => 100,
			'loss_mana' => 100,
			'loss_skills' => 100,
			'loss_items' => 10,
			'online' => 0,
			'balance' => 0
		);
	}

	// Clients below 7.8 don't have outfit addons
	if (isset($import_data['lookaddons']) && config('client') < 780) {
		unset($import_data['lookaddons']);
	}

	// TFS 1.0 variations
	if ($cnf['ServerEngine'] === 'TFS_10') {
		unset($import_data['rank_id']);
		unset($import_data['guildnick']);
		unset($import_data['direction']);
		unset($import_data['loss_experience']);
		unset($import_data['loss_mana']);
		unset($import_data['loss_skills']);
		unset($import_data['premend']);
		unset($import_data['online']);

		// Skills can be added into the same query on TFS 1.0+
		$import_data['skill_fist'] = $skills['fist'];
		$import_data['skill_club'] = $skills['club'];
		$import_data['skill_sword'] = $skills['sword'];
		$import_data['skill_axe'] = $skills['axe'];
		$import_data['skill_dist'] = $skills['dist'];
		$import_data['skill_shielding'] = $skills['shield'];
		$import_data['skill_fishing'] = $skills['fishing'];
	}

	// If you are no vocation (id 0), use these details instead:
	if ($vocation === 0) {
		$import_data['level'] = $create['novocation']['level'];
		$import_data['experience'] = level_to_experience($create['novocation']['level']);

		if ($create['novocation']['forceTown'] === true) {
			$import_data['town_id'] = $create['novocation']['townId'];
		}
	}

	$fields = array_keys($import_data); // Fetch select fields
	$data = array_values($import_data); // Fetch insert data

	$fields_sql = implode("`, `", $fields); // Convert array into SQL compatible string
	$data_sql = implode("', '", $data); // Convert array into SQL compatible string

	mysql_insert("INSERT INTO `players`(`$fields_sql`) VALUES ('$data_sql');");

	$created = time();
	$charid = user_character_id($import_data['name']);
	mysql_insert("INSERT INTO `znote_players`(`player_id`, `created`, `hide_char`, `comment`) VALUES ('$charid', '$created', '0', '');");

	// Player skills TFS 0.2, 0.3/4. (TFS 1.0 is done above character creation)
	if ($cnf['ServerEngine'] != 'TFS_10') {
		mysql_delete("DELETE FROM `player_skills` WHERE `player_id`='{$charid}';");
		mysql_insert("INSERT INTO `player_skills` (`player_id`, `skillid`, `value`) VALUES ('{$charid}', '0', '".$skills['fist']."'), ('{$charid}', '1', '".$skills['club']."'), ('{$charid}', '2', '".$skills['sword']."'), ('{$charid}', '3', '".$skills['axe']."'), ('{$charid}', '4', '".$skills['dist']."'), ('{$charid}', '5', '".$skills['shield']."'), ('{$charid}', '6', '".$skills['fishing']."');");
	}
}

// Returns counted value of all players online
function user_count_online() {
	if (config('ServerEngine') == 'TFS_10') {
		$online = mysql_select_single("SELECT COUNT(`player_id`) AS `value` FROM `players_online`;");
		return ($online !== false) ? $online['value'] : 0;
	} else {
		$data = mysql_select_single("SELECT COUNT(`id`) AS `count` from `players` WHERE `online` = 1;");
		return ($data !== false) ? $data['count'] : 0;
	}
}

// Returns counted value of all accounts.
function user_count_accounts() {
	$result = mysql_select_single("SELECT COUNT(`id`) AS `id` from `accounts`;");
	return ($result !== false) ? $result['id'] : 0;
}

/* user_character_data (fetches whatever data you want from players table)!
	Usage:
	$player = user_data(player_ID, 'name', 'level');
	echo "Character name: ". $player['name'] .". Level: ". $player['level'];
*/
function user_character_data($user_id) {
	$data = array();
	$user_id = (int)$user_id;
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
		return mysql_select_single("SELECT $fields FROM `znote`;");
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
		return mysql_select_single("SELECT $fields FROM `znote_accounts` WHERE `account_id` = $accid LIMIT 1;");
	} else return false;
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
		return mysql_select_single("SELECT $fields FROM `znote_visitors` WHERE `ip` = $longip;");
	} else return false;
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
		return mysql_select_single("SELECT $fields FROM `znote_visitors_details` WHERE `ip` = $longip;");
	} else return false;
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
		return mysql_select_single("SELECT $fields FROM `accounts` WHERE `id` = $user_id LIMIT 1;");
	} else return false;
}

// Checks if user is activated (Not in use atm)
function user_activated($username) {
	$username = sanitize($username);
	// Deprecated, removed from DB.
	return false;
}

// Checks that username exist in database
function user_exist($username) {
	$username = sanitize($username);
	if (config('ServerEngine') !== 'OTHIRE')
		$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `name`='$username';");
	else
		$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `id`='$username';");
	return ($data !== false) ? true : false;
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
	$player = mysql_select_single("SELECT `id` FROM `players` WHERE `name`='$username';");
	return ($player !== false) ? $player['id'] : false;
}

// Checks that this email exist.
function user_email_exist($email) {
	$email = sanitize($email);
	$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `email`='$email';");
	return ($data !== false) ? true : false;
}

// Fetch user account ID from registered email. (this is used by etc lost account)
function user_id_from_email($email) {
	$email = sanitize($email);
	 $data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `email`='$email';");
	 return ($data !== false) ? $data['id'] : false;
}

// Checks that a password exist in the database.
function user_password_exist($password) {
	$password = sha1($password); // No need to sanitize passwords since we encrypt them.
	$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `password`='$password';");
	return ($data !== false) ? true : false;
}

// Verify that submitted password match stored password in account id
function user_password_match($password, $account_id) {
	$password = sha1($password); // No need to sanitize passwords since we encrypt them.
	$account_id = (int)$account_id;
	$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `password`='$password' AND `id`='$account_id';");
	return ($data !== false) ? true : false;
}

// Get user ID from name
function user_id($username) {
	$username = sanitize($username);
	if (config('ServerEngine') !== 'OTHIRE')
		$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `name`='$username' LIMIT 1;");
	else
		$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `id`='$username' LIMIT 1;");
	if ($data !== false) return $data['id'];
	else return false;
}

// Get user login ID from username and password
function user_login_id($username, $password) {
	$username = sanitize($username);
	$password = sha1($password);
	if (config('ServerEngine') !== 'OTHIRE')
		$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `name`='$username' AND `password`='$password' LIMIT 1;");
	else
		$data = mysql_select_single("SELECT `id` FROM `accounts` WHERE `id`='$username' AND `password`='$password' LIMIT 1;");
	if ($data !== false) return $data['id'];
	else return false;
}

// TFS 0.3+ compatibility.
function user_login_id_03($username, $password) {
	if (config('salt') === true) {
		if (user_exist($username)) {
			$user_id = user_id($username);
			$username = sanitize($username);

			$data = mysql_select_single("SELECT `salt`, `id`, `name`, `password` FROM `accounts` WHERE `id`='$user_id';");
			$salt = $data['salt'];
			if (!empty($salt)) $password = sha1($salt.$password);
			else $password = sha1($password);
			return ($data !== false && $data['name'] == $username && $data['password'] == $password) ? $data['id'] : false;
		} else return false;
	} else return user_login_id($username, $password);
}

// Get character ID from character name
function user_character_id($charname) {
	$charname = sanitize($charname);
	$char = mysql_select_single("SELECT `id` FROM `players` WHERE `name`='$charname';");
	if ($char !== false) return $char['id'];
	else return false;
}

// Get character name from character ID
function user_character_name($charID) {
	$charID = (int)$charID;
	$char = mysql_select_single('SELECT `name` FROM `players` WHERE `id` = ' . $charID);
	if ($char !== false) return $char['name'];
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
	$username = sanitize($username);
	$password = sha1($password);
	if (config('ServerEngine') !== 'OTHIRE')
		$data = mysql_select_single("SELECT `id` FROM accounts WHERE name='$username' AND password='$password';");
	else
		$data = mysql_select_single("SELECT `id` FROM accounts WHERE id='$username' AND password='$password';");
	return ($data !== false) ? $data['id'] : false;
}

// Login a user with TFS 0.3 compatibility
function user_login_03($username, $password) {
	if (config('salt') === true) {
		$username = sanitize($username);
		$data = mysql_select_single("SELECT `salt`, `id`, `password`, `name` FROM `accounts` WHERE `name`='$username';");
		$salt = $data['salt'];
		if (!empty($salt)) $password = sha1($salt.$password);
		else $password = sha1($password);
		return ($data !== false && $data['name'] == $username && $data['password'] == $password) ? $data['id'] : false;
	} else return user_login($username, $password);
}

// Verify that user is logged in
function user_logged_in() {
	return (getSession('user_id') !== false) ? true : false;
}

function guild_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	$gname = get_guild_name($cid);
	$ename = get_guild_name($gid);
	$time = time();
	mysql_insert("INSERT INTO `guild_wars` (`guild1`, `guild2`, `name1`, `name2`, `status`, `started`, `ended`) VALUES ('$cid', '$gid', '$gname', '$ename', '0', '$time', '0');");
}

function accept_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	mysql_update("UPDATE `guild_wars` SET `status` = 1 WHERE `guild1` = '$cid' AND `guild2` = '$gid' AND `status` = 0;");
}

function reject_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	$time = time();
	mysql_update("UPDATE `guild_wars` SET `status` = 2, `ended` = '$time' WHERE `guild1` = '$cid' AND `guild2` = '$gid';");
}

function cancel_war_invitation($cid, $gid) {
	$cid = (int)$cid;
	$gid = (int)$gid;
	$time = time();
	mysql_update("UPDATE `guild_wars` SET `status` = 3, `ended` = '$time' WHERE `guild2` = '$cid' AND `guild1` = '$gid';");
}

?>

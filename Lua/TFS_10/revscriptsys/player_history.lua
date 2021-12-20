-- Auto install tables if we dont got them yet (first install)
db.query([[
	CREATE TABLE IF NOT EXISTS `player_history_skill` (
	  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	  `player_id` int(11) NOT NULL,
	  `lastlogin` bigint(20) unsigned NOT NULL,
	  `lastlogout` bigint(20) unsigned NOT NULL,
	  `town_id` int(11) NOT NULL,
	  `lastip` int(10) unsigned NOT NULL,
	  `skull` tinyint(1) NOT NULL,
	  `blessings` tinyint(2) NOT NULL,
	  `onlinetime` int(11) NOT NULL,
	  `balance` bigint(20) unsigned NOT NULL,
	  `level` int(11) NOT NULL,
	  `experience` bigint(20) NOT NULL,
	  `maglevel` int(11) NOT NULL,
	  `skill_fist` int(10) unsigned NOT NULL,
	  `skill_club` int(10) unsigned NOT NULL,
	  `skill_sword` int(10) unsigned NOT NULL,
	  `skill_axe` int(10) unsigned NOT NULL,
	  `skill_dist` int(10) unsigned NOT NULL,
	  `skill_shielding` int(10) unsigned NOT NULL,
	  `skill_fishing` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`id`),
	  FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
]])


-- Auto populate table if it is empty
local resultId = db.storeQuery("SELECT `id` FROM `player_history_skill` LIMIT 1;")
if resultId == false then
	db.asyncQuery([[
		INSERT INTO `player_history_skill` (
			`player_id`,
			`lastlogin`, 
			`lastlogout`, 
			`town_id`,
			`lastip`,
			`skull`,
			`blessings`,
			`onlinetime`,
			`balance`, 
			`level`, 
			`experience`, 
			`maglevel`, 
			`skill_fist`, 
			`skill_club`, 
			`skill_sword`, 
			`skill_axe`, 
			`skill_dist`, 
			`skill_shielding`, 
			`skill_fishing`
		)
		SELECT 
			`p`.`id` AS `player_id`, 
			`zp`.`created` AS `lastlogin`, 
			CASE WHEN `p`.`lastlogout` > 0 
				THEN `p`.`lastlogout` 
				ELSE `zp`.`created` 
			END AS `lastlogout`, 
			`p`.`town_id`,
			`p`.`lastip`,
			`p`.`skull`,
			`p`.`blessings`,
			`p`.`onlinetime`,
			`p`.`balance`, 
			`p`.`level`, 
			`p`.`experience`, 
			`p`.`maglevel`, 
			`p`.`skill_fist`, 
			`p`.`skill_club`, 
			`p`.`skill_sword`, 
			`p`.`skill_axe`, 
			`p`.`skill_dist`, 
			`p`.`skill_shielding`, 
			`p`.`skill_fishing` 
		FROM `players` AS `p`
		INNER JOIN `znote_players` AS `zp`
			ON `p`.`id` = `zp`.`player_id`
		ORDER BY `zp`.`created`
	]])
else
	result.free(resultId)
end


-- Logout event, triggered by logout, and death
function historyLogoutEvent(player)
	local blessdec = 0
	local i = 0
	while player:hasBlessing(i+1) do
		blessdec = blessdec+2^i
		i = i+1
	end

	local playerGuid = player:getGuid()
	db.query([[
		INSERT INTO `player_history_skill` (
			`player_id`,
			`lastlogin`, 
			`lastlogout`, 
			`town_id`,
			`lastip`,
			`skull`,
			`blessings`,
			`onlinetime`,
			`balance`, 
			`level`, 
			`experience`, 
			`maglevel`, 
			`skill_fist`, 
			`skill_club`, 
			`skill_sword`, 
			`skill_axe`, 
			`skill_dist`, 
			`skill_shielding`, 
			`skill_fishing`
		) VALUES (
			]]..table.concat({
				playerGuid,
				player:getLastLoginSaved(),
				os.time(),
				player:getTown():getId(),
				player:getIp(),
				player:getSkull(),
				blessdec,
				"(SELECT `onlinetime` FROM `players` WHERE `id`='"..playerGuid.."') + ".. os.time() - player:getLastLoginSaved(),
				player:getBankBalance(),
				player:getLevel(),
				player:getExperience(),
				player:getMagicLevel(),
				player:getSkillLevel(SKILL_FIST),
				player:getSkillLevel(SKILL_CLUB),
				player:getSkillLevel(SKILL_SWORD),
				player:getSkillLevel(SKILL_AXE),
				player:getSkillLevel(SKILL_DISTANCE),
				player:getSkillLevel(SKILL_SHIELD),
				player:getSkillLevel(SKILL_FISHING)
			}, ",")..[[
		);
	]])
end


-- Log player state on logout
local player_history_skill = CreatureEvent("player_history_skill")
function player_history_skill.onLogout(player)
	--print("2-logout["..player:getName().."]")
	historyLogoutEvent(player)
	return true
end
player_history_skill:register()


-- And on death
local player_history_skill_death = CreatureEvent("player_history_skill_death")
function player_history_skill_death.onDeath(creature, corpse, killer, mostDamageKiller, lastHitUnjustified, mostDamageUnjustified)
	--print("3-death["..creature:getName().."]")
	historyLogoutEvent(Player(creature))
end
player_history_skill_death:register()


-- If this is first login, insert current progress
local player_history_skill_login = CreatureEvent("player_history_skill_login")
function player_history_skill_login.onLogin(player)
	--print("1-login["..player:getName().."]")
	player:registerEvent("player_history_skill_death")

	local playerGuid = player:getGuid()
	local resultId = db.storeQuery("SELECT `id` FROM `player_history_skill` WHERE `player_id`="..playerGuid.." LIMIT 1;")
	if resultId == false then
		db.query([[
			INSERT INTO `player_history_skill` (
				`player_id`,
				`lastlogin`, 
				`lastlogout`, 
				`town_id`,
				`lastip`,
				`skull`,
				`blessings`,
				`onlinetime`,
				`balance`, 
				`level`, 
				`experience`, 
				`maglevel`, 
				`skill_fist`, 
				`skill_club`, 
				`skill_sword`, 
				`skill_axe`, 
				`skill_dist`, 
				`skill_shielding`, 
				`skill_fishing`
			)
			SELECT 
				`p`.`id` AS `player_id`, 
				`zp`.`created` AS `lastlogin`, 
				CASE WHEN `p`.`lastlogout` > 0 
					THEN `p`.`lastlogout` 
					ELSE `zp`.`created` 
				END AS `lastlogout`, 
				`p`.`town_id`,
				`p`.`lastip`,
				`p`.`skull`,
				`p`.`blessings`,
				`p`.`onlinetime`,
				`p`.`balance`, 
				`p`.`level`, 
				`p`.`experience`, 
				`p`.`maglevel`, 
				`p`.`skill_fist`, 
				`p`.`skill_club`, 
				`p`.`skill_sword`, 
				`p`.`skill_axe`, 
				`p`.`skill_dist`, 
				`p`.`skill_shielding`, 
				`p`.`skill_fishing` 
			FROM `players` AS `p`
			INNER JOIN `znote_players` AS `zp`
				ON `p`.`id` = `zp`.`player_id`
			WHERE `p`.`id` = ]]..playerGuid..[[
		]])
	else
		result.free(resultId)
	end
	return true
end
player_history_skill_login:register()

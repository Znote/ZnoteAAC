local deathListEnabled = true
local maxDeathRecords = 5

function onDeath(cid, corpse, killer, mostDamage, unjustified, mostDamage_unjustified)
	local player = Player(cid)

	player:sendTextMessage(MESSAGE_EVENT_ADVANCE, "You are dead.")
	if not deathListEnabled then
		return
	end

	local byPlayer = 0
	local killerCreature = Creature(killer)
	if killerCreature == nil then
		killerName = "field item"
	else
		if killerCreature:isPlayer() then
			byPlayer = 1
		else
			local master = killerCreature:getMaster()
			if master and master ~= killerCreature and master:isPlayer() then
				killerCreature = master
				byPlayer = 1
			end
		end
		killerName = killerCreature:getName()
	end

	local byPlayerMostDamage = 0
	if mostDamage == 0 then
		mostDamageName = "field item"
	else
		local mostDamageKiller = Creature(mostDamage)
		if mostDamageKiller:isPlayer() then
			byPlayerMostDamage = 1
		else
			local master = mostDamageKiller:getMaster()
			if master and master ~= mostDamageKiller and master:isPlayer() then
				mostDamageKiller = master
				byPlayerMostDamage = 1
			end
		end
		mostDamageName = mostDamageKiller:getName()
	end

	local playerGuid = player:getGuid()
	db.query("INSERT INTO `player_deaths` (`player_id`, `time`, `level`, `killed_by`, `is_player`, `mostdamage_by`, `mostdamage_is_player`, `unjustified`, `mostdamage_unjustified`) VALUES (" .. playerGuid .. ", " .. os.time() .. ", " .. player:getLevel() .. ", " .. db.escapeString(killerName) .. ", " .. byPlayer .. ", " .. db.escapeString(mostDamageName) .. ", " .. byPlayerMostDamage .. ", " .. unjustified .. ", " .. mostDamage_unjustified .. ")")
	local resultId = db.storeQuery("SELECT `player_id` FROM `player_deaths` WHERE `player_id` = " .. playerGuid)

	local deathRecords = 0
	local tmpResultId = resultId
	while tmpResultId ~= false do
		tmpResultId = result.next(resultId)
		deathRecords = deathRecords + 1
	end

	if resultId ~= false then
		result.free(resultId)
	end

	while deathRecords > maxDeathRecords do
		db.query("DELETE FROM `player_deaths` WHERE `player_id` = " .. playerGuid .. " ORDER BY `time` LIMIT 1")
		deathRecords = deathRecords - 1
	end

	if byPlayer == 1 then
		local playerGuild = player:getGuild()
		if playerGuild then
			local killerGuild = killerCreature:getGuild()
			if playerGuild ~= killerGuild and isInWar(cid, killerCreature) then
				local warId = false
				resultId = db.storeQuery("SELECT `id` FROM `guild_wars` WHERE `status` = 1 AND ((`guild1` = " .. killerGuild:getId() .. " AND `guild2` = " .. playerGuild:getId() .. ") OR (`guild1` = " .. playerGuild:getId() .. " AND `guild2` = " .. killerGuild:getId() .. "))")
				if resultId ~= false then
					warId = result.getDataInt(resultId, "id")
					result.free(resultId)
				end

				if warId ~= false then
					db.query("INSERT INTO `guildwar_kills` (`killer`, `target`, `killerguild`, `targetguild`, `time`, `warid`) VALUES (" .. db.escapeString(killerName) .. ", " .. db.escapeString(player:getName()) .. ", " .. killerGuild:getId() .. ", " .. playerGuild:getId() .. ", " .. os.time() .. ", " .. warId .. ")")
					local wars = db.storeQuery("SELECT `guild_wars`.`id`, (SELECT `limit` FROM `znote_guild_wars` WHERE `znote_guild_wars`.`id` = `guild_wars`.`id`) AS `limit`, (SELECT COUNT(1) FROM `guildwar_kills` WHERE `guildwar_kills`.`warid` = `guild_wars`.`id` AND `guildwar_kills`.`killerguild` = `guild_wars`.`guild1`) guild1_kills, (SELECT COUNT(1) FROM `guildwar_kills` WHERE `guildwar_kills`.`warid` = `guild_wars`.`id` AND `guildwar_kills`.`killerguild` = `guild_wars`.`guild2`) guild2_kills FROM `guild_wars` WHERE (`guild1` = " .. killerGuild:getId() .. " OR `guild2` = " .. killerGuild:getId() .. ") AND `id` = " .. warId .. " AND `status` = 1")
					if wars ~= false then
						guildKills1 = result.getDataInt(wars, "guild1_kills")
						guildKills2 = result.getDataInt(wars, "guild2_kills")
						fragLimit = result.getDataInt(wars, "limit")
						result.free(wars)
					end
					
					if guildKills1 >= fragLimit or guildKills2 >= fragLimit then
						broadcastMessage(string.format("%s has just won the war against %s.", killerGuild:getName(), playerGuild:getName()), MESSAGE_EVENT_ADVANCE)
						db.query("UPDATE `guild_wars` SET `status` = 4, `ended` = " .. os.time() .. " WHERE `status` = 1 AND `id` = " .. warId)
					end
				end
			end
		end
	end
end
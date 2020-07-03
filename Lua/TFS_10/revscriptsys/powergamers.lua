
-- getEternalStorage and setEternalStorage 
-- can be added to data/global.lua if you want to use eternal storage for another purpose than this.
-- Regular TFS global storage values get reset every time server reboots. This does not.
local function getEternalStorage(key, parser)
	local value = result.getString(db.storeQuery("SELECT `value` FROM `znote_global_storage` WHERE `key` = ".. key .. ";"), "value")
	if not value then
		if parser then
			return false
		else
			return -1
		end
	end
	result.free(value)
	return tonumber(value) or value
end

local function setEternalStorage(key, value)
	if getEternalStorage(key, true) then
		db.query("UPDATE `znote_global_storage` SET `value` = '".. value .. "' WHERE `key` = ".. key .. ";")
	else
		db.query("INSERT INTO `znote_global_storage` (`key`, `value`) VALUES (".. key ..", ".. value ..");")
	end
	return true
end

-- SQL Query to execute: --
--[[
ALTER TABLE `znote_players` ADD `exphist_lastexp` BIGINT(255) NOT NULL DEFAULT '0', 
ADD `exphist1` BIGINT(255) NOT NULL DEFAULT '0', 
ADD `exphist2` BIGINT(255) NOT NULL DEFAULT '0', 
ADD `exphist3` BIGINT(255) NOT NULL DEFAULT '0', 
ADD `exphist4` BIGINT(255) NOT NULL DEFAULT '0', 
ADD `exphist5` BIGINT(255) NOT NULL DEFAULT '0', 
ADD `exphist6` BIGINT(255) NOT NULL DEFAULT '0', 
ADD `exphist7` BIGINT(255) NOT NULL DEFAULT '0', 
ADD `onlinetimetoday` BIGINT(20) NOT NULL DEFAULT '0',
ADD `onlinetime1` BIGINT(20) NOT NULL DEFAULT '0',
ADD `onlinetime2` BIGINT(20) NOT NULL DEFAULT '0',
ADD `onlinetime3` BIGINT(20) NOT NULL DEFAULT '0',
ADD `onlinetime4` BIGINT(20) NOT NULL DEFAULT '0',
ADD `onlinetime5` BIGINT(20) NOT NULL DEFAULT '0',
ADD `onlinetime6` BIGINT(20) NOT NULL DEFAULT '0',
ADD `onlinetime7` BIGINT(20) NOT NULL DEFAULT '0',
ADD `onlinetimeall` BIGINT(20) NOT NULL DEFAULT '0';
]]--

-- after that execute: --
--[[
UPDATE `znote_players` AS `z` INNER JOIN `players` AS `p` ON `p`.`id`=`z`.`player_id` SET `z`.`exphist_lastexp`=`p`.`experience`;
]]--

local globalevent = GlobalEvent("PowerGamers")

function globalevent.onThink(...)
	if tonumber(os.date("%d")) ~= getEternalStorage(23856) then
		setEternalStorage(23856, (tonumber(os.date("%d"))))
		db.query("UPDATE `znote_players` SET `onlinetime7`=`onlinetime6`, `onlinetime6`=`onlinetime5`, `onlinetime5`=`onlinetime4`, `onlinetime4`=`onlinetime3`, `onlinetime3`=`onlinetime2`, `onlinetime2`=`onlinetime1`, `onlinetime1`=`onlinetimetoday`, `onlinetimetoday`=0;")
		db.query("UPDATE `znote_players` `z` INNER JOIN `players` `p` ON `p`.`id`=`z`.`player_id` SET `z`.`exphist7`=`z`.`exphist6`, `z`.`exphist6`=`z`.`exphist5`, `z`.`exphist5`=`z`.`exphist4`, `z`.`exphist4`=`z`.`exphist3`, `z`.`exphist3`=`z`.`exphist2`, `z`.`exphist2`=`z`.`exphist1`, `z`.`exphist1`=`p`.`experience`-`z`.`exphist_lastexp`, `z`.`exphist_lastexp`=`p`.`experience`;")
	end
	db.query("UPDATE `znote_players` SET `onlinetimetoday` = `onlinetimetoday` + 60, `onlinetimeall` = `onlinetimeall` + 60 WHERE `player_id` IN (SELECT `player_id` FROM `players_online` WHERE `players_online`.`player_id` = `znote_players`.`player_id`)")
	return true
end

globalevent:interval(60000)
globalevent:register()

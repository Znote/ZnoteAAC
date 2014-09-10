function onThink(interval, lastExecution, thinkInterval)
	if (tonumber(os.date("%d")) ~= getGlobalStorageValue(23856)) then
		setGlobalStorageValue(23856, (tonumber(os.date("%d"))))
		db.query("UPDATE `znote_players` SET `onlinetime7`=`onlinetime6`, `onlinetime6`=`onlinetime5`, `onlinetime5`=`onlinetime4`, `onlinetime4`=`onlinetime3`, `onlinetime3`=`onlinetime2`, `onlinetime2`=`onlinetime1`, `onlinetime1`=`onlinetimetoday`, `onlinetimetoday`=0;")
		db.query("UPDATE `znote_players` `a` INNER JOIN `players` `b` ON `a`.`id`=`b`.`player_id` SET `a`.`exphist7`=`a`.`exphist6`,  `a`.`exphist6`=`a`.`exphist5`, `a`.`exphist5`=`a`.`exphist4`, `a`.`exphist4`=`a`.`exphist3`, `a`.`exphist3`=`a`.`exphist2`, `a`.`exphist2`=`a`.`exphist1`, `a`.`exphist1`=`b`.`experience`-`a`.`exphist_lastexp`, `a`.`exphist_lastexp`=`b`.`experience`;")
	end
	db.query("UPDATE `znote_players` SET `onlinetimetoday` = `onlinetimetoday` + 60, `onlinetimeall` = `onlinetimeall` + 60 WHERE `id` IN (SELECT `player_id` FROM `players_online` WHERE `players_online`.`player_id` = `players`.`id`)")
	return true
end

-- TFS 1.0 (globalevents.xml)
--	<!-- Power Gamers -->
--	<globalevent name="PowerGamers" interval="60000" script="powergamers.lua"/>

-- SQL  (remember to remove all (--) before executing)--
--ALTER TABLE `znote_players` ADD `exphist_lastexp` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist1` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist2` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist3` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist4` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist5` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist6` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist7` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `onlinetimetoday` BIGINT( 20 ) NOT NULL DEFAULT '0',
--ADD `onlinetime1` BIGINT( 20 ) NOT NULL DEFAULT '0',
--ADD `onlinetime2` BIGINT( 20 ) NOT NULL DEFAULT '0',
--ADD `onlinetime3` BIGINT( 20 ) NOT NULL DEFAULT '0',
--ADD `onlinetime4` BIGINT( 20 ) NOT NULL DEFAULT '0',
--ADD `onlinetime5` BIGINT( 20 ) NOT NULL DEFAULT '0',
--ADD `onlinetime6` BIGINT( 20 ) NOT NULL DEFAULT '0',
--ADD `onlinetime7` BIGINT( 20 ) NOT NULL DEFAULT '0',
--ADD `onlinetimeall` BIGINT( 20 ) NOT NULL DEFAULT '0';
---------------


-- after that execute --
 --UPDATE `znote_players` AS `z` INNER JOIN  `players` AS `p` ON `p`.`id`=`z`.`player_id` SET `z`.`exphist_lastexp`=`p`.`experience`

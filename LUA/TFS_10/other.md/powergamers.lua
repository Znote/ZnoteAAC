function onThink(interval, lastExecution, thinkInterval)
	if (tonumber(os.date("%d")) ~= getGlobalStorageValue(23856)) then
		setGlobalStorageValue(23856, (tonumber(os.date("%d"))))
		db.query("UPDATE `znote_players` `a` INNER JOIN `players` `b` ON `a`.`id`=`b`.`id` SET `a`.`exphist7`=`a`.`exphist6`,  `a`.`exphist6`=`a`.`exphist5`, `a`.`exphist5`=`a`.`exphist4`, `a`.`exphist4`=`a`.`exphist3`, `a`.`exphist3`=`a`.`exphist2`, `a`.`exphist2`=`a`.`exphist1`, `a`.`exphist1`=`b`.`experience`-`a`.`exphist_lastexp`, `a`.`exphist_lastexp`=`b`.`experience`;")
	end
	return true
end

-- TFS 1.0 (globalevents.xml)
--	<!-- Power Gamers -->
--	<globalevent name="PowerGamers" interval="15000" script="powergamers.lua"/>

-- SQL  (remember to remove all -- before executing)--
--ALTER TABLE `znote_players` ADD `exphist_lastexp` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist1` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist2` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist3` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist4` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist5` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist6` BIGINT( 255 ) NOT NULL DEFAULT '0', 
--ADD `exphist7` BIGINT( 255 ) NOT NULL DEFAULT '0', 

-- after that execute --
-- UPDATE `znote_players` SET `exphist_lastexp`=`players`.`experience`  

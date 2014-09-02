function onThink(interval, lastExecution, thinkInterval)
	if (tonumber(os.date("%d")) ~= getGlobalStorageValue(23856)) then
		setGlobalStorageValue(23856, (tonumber(os.date("%d"))))
		db.query("UPDATE `znote_players` `a` INNER JOIN `players` `b` ON `a`.`id`=`b`.`id` SET `a`.`exphist7`=`a`.`exphist6`,  `a`.`exphist6`=`a`.`exphist5`, `a`.`exphist5`=`a`.`exphist4`, `a`.`exphist4`=`a`.`exphist3`, `a`.`exphist3`=`a`.`exphist2`, `a`.`exphist2`=`a`.`exphist1`, `a`.`exphist1`=`b`.`experience`-`a`.`exphist_lastexp`, `a`.`exphist_lastexp`=`b`.`experience`;")
	end
	return true
end

TFS 1.0 (globalevents.xml)
	<!-- Power Gamers -->
	<globalevent name="PowerGamers" interval="15000" script="powergamers.lua"/>

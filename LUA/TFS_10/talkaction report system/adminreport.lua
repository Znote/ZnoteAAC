-- <talkaction words="!report" separator=" " script="adminreport.lua"/>
-- Coded by Dark ShaoOz, modified by Znote
function onSay(player, words, param)
	local storage = 6708 -- You can change the storage if its already in use
	local delaytime = 30 -- Exhaust In Seconds.
	if param == '' then
		player:sendTextMessage(MESSAGE_STATUS_CONSOLE_ORANGE, "Command param required.")
		return true
	end
	if player:getStorageValue(storage) <= os.time() then
		player:sendTextMessage(MESSAGE_INFO_DESCR, "Your report has been received successfully!")
		db.query("INSERT INTO `znote_player_reports` (`id` ,`name` ,`posx` ,`posy` ,`posz` ,`report_description` ,`date`)VALUES (NULL ,  " .. db.escapeString(player:getName()) .. ",  '" .. player:getPosition().x .. "',  '" .. player:getPosition().y .. "',  '" .. player:getPosition().z .. "',  " .. db.escapeString(param) .. ",  '" .. os.time() .. "')")
		player:setStorageValue(storage, os.time() + delaytime)
	else
		player:sendTextMessage(MESSAGE_STATUS_WARNING, "You have to wait " .. player:getStorageValue(storage) - os.time() .. " seconds to report again.")
	end
	return true
end

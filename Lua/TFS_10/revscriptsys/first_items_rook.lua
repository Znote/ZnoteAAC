local creatureevent = CreatureEvent("FirstItemsRook")

local firstItems = {2050, 2382} -- torch and club

function creatureevent.onLogin(player)
	if player:getLastLoginSaved() <= 0 then
		for i = 1, #firstItems do
			player:addItem(firstItems[i], 1)
		end
		player:addItem(player:getSex() == 0 and 2651 or 2650, 1) -- coat
		player:addItem(ITEM_BAG, 1)
		player:addItem(2674, 1) -- red apple
	end
	return true
end

creatureevent:register()

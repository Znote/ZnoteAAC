-- Znote Shop v1.0 for Znote AAC on TFS 1.0+.
function onSay(cid, words, param)
	local storage = 54073 -- Make sure to select non-used storage. This is used to prevent SQL load attacks.
	local cooldown = 15 -- in seconds.
	local player = Player(cid)

	if player:getStorageValue(storage) <= os.time() then
		player:setStorageValue(storage, os.time() + cooldown)

		-- Create the query
		local orderQuery = db.storeQuery("SELECT `id`, `type`, `itemid`, `count` FROM `znote_shop_orders` WHERE `account_id` = " .. player:getAccountId() .. " LIMIT 1;")

		-- Detect if we got any results
		if orderQuery ~= false then
			-- Fetch order values
			local q_id = result.getDataInt(orderQuery, "id")
			local q_type = result.getDataInt(orderQuery, "type")
			local q_itemid = result.getDataInt(orderQuery, "itemid")
			local q_count = result.getDataInt(orderQuery, "count")
			result.free(orderQuery)

			-- ORDER TYPE 1 (Regular item shop products)
			if q_type == 1 then
				-- Get wheight
				if player:getFreeCapacity() >= ItemType(q_itemid):getWeight(q_count) then
					db.query("DELETE FROM `znote_shop_orders` WHERE `id` = " .. q_id .. ";")
					player:addItem(q_itemid, q_count)
					player:sendTextMessage(MESSAGE_INFO_DESCR, "Congratulations! You have received " .. q_count .. " x " .. ItemType(q_itemid):getName() .. "!")
				else
					player:sendTextMessage(MESSAGE_STATUS_WARNING, "Need more CAP!")
				end
			end
			-- Add custom order types here
			-- Type 2 is reserved for premium days and is handled on website, not needed here.
			-- Type 3 is reserved for character gender(sex) change and is handled on website as well.
			-- So use type 4+ for custom stuff, like etc packages.
			-- if q_type == 4 then
			-- end
		else
			player:sendTextMessage(MESSAGE_STATUS_WARNING, "You have no orders.")
		end
	else
		player:sendTextMessage(MESSAGE_STATUS_CONSOLE_BLUE, "Can only be executed once every " .. cooldown .. " seconds. Remaining cooldown: " .. player:getStorageValue(storage) - os.time())
	end
	return false
end

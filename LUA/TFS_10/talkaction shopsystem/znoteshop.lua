-- Znote Shop v1.0 for Znote AAC on TFS 1.1
function onSay(player, words, param)
	local storage = 54073 -- Make sure to select non-used storage. This is used to prevent SQL load attacks.
	local cooldown = 15 -- in seconds.

	if player:getStorageValue(storage) <= os.time() then
		player:setStorageValue(storage, os.time() + cooldown)

		-- Create the query
		local orderQuery = db.storeQuery("SELECT `id`, `type`, `itemid`, `count` FROM `znote_shop_orders` WHERE `account_id` = " .. player:getAccountId() .. " LIMIT 1;")

		-- Detect if we got any results
		if orderQuery ~= false then
			-- Fetch order values
			local q_id = result.getNumber(orderQuery, "id")
			local q_type = result.getNumber(orderQuery, "type")
			local q_itemid = result.getNumber(orderQuery, "itemid")
			local q_count = result.getNumber(orderQuery, "count")
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

			-- ORDER TYPE 5 (Outfit and addon)
			if q_type == 5 then
				-- Make sure player don't already have this outfit and addon
				if not player:hasOutfit(q_itemid, q_count) then
					db.query("DELETE FROM `znote_shop_orders` WHERE `id` = " .. q_id .. ";")
					player:addOutfit(q_itemid)
					player:addOutfitAddon(q_itemid, q_count)
					player:sendTextMessage(MESSAGE_INFO_DESCR, "Congratulations! You have received a new outfit!")
				else
					player:sendTextMessage(MESSAGE_STATUS_WARNING, "You already have this outfit and addon!")
				end
			end

			-- ORDER TYPE 6 (Mounts)
			if q_type == 6 then
				-- Make sure player don't already have this outfit and addon
				if not player:hasMount(q_itemid) then
					db.query("DELETE FROM `znote_shop_orders` WHERE `id` = " .. q_id .. ";")
					player:addMount(q_itemid)
					player:sendTextMessage(MESSAGE_INFO_DESCR, "Congratulations! You have received a new mount!")
				else
					player:sendTextMessage(MESSAGE_STATUS_WARNING, "You already have this mount!")
				end
			end
			
			-- Add custom order types here
			-- Type 1 is for itemids (Already coded here)
			-- Type 2 is for premium (Coded on web)
			-- Type 3 is for gender change (Coded on web)
			-- Type 4 is for character name change (Coded on web)
			-- Type 5 is for character outfit and addon (Already coded here)
			-- Type 6 is for mounts (Already coded here)
			-- So use type 7+ for custom stuff, like etc packages.
			-- if q_type == 7 then
			-- end
		else
			player:sendTextMessage(MESSAGE_STATUS_WARNING, "You have no orders.")
		end
	else
		player:sendTextMessage(MESSAGE_STATUS_CONSOLE_BLUE, "Can only be executed once every " .. cooldown .. " seconds. Remaining cooldown: " .. player:getStorageValue(storage) - os.time())
	end
	return false
end
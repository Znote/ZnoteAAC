function onLogin(cid)
	local storage = 30055 -- storage value

	local sorcItems = {
			2460, -- Brass helmet
			2465, -- Brass armor
			2190, -- Wand of vortex
			2511, -- Brass shield
			2478, -- Brass legs
			2643, -- Leather boots
			1988, -- Brown backpack
			2050 -- torch
		}
	local druidItems = {
			2460, -- Brass helmet
			2465, -- Brass armor
			2511, -- Brass shield
			2182, -- Snakebite rod
			2478, -- Brass legs
			2643, -- Leather boots
			1988, -- Brown backpack
			2050 -- torch
		}
	local pallyItems = {
			2460, -- Brass helmet
			2465, -- Brass armor
			2456, -- Bow
			2478, -- Brass legs
			2643, -- Leather boots
			1988, -- Brown backpack
		}
	local kinaItems = {
			2460, -- Brass helmet
			2465, -- Brass armor
			2511, -- Brass shield
			2412, -- Katana
			2478, -- Brass legs
			2643, -- Leather boots
			1988, -- Brown backpack
			2050 -- torch
		}

	if getPlayerStorageValue(cid, storage) == -1 then
		setPlayerStorageValue(cid, storage, 1)
		if getPlayerVocation(cid) == 1 then
			-- Sorcerer
			for i = 1, table.getn(sorcItems), 1 do
				doPlayerAddItem(cid, sorcItems[i], 1, false)
			end

		elseif getPlayerVocation(cid) == 2 then
			-- Druid
			for i = 1, table.getn(druidItems), 1 do
				doPlayerAddItem(cid, druidItems[i], 1, false)
			end

		elseif getPlayerVocation(cid) == 3 then
			-- Paladin
			for i = 1, table.getn(pallyItems), 1 do
				doPlayerAddItem(cid, pallyItems[i], 1, false)
			end
			-- 8 arrows
			doPlayerAddItem(cid, 2544, 8, false)

		elseif getPlayerVocation(cid) == 4 then
			-- Knight
			for i = 1, table.getn(kinaItems), 1 do
				doPlayerAddItem(cid, kinaItems[i], 1, false)
			end
		end

		-- Common for all
		doPlayerAddItem(cid, 2674, 5, false) -- 5 apples
		doPlayerAddItem(cid, 2120, 1, false) -- 1 rope
	end
	return true
end

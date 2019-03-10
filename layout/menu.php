<ul class="sf-menu" id="nav">
	<li><a href="index.php">Home</a></li>
	<li><a href="downloads.php">Downloads</a></li>
	<li><a href="serverinfo.php">Server Information</a></li>

	<li><a href="forum.php">Community</a>
		<ul> <!-- (sub)dropdown COMMUNITY -->
			<li><a href="market.php">Item Market</a></li>
			<li><a href="gallery.php">Gallery</a></li>
			<li><a href="support.php">Support</a></li>
			<li><a href="helpdesk.php">Helpdesk</a></li>
			<li><a href="houses.php">Houses</a></li>
			<li><a href="deaths.php">Deaths</a></li>
			<li><a href="killers.php">Killers</a></li>
			<li><a href="spells.php">Spells</a></li>
			<?php if ($config['items'] == true) { ?><li><a href="items.php">Items</a></li><?php } ?>
		</ul>
	</li>
	<li><a href="forum.php">Forum</a></li>
	
	<li><a href="shop.php">Shop</a>
		<ul> <!-- (sub)dropdown SHOP -->
			<li><a href="buypoints.php">Buy Points</a></li>
			<li><a href="shop.php">Shop Offers</a></li>
			<?php if ($config['shop_auction']['characterAuction']): ?>
				<li><a href="auctionChar.php">Character Auction</a></li>
			<?php endif; ?>
		</ul>
	</li>
	<li><a href="guilds.php">Guilds</a>
	<?php if ($config['guildwar_enabled'] === true) { ?>
		<ul>
			<li><a href="guilds.php">Guild List</a></li>
			<li><a href="guildwar.php">Guild Wars</a></li>
		</ul>
	<?php } ?></li>
	<li><a href="changelog.php">Changelog</a></li>
</ul>
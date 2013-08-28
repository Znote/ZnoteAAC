<?php require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php'; 

// Import from config:
$auction = $config['shop_auction'];

if ($auction['characterAuction']) {
	?>
<h1>Character auctioning</h1>
<table class="auction_char">
	<tr class="yellow">
		<td>Name</td>
		<td>Level</td>
		<td>Vocation</td>
		<td>Image</td>
		<td>Price/Buy</td>
	</tr>
	<tr>
		<td><a href="characterprofile.php?name=Tester" target="_BLANK">Tester</a></td>
		<td>105</td>
		<td>Sorcerer</td>
		<td><a href="asd" target="_BLANK">VIEW</a></td>
		<td><button>105 points</button></td>
	</tr>
</table>
<textarea cols="65" rows="15">
CREATE TABLE IF NOT EXISTS `znote_auction_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `bidder_id` int(11) NOT NULL,
  `vocation` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
</textarea>
	<?php
} else echo "<p>Character shop auctioning system is disabled.</p>";

include 'layout/overall/footer.php'; ?>


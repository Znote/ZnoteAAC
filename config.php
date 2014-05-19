<?php
	// Available options: TFS_02, TFS_03
	// TFS 0.2 = TFS_02
	// TFS 0.3 = TFS_03 (If ur using 0.3.6, set $config['salt'] to false)!
	// TFS 0.4 = TFS_03
	// TFS 1.0 = TFS_10 (Under developement)
	$config['TFSVersion'] = 'TFS_10';

	$config['site_title'] = 'Znote AAC';
	$config['site_title_context'] = 'Because open communities are good communities. :3';

	// Path to server folder without / Example: C:\Users\Alvaro\Documents\GitHub\forgottenserver
	$config['server_path'] = ''; 


	// ------------------------ \\
	// MYSQL CONNECTION DETAILS \\
	// ------------------------ \\

	// phpmyadmin username for OT server: (DONT USE "root" if ur hosting to public.).
	$config['sqlUser'] = 'tfs10';

	// phpmyadmin password for OT server:
	$config['sqlPassword'] = 'tfs10';

	// The database name to connect to. (This is usually same as username).
	$config['sqlDatabase'] = 'tfs10';

	// Hostname is usually localhost or 127.0.0.1.
	$config['sqlHost'] = 'localhost';

	/* CLOCK FUNCTION
		- getClock() = returns current time in numbers.
		- getClock(time(), true) = returns current time in formatted date
		- getClock(false, true) = same as above
		- getClock(false, true, false) = get current time, don't adjust timezone 
		- echo getClock($profile_data['lastlogin'], true); = from characterprofile,
		explains when user was last logged in. */
	function getClock($time = false, $format = false, $adjust = true) {
		if ($time === false) $time = time();
		// Date string representation
		$date = "d F Y (H:i)"; // 15 July 2013 (13:50)
		if ($adjust) $adjust = (1 * 3600); // Adjust to fit your timezone.
		else $adjust = 0;
		if ($format) return date($date, $time+$adjust);
		else return $time+$adjust;
	}

	// ------------------- \\
	// CUSTOM SERVER STUFF \\
	// ------------------- \\
	// Enable / disable Questlog function (true / false) 
	        $config['EnableQuests'] = false;

	// array for filling questlog (Questid, max value, name, end of the quest fill 1 for the last part 0 for all others)
	        $config['Quests'] = array(
                array(1501,100,'Killing in the Name of',0),
                array(1502,150,'Killing in the Name of',0),
                array(65001,100,'Killing in the Name of',0),
                array(65002,150,'Killing in the Name of',0),
                array(65003,300,'Killing in the Name of',0),
                array(65004,3,'Killing in the Name of',0),
                array(65005,300,'Killing in the Name of',0),
                array(65006,150,'Killing in the Name of',0),
                array(65007,200,'Killing in the Name of',0),
                array(65008,300,'Killing in the Name of',0),
                array(65009,300,'Killing in the Name of',0),
                array(65010,300,'Killing in the Name of',0),
                array(65011,300,'Killing in the Name of',0),
                array(65012,300,'Killing in the Name of',0),
                array(65013,300,'Killing in the Name of',0),
                array(65014,300,'Killing in the Name of',1),
                array(12110,2,'The Inquisition',0),
                array(12111,7,'The Inquisition',0),
                array(12112,3,'The Inquisition',0),
                array(12113,6,'The Inquisition',0),
                array(12114,3,'The Inquisition',0),
                array(12115,3,'The Inquisition',0),
                array(12116,3,'The Inquisition',0),
                array(12117,5,'The Inquisition',1),
                array(330,3,'Sam\'s Old Backpack',1),
                array(12121,3,'The Ape City',0),
                array(12122,5,'The Ape City',0),
                array(12123,3,'The Ape City',0),
                array(12124,3,'The Ape City',0),
                array(12125,3,'The Ape City',0),
                array(12126,3,'The Ape City',0),
                array(12127,4,'The Ape City',0),
                array(12128,3,'The Ape City',0),
                array(12129,3,'The Ape City',1),
                array(12101,1,'The Ancient Tombs',0),
                array(12102,1,'The Ancient Tombs',0),
                array(12103,1,'The Ancient Tombs',0),
                array(12104,1,'The Ancient Tombs',0),
                array(12105,1,'The Ancient Tombs',0),
                array(12106,1,'The Ancient Tombs',0),
                array(12107,1,'The Ancient Tombs',1),
                array(12022,3,'Barbarian Test Quest',0),
                array(12022,3,'Barbarian Test Quest',0),
                array(12022,3,'Barbarian Test Quest',1),
                array(12025,3,'The Ice Islands Quest',0),
                array(12026,5,'The Ice Islands Quest',0),
                array(12027,3,'The Ice Islands Quest',0),
                array(12028,2,'The Ice Islands Quest',0),
                array(12029,6,'The Ice Islands Quest',0),
                array(12030,8,'The Ice Islands Quest',0),
                array(12031,3,'The Ice Islands Quest',0),
                array(12032,4,'The Ice Islands Quest',0),
                array(12033,2,'The Ice Islands Quest',0),
                array(12034,2,'The Ice Islands Quest',0),
                array(12035,2,'The Ice Islands Quest',0),
                array(12036,6,'The Ice Islands Quest',1),
                );

	// Vocation ids and names.
	$config['vocations'] = array(
		0 => 'No vocation',
		1 => 'Sorcerer',
		2 => 'Druid',
		3 => 'Paladin',
		4 => 'Knight',
		5 => 'Master Sorcerer',
		6 => 'Elder Druid',
		7 => 'Royal Paladin',
		8 => 'Elite Knight',
	);

	/* Vocation stat gains per level
		- Ordered by vocation ID
		- Currently used for admin_skills page. */
	$config['vocations_gain'] = array(
		0 => array(
			'hp' => 5,
			'mp' => 5,
			'cap' => 10
		),
		1 => array(
			'hp' => 5,
			'mp' => 30,
			'cap' => 10
		),
		2 => array(
			'hp' => 5,
			'mp' => 30,
			'cap' => 10
		),
		3 => array(
			'hp' => 10,
			'mp' => 15,
			'cap' => 20
		),
		4 => array(
			'hp' => 15,
			'mp' => 5,
			'cap' => 25
		),
		5 => array(
			'hp' => 5,
			'mp' => 30,
			'cap' => 10
		),
		6 => array(
			'hp' => 5,
			'mp' => 30,
			'cap' => 10
		),
		7 => array(
			'hp' => 10,
			'mp' => 15,
			'cap' => 20
		),
		8 => array(
			'hp' => 15,
			'mp' => 5,
			'cap' => 25
		),
	);
	// Town ids and names: (In RME map editor, open map, click CTRL + T to view towns, their names and their IDs. 
	// townID => 'townName' etc: ['3'=>'Thais']
	$config['towns'] = array(
		2 => 'Thyrfing',
		3 => 'Town 3',
	);

	// - TFS 1.0 ONLY -- HOUSE AUCTION SYSTEM!
	$config['houseConfig'] = array(
		'HouseListDefaultTown' => 1, // Default town id to display when visting house list page page.
		'minimumBidSQM' => 200, // minimum bid cost on auction (per SQM)
		'auctionPeriod' => 24 * 60 * 60, // 24 hours auction time.
		'housesPerPlayer' => 1,
		'requirePremium' => false,
		'levelToBuyHouse' => 8,
	);

	// Leave on black square in map and player should get teleported to their selected town.
	// If chars get buggy set this position to a beginner location to force players there.
	$config['default_pos'] = array(
		'x' => 5,
		'y' => 5,
		'z' => 2,
	);

	$config['war_status'] = array(
		0 => 'Pending..',
		1 => 'Accepted',
		2 => 'Rejected',
		3 => 'Cancelled',
		4 => '???',
		5 => 'Ended',
	);

	/* -- SUB PAGES -- 
		Some custom layouts/templates have custom pages, they can use
		this sub page functionality for that.
	*/
	$config['allowSubPages'] = true;

	// ---------------- \\
	// Create Character \\
	// ---------------- \\

	// Max characters on each account:
	$config['max_characters'] = 7;

	// Available character vocation users can create.
	$config['available_vocations'] = array(1, 2, 3, 4);

	// Available towns (specify town ids, etc: (0, 1, 2); to display 3 town options (town id 0, 1 and 2).
	$config['available_towns'] = array(2);

	$config['level'] = 8;
	$config['health'] = 185;
	$config['mana'] = 40;
	$config['cap'] = 435;
	$config['soul'] = 100;

	$config['maleOutfitId'] = 128;
	$config['femaleOutfitId'] = 136;
	$config['lookHead'] = 78;
	$config['lookBody'] = 68;
	$config['lookLegs'] = 58;
	$config['lookFeet'] = 76;

	// No vocation info (if user select vocation id 0, we force thees configurations on him
	$config['nvlevel'] = 1;
	$config['nvHealth'] = 150;
	$config['nvMana'] = 5;
	$config['nvCap'] = 400;
	$config['nvSoul'] = 100;

	$config['nvForceTown'] = 0; // Force a town to no vocation even though he selected something else? 0 = no, 1 = yes.
	$config['nvTown'] = 0; // Town id to force no vocations to get to, if nvForceTown is 1.

	// Minimum allowed character name letters. Etc 4 letters: "Kåre".
	$config['minL'] = 4;
	// Maximum allowed character name letters. Etc 20 letters: "Bobkåreolesofiesberg"
	$config['maxL'] = 20;

	// Maximum allowed character name words. Etc 2 words = "Bob Kåre", 3 words: "Bob Arne Kåre" as max char name words.
	$config['maxW'] = 2;

	// -------------- \\
	// WEBSITE STUFF  \\
	// -------------- \\

	// News to be displayed per page
	$config['news_per_page'] = 4;

	// Enable or disable changelog ticker in news page.
	$config['UseChangelogTicker'] = true;

	// Highscore configuration
	$config['highscore'] = array(
			'rows' => 100,
			'rowsPerPage' => 20,
			'ignoreGroupId' => 2, // Ignore group id higher than this (staff)
		);

	// ONLY FOR TFS 0.2 (TFS 0.3/4 users don't need to care about this, as its fully loaded from db)
	$config['house'] = array(
			'house_file' => 'C:\test\Mystic Spirit_0.2.5\data\world\forgotten-house.xml',
			'price_sqm' => '50', // price per house sqm
		);

	$config['status'] = array(
		'status_check' => false, //enable or disable status checker
		'status_ip' => '127.0.0.1',
		'status_port' => "7171",
		);

	$config['delete_character_interval'] = '3 DAY'; // Delay after user character delete request is executed eg. 1 DAY, 2 HOUR, 3 MONTH etc. 

	$config['validate_IP'] = true; // Only allow legal IP addresses to register and create character.
	$config['salt'] = false; // Some noob 0.3.6 servers don't support salt.

	// Restricted names
	$config['invalidNameTags'] = array("god", "gm", "cm", "gamemaster", "hoster", "admin", "admim", "adm", "owner", "staff");

	// Use guild logo system
	$config['use_guild_logos'] = 4;

	// Level requirement to create guild? (Just set it to 1 to allow all levels).
	$config['create_guild_level'] = 8;

	// Change Gender can be purchased in shop, or perhaps you want to allow everyone to change gender for free?
	$config['free_sex_change'] = false;

	// Do you need to have premium account to create a guild?
	$config['guild_require_premium'] = false;

	$config['guildwar_enabled'] = true;

	// Use htaccess rewrite? (basically this makes website.com/username work instead of website.com/characterprofile.php?name=username
	// Linux users needs to enable mod_rewrite php extention to make it work properly, so set it to false if your lost and using Linux.
	$config['htwrite'] = true;

	// What client version and server port are you using on this OT?
	// Used for the Downloads page.
	$config['client'] = 1037; // 954 = tibia 9.54

	 // Download link to client. Recommended:
	 // Select download link from remere map editor website!
	 // See list of clients: http://remeresmapeditor.com/marklar.php?clients
	$config['client_download'] = 'http://clients.tibiaking.com/client/windows/'. $config['client'] .'';
	$config['client_download_linux'] = 'http://clients.tibiaking.com/client/linux/'. $config['client'] .'';

	$config['port'] = 7171; // Port number to connect to your OT.

	// How often do you want highscores to update?
	$config['cache_lifespan'] = 5;//60 * 15; // 15 minutes.

	// WARNING! Account names written here will have admin access to web page!
	$config['page_admin_access'] = array(
		//'otland0',
		//'otland1',
		'znote'
	);

	// Built-in FORUM
	// Enable forum, enable guildboards, level to create threads/post in them
	// How long do they have to wait to create thread or post?
	// How to design/display hidden/closed/sticky threads.
	$config['forum'] = array(
		'enabled' => true,
		'guildboard' => true,
		'level' => 5,
		'cooldownPost' => 1,//60,
		'cooldownCreate' => 1,//180,
		'hidden' => '<font color="orange">[H]</font>',
		'closed' => '<font color="red">[C]</font>',
		'sticky' => '<font color="green">[S]</font>',
	);

	// Guilds and guild war pages will do lots of queries on bigger databases.
	// So its recommended to require login to view them, but you can disable this
	// If you don't have any problems with load.
	$config['require_login'] = array(
		'guilds' => false,
		'guildwars' => false,
	);

	// IMPORTANT! Write a character name(that exist) that will represent website bans!
	// Or remember to create character "God Website" character exist.
	// If you don't do this, bann from admin panel won't work properly.
	$config['website_char'] = 'Luxitur';

	//----------------\\
	// ADVANCED STUFF \\
	//----------------\\
	// Api config
	$config['api'] = array(
		'debug' => false,
	);
	// Use Znote's External Open Tibia Services Server
	// Currently in Alpha and is pretty useless, but will contain paypal blacklist etc in future.
	// You can use the official server: http://zeotss.znote.eu/
	// Or host your own private one, here is the code: https://github.com/Znote/ZEOTSS
	$config['zeotss'] = array(
		'enabled' => true,
		'visitors' => true,
		'server' => "http://zeotss.znote.eu/"
	);
	// Don't touch this unless you know what you are doing. (modifying this(key value) also requires modifications in OT files /XML/commands.xml).
	$config['ingame_positions'] = array(
		1 => 'Player',
		2 => 'Tutor',
		3 => 'Senior Tutor',
		4 => 'Gamemaster',
		5 => 'Community Manager',
		6 => 'God',
	);

	// Enable OS advanced feautures? false = no, true = yes
	$config['os_enabled'] = false;

	// What kind of computer are you hosting this website on?
	// Available options: LINUX or WINDOWS
	$config['os'] = 'WINDOWS';

	// Measure how much players are lagging in-game. (Not completed). 
	$config['ping'] = false;

	// BAN STUFF - Don't touch this unless you know what you are doing.
	// You can order the lines the way you want, from top to bot, in which order you
	// wish for them to be displayed in admin panel. Just make sure key[#] represent your describtion.
	$config['ban_type'] = array(
		4 => 'NOTATION_ACCOUNT',
		2 => 'NAMELOCK_PLAYER',
		3 => 'BAN_ACCOUNT',
		5 => 'DELETE_ACCOUNT',
		1 => 'BAN_IPADDRESS',
	);

	// BAN STUFF - Don't touch this unless you know what you are doing.
	// You can order the lines the way you want, from top to bot, in which order you
	// wish for them to be displayed in admin panel. Just make sure key[#] represent your describtion.
	$config['ban_action'] = array(
		0 => 'Notation',
		1 => 'Name Report',
		2 => 'Banishment',
		3 => 'Name Report + Banishment',
		4 => 'Banishment + Final Warning',
		5 => 'NR + Ban + FW',
		6 => 'Statement Report',
	);

	// Ban reasons, for changes beside default values to work with client,
	// you also need to edit sources (tools.cpp line 1096)
	$config['ban_reason'] = array(
		0 => 'Offensive Name',
		1 => 'Invalid Name Format',
		2 => 'Unsuitable Name',
		3 => 'Name Inciting Rule Violation',
		4 => 'Offensive Statement',
		5 => 'Spamming',
		6 => 'Illegal Advertising',
		7 => 'Off-Topic Public Statement',
		8 => 'Non-English Public Statement',
		9 => 'Inciting Rule Violation',
		10 => 'Bug Abuse',
		11 => 'Game Weakness Abuse',
		12 => 'Using Unofficial Software to Play',
		13 => 'Hacking',
		14 => 'Multi-Clienting',
		15 => 'Account Trading or Sharing',
		16 => 'Threatening Gamemaster',
		17 => 'Pretending to Have Influence on Rule Enforcement',
		18 => 'False Report to Gamemaster',
		19 => 'Destructive Behaviour',
		20 => 'Excessive Unjustified Player Killing',
		21 => 'Spoiling Auction',
	);

	// BAN STUFF
	// Ban time duration selection in admin panel
	// seconds => describtion
	$config['ban_time'] = array(
		3600 => '1 hour',
		21600 => '6 hours',
		43200 => '12 hours',
		86400 => '1 day',
		259200 => '3 days',
		604800 => '1 week',
		1209600 => '2 weeks',
		2592000 => '1 month',
	);


		// --------------- \\
		// SECURITY STUFF  \\
		// --------------- \\
	$config['use_token'] = false;
	$config['use_captcha'] = false;

	/*	Store visitor data
		Store visitor data in the database, logging every IP visitng site, 
		and how many times they have visited the site. And sometimes what
		they do on the site.
		
		This helps to prevent POST SPAM (like register 1000 accounts in a few seconds)
		and other things which can stress and slow down the server.
		
		The only downside is that database can get pretty fed up with much IP data
		if table never gets flushed once in a while. So I highly recommend you
		to configure flush_ip_logs if IPs are logged.
	*/

	$config['log_ip'] = false;

	// Flush IP logs each configured seconds, 60 * 15 = 15 minutes.
	// Set to false to entirely disable ip log flush. 
	// It is important to flush for optimal performance.
	$config['flush_ip_logs'] = 59 * 27;

	/*	IP SECURTY REQUIRE: $config['log_ip'] = true;
		Configure how tight this security shall be.
		Etc: You can max click on anything/refresh page
		[max activity] 15 times, within time period 10
		seconds. During time_period, you can also only
		register 1 account and 1 character.
	*/
	$config['ip_security'] = array(
		'time_period' => 10, // In seconds
		'max_activity' => 10, // page clicks/visits
		'max_post' => 6, // register, create, highscore, character search such actions
		'max_account' => 1, // register
		'max_character' => 1, // create char
		'max_forum_post' => 1, // Create threads and post in forum
	);

	//////////////
	/// PAYPAL ///
	//////////////

	// Write your paypal address here, and what currency you want to recieve money in.
	$config['paypal'] = array(
		'enabled' => true,
		'email' => 'stefan_brannfjell@live.no', // Example: paypal@mail.com
		'currency' => 'EUR',
		'points_per_currency' => 10, // 1 currency = ? points? [ONLY used to calculate bonuses]
		'success' => "http://".$_SERVER['HTTP_HOST']."/success.php",
		'failed' => "http://".$_SERVER['HTTP_HOST']."/failed.php",
		'ipn' => "http://".$_SERVER['HTTP_HOST']."/ipn.php",
		'showBonus' => true,
	);

	// Configure the "buy now" buttons prices, first write price, then how many points you get.
	// Giving some bonus points for higher donations will tempt users to donate more.
	$config['paypal_prices'] = array(
	//	price => points,
		1 => 45, // -10% bonus
		10 => 100, // 0% bonus
		15 => 165, // +10% bonus
		20 => 240, // +20% bonus
		25 => 325, // +30% bonus
		30 => 420, // +40% bonus
	);

	//////////////////
	/// PAYGOL SMS ///
	//////////////////
	// !!! Paygol takes 60%~ of the money, and send aprox 40% to your paypal.
	// You can configure paygol to send each month, then they will send money 
	// to you 1 month after recieving 50+ eur.
	$config['paygol'] = array(
		'enabled' => true,
		'serviceID' => 86648,// Service ID from paygol.com
		'currency' => 'SEK',
		'price' => 20,
		'points' => 20, // Remember to write same details in paygol.com!
		'name' => '20 points',
		'returnURL' => "http://".$_SERVER['HTTP_HOST']."/success.php",
		'cancelURL' => "http://".$_SERVER['HTTP_HOST']."/failed.php"
	);

	////////////
	/// SHOP ///
	////////////
	// If useDB is set to true, player can shop in-game as well using Znote LUA shop system plugin.
	$config['shop'] = array(
		'enabled' => true,
		'enableShopConfirmation' => true, // Verify that user wants to buy with popup
		'useDB' => false, // Fetch offers from database, or the below config array
		'showImage' => true,
		'imageServer' => 'items.znote.eu',
		'imageType' => 'gif',
	);

	//////////
	/// Let players sell characters.
	/////////
	$config['shop_auction'] = array(
			'characterAuction' => true, // Enable/disable this system
			'requiredLevel' => 50, // Minimum level of sold character
			'leastValue' => 10, // Lowest donation points a char can be sold for.
			'leastTime' => 24, // In hours. False to disable.
			// leastTime = Lowest duration of time an auctioned player has to be 
			// sellable before auctioneer can claim character back.
		);

	// If useDB is false, this array list will be used for shop offers.
	$config['shop_offers'] = array(
		// offer 1
		1 => array(
			'type' => 1, // 1 = item id offers, 2 = premium days [itemid ignored], 3 = sex change[itemid & count ignored], 4+ = custom.
			'itemid' => 2160, // item to get in-game
			'count' => 5, //if type is 2, this represents premium days
			'describtion' => "Crystal coin.", // Describtion shown on website
			'points' => 100, // How many points this offer costs
		),

		// offer 2
		2 => array(
			'type' => 1,
			'itemid' => 2392,
			'count' => 1,
			'describtion' => "Fire sword.",
			'points' => 10,
		),

		// offer 3
		3 => array(
			'type' => 2,
			'itemid' => 12466, // Item to display on page
			'count' => 7,
			'describtion' => "Premium membership.",
			'points' => 25,
		),

		// offer 4
		4 => array(
			'type' => 3,
			'itemid' => 12666,
			'count' => 3,
			'describtion' => "Change character gender.",
			'points' => 10,
		),
		5 => array(
			'type' => 3,
			'itemid' => 12666,
			'count' => 0,
			'describtion' => "Change character gender.",
			'points' => 20,
		),
		5 => array(
			'type' => 4,
			'itemid' => 12666,
			'count' => 1,
			'describtion' => "Change character name.",
			'points' => 20,
		),
	);
?>

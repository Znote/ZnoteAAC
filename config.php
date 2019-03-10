<?php
	if (!defined('ZNOTE_OS')) {
		$isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
		define('ZNOTE_OS', ($isWindows) ? 'WINDOWS' : 'LINUX');
	}
	
	// If you want to use items.php (not 100% yet, I guess)
	// Tested with TFS items.xml master (1.3)
	$config['items'] = false;
	
	// Available options: TFS_02, TFS_03, OTHIRE
	// OTHire = OTHIRE
	// TFS 0.2 = TFS_02
	// TFS 0.3 = TFS_03 (If ur using 0.3.6, set $config['salt'] to false)!
	// TFS 0.4 = TFS_03
	// TFS 1.0 = TFS_10 (Under developement)
	$config['ServerEngine'] = 'TFS_10';
	// As far as I know, OTX is based on TFS_03, so make sure TFS version is configured TFS_03
	$config['CustomVersion'] = false;

	$config['site_title'] = 'Znote AAC';
	$config['site_title_context'] = 'Because open communities are good communities. :3';
	$config['site_url'] = "http://demo.znote.eu";

	// Path to server folder without / Example: C:\Users\Alvaro\Documents\GitHub\forgottenserver
	$config['server_path'] = ''; 

	// ------------------------ \\
	// MYSQL CONNECTION DETAILS \\
	// ------------------------ \\

	if ($config['ServerEngine'] !== 'OTHIRE') {

	// TFS DATABASE CONFIGS
	// phpmyadmin username for OT server: (DONT USE "root" if ur hosting to public.).
	$config['sqlUser'] = 'tfs13';

	// phpmyadmin password for OT server:
	$config['sqlPassword'] = 'tfs13';

	// The database name to connect to. (This is usually same as username).
	$config['sqlDatabase'] = 'tfs13';

	// Hostname is usually localhost or 127.0.0.1.
	$config['sqlHost'] = '127.0.0.1';
	} else {

	// OTHIRE DATABASE CONFIG
	// phpmyadmin username for OT server: (DONT USE "root" if ur hosting to public.).
	$config['sql_user'] = 'tfs13';

	// phpmyadmin password for OT server:
	$config['sql_pass'] = 'tfs13';

	// The database name to connect to. (This is usually same as username).
	$config['sql_db'] = 'tfs13';

	// Hostname is usually localhost or 127.0.0.1.
	$config['sql_host'] = '127.0.0.1';
	}

	// QR code authenticator Only works with TFS 1.2+
	$config['twoFactorAuthenticator'] = false;
	// You can use the mobile phone app "authy" with this.

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
	$config['quests'] = array(
		array(1501,100,"Killing in the Name of",0),
		array(1502,150,"Killing in the Name of",0),
		array(65001,100,"Killing in the Name of",0),
		array(65002,150,"Killing in the Name of",0),
		array(65003,300,"Killing in the Name of",0),
		array(65004,3,"Killing in the Name of",0),
		array(65005,300,"Killing in the Name of",0),
		array(65006,150,"Killing in the Name of",0),
		array(65007,200,"Killing in the Name of",0),
		array(65008,300,"Killing in the Name of",0),
		array(65009,300,"Killing in the Name of",0),
		array(65010,300,"Killing in the Name of",0),
		array(65011,300,"Killing in the Name of",0),
		array(65012,300,"Killing in the Name of",0),
		array(65013,300,"Killing in the Name of",0),
		array(65014,300,"Killing in the Name of",1),
		array(12110,2,"The Inquisition",0),
		array(12111,7,"The Inquisition",0),
		array(12112,3,"The Inquisition",0),
		array(12113,6,"The Inquisition",0),
		array(12114,3,"The Inquisition",0),
		array(12115,3,"The Inquisition",0),
		array(12116,3,"The Inquisition",0),
		array(12117,5,"The Inquisition",1),
		array(330,3,"Sam's Old Backpack",1),
		array(12121,3,"The Ape City",0),
		array(12122,5,"The Ape City",0),
		array(12123,3,"The Ape City",0),
		array(12124,3,"The Ape City",0),
		array(12125,3,"The Ape City",0),
		array(12126,3,"The Ape City",0),
		array(12127,4,"The Ape City",0),
		array(12128,3,"The Ape City",0),
		array(12129,3,"The Ape City",1),
		array(12101,1,"The Ancient Tombs",0),
		array(12102,1,"The Ancient Tombs",0),
		array(12103,1,"The Ancient Tombs",0),
		array(12104,1,"The Ancient Tombs",0),
		array(12105,1,"The Ancient Tombs",0),
		array(12106,1,"The Ancient Tombs",0),
		array(12107,1,"The Ancient Tombs",1),
		array(12022,3,"Barbarian Test Quest",0),
		array(12022,3,"Barbarian Test Quest",0),
		array(12022,3,"Barbarian Test Quest",1),
		array(12025,3,"The Ice Islands Quest",0),
		array(12026,5,"The Ice Islands Quest",0),
		array(12027,3,"The Ice Islands Quest",0),
		array(12028,2,"The Ice Islands Quest",0),
		array(12029,6,"The Ice Islands Quest",0),
		array(12030,8,"The Ice Islands Quest",0),
		array(12031,3,"The Ice Islands Quest",0),
		array(12032,4,"The Ice Islands Quest",0),
		array(12033,2,"The Ice Islands Quest",0),
		array(12034,2,"The Ice Islands Quest",0),
		array(12035,2,"The Ice Islands Quest",0),
		array(12036,6,"The Ice Islands Quest",1),
	);

	//Achivements based on "https://github.com/PrinterLUA/FORGOTTENSERVER-ORTS/blob/master/data/lib/achievements_lib.lua"  (TFS 1.0)
	$config['Ach'] = false;
	$config['achievements'] = array(
		35000 => array(
			'First Dragon', //name
			'Rumours say that you will never forget your first Dragon', //comment
			'points' => '1', //points
			'img' => 'http://www.tibia-wiki.net/images/Dragon.gif', //img link or folder (example)> 'images/dragon.png'
		),
		35001 => array(
			'Uniwheel',
			'You\'re probably one of the very few people with this classic and unique ride, hope it doesn\'t break anytime soon.', //comment
			'points' => '1', //points
			'img' => 'http://img1.wikia.nocookie.net/__cb20140214234600/tibia/en/images/e/e5/Uniwheel.gif', //img link or folder (example)> 'images/dragon.png'
			'secret' => true
		),
		30001 => array(
			'Allow Cookies?', 
			'With a perfectly harmless smile you fooled all of those wicecrackers into eating your exploding cookies. Consider a boy or girl scout outfit next time to make the trick even better.', 
			'points' => '10', // 1-3 points (1star), 4-6 points(2 stars), 7-9 points(3 stars), 10 points => (4 stars)
			'secret' => true // show "secret" image
		),
		30002 => array(
			'Backpack Tourist',
			'If someone lost a random thing in a random place, you\'re probably a good person to ask and go find it, even if you don\'t know what and where.',
			'points' => '7'
		),
		30003 => array(
			'Bearhugger',
			'Warm, furry and cuddly - though that same bear you just hugged would probably rip you into pieces if he had been conscious, he reminded you of that old teddy bear which always slept in your bed when you were still small.', 
			'points' => '4'
		),
		30004 => array(
			'Bone Brother',
			'You\'ve joined the undead bone brothers - making death your enemy and your weapon as well. Devouring what\'s weak and leaving space for what\'s strong is your primary goal.',
			'points' => '1'
		),
		30005 => array(
			'Chorister',
			'Lalalala... you now know the cult\'s hymn sung in Liberty Bay by heart. Not that hard, considering that it mainly consists of two notes and repetitive lyrics.', 
			'points' => '1'
		),
		30006 => array(
			'Fountain of Life',
			'You found and took a sip from the Fountain of Life. Thought it didn\'t grant you eternal life, you feel changed and somehow at peace.', 
			'points' => '1',
			'secret' => true
		),
		30007 => array(
			'Here, Fishy Fishy!',
			'Ah, the smell of the sea! Standing at the shore and casting a line is one of your favourite activities. For you, fishing is relaxing - and at the same time, providing easy food. Perfect!', 
			'points' => '1'
		),
		30008 => array(
			'Honorary Barbarian',
			'You\'ve hugged bears, pushed mammoths and proved your drinking skills. And even though you have a slight hangover, a partially fractured rib and some greasy hair on your tongue, you\'re quite proud to call yourself a honorary barbarian from now on.', 
			'points' => '1'
		),
		30009 => array(
			'Huntsman',
			'You\'re familiar with hunting tasks and have carried out quite a few already. A bright career as hunter for the Paw & Fur society lies ahead!',
			'points' => '2'
		),
		300010 => array(
			'Just in Time',
			'You\'re a fast runner and are good at delivering wares which are bound to decay just in the nick of time, even if you can\'t use any means of transportation or if your hands get cold or smelly in the process.', 
			'points' => '1'
		),
		30011 => array(
			'Matchmaker',
			'You don\'t believe in romance to be a coincidence or in love at first sight. In fact - love potions, bouquets of flowers and cheesy poems do the trick much better than ever could. Keep those hormones flowing!', 
			'points' => '1',
			'secret' => true
		),
		30012 => array(
			'Nightmare Knight',
			'You follow the path of dreams and that of responsibility without self-centered power. Free from greed and selfishness, you help others without expecting a reward.',
			'points' => '1',
			'secret' => true
		),
		30013 => array(
			'Party Animal',
			'Oh my god, it\'s a paaaaaaaaaaaarty! You\'re always in for fun, friends and booze and love being the center of attention. There\'s endless reasons to celebrate! Woohoo!',
			'points' => '1',
			'secret' => true
		),
		30014 => array(
			'Secret Agent',
			'Pack your spy gear and get ready for some dangerous missions in service of a secret agency. You\'ve shown you want to - but can you really do it? Time will tell.', 
			'points' => '1',
			'secret' => true
		),
		30015 => array(
			'Talented Dancer',
			'You\'re a lord or lady of the dance - and not afraid to use your skills to impress tribal gods. One step to the left, one jump to the right, twist and shout!',
			'points' => '1'
		),
		30016 => array(
			'Territorial',
			'Your map is your friend - always in your back pocket and covered with countless marks of interesting and useful locations. One could say that you might be lost without it - but luckily there\'s no way to take it from you.',
			'points' => '1'
		),
		30017 => array(
			'Worm Whacker',
			'Weehee! Whack those worms! You sure know how to handle a big hammer.', 
			'points' => '1',
			'secret' => true
		),
		30018 => array(
			'Allowance Collector',
			'You certainly have your ways when it comes to acquiring money. Many of them are pink and paved with broken fragments of porcelain.',
			'points' => '1'
		),
		30019 => array(
			'Amateur Actor',
			'You helped bringing Princess Buttercup, Doctor Dumbness and Lucky the Wonder Dog to life - and will probably dream of them tonight, since you memorised your lines perfectly. What a .. special piece of.. screenplay.', 
			'points' => '2'
		),
		30020 => array(
			'Animal Activist',
			'Phasellus lacinia odio dolor, in elementum mauris dapibus a. Vivamus nec gravida libero, ac pretium eros. Nam in dictum ealesuada sodales. Nullam eget ex sit amet urna fringilla molestie. Aliquam lobortis urna eros, vel elementum metus accumsan eu. Nulla porttitor in lacus vel ullamcorper.',
			'points' => '2',
			'secret' => true
		),
	);

	// TFS 1.0 powergamers and top online
	//Before enabling powergamers, make sure that you have added LUA files and possible cloums to your server.
	//files can be found at Lua folder.
	
	$config['powergamers'] = array(
		'enabled' => true, // Enable or disable page
		'limit' => 20, //Number of players that it will show.
	);

	$config['toponline'] = array(
		'enabled' => true, // Enable or disable page
		'limit' => 20, //Number of players that it will show.
	);

	// Vocation IDs, names and which vocation ID they got promoted from
	$config['vocations'] = array(
		0 => array( 
			'name' => 'No vocation',
			'fromVoc' => false
		),
		1 => array( 
			'name' => 'Sorcerer',
			'fromVoc' => false
		),
		2 => array( 
			'name' => 'Druid',
			'fromVoc' => false
		),
		3 => array( 
			'name' => 'Paladin',
			'fromVoc' => false
		),
		4 => array( 
			'name' => 'Knight',
			'fromVoc' => false
		),
		5 => array( 
			'name' => 'Master Sorcerer',
			'fromVoc' => 1
		),
		6 => array( 
			'name' => 'Elder Druid',
			'fromVoc' => 2
		),
		7 => array( 
			'name' => 'Royal Paladin',
			'fromVoc' => 3
		),
		8 => array( 
			'name' => 'Elite Knight',
			'fromVoc' => 4
		)
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
		1 => 'Venore',
		2 => 'Thais',
		3 => 'Kazordoon',
		4 => 'Carlin',
		5 => "Ab'Dendriel",
		6 => 'Rookgaard',
		7 => 'Liberty Bay',
		8 => 'Port Hope',
		9 => 'Ankrahmun',
		10 => 'Darashia',
		11 => 'Edron',
		12 => 'Svargrond',
		13 => 'Yalahar',
		14 => 'Farmine',
		28 => 'Gray Beach',
		29 => 'Roshamuul',
		30 => 'Rookgaard Tutorial Island',
		31 => 'Isle of Solitude',
		32 => 'Island Of Destiny',
		33 => 'Rathleton'
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
		0 => 'Pending',
		1 => 'Accepted',
		2 => 'Rejected',
		3 => 'Cancelled',
		4 => 'Ended by kill limit',
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

	// Available towns (specify town ids, etc: (1, 2, 3); to display 3 town options (town id 1, 2 and 3).
	$config['available_towns'] = array(1, 2, 4, 5);

	$config['player'] = array(
		'base' => array(
			'level' => 8,
			'health' => 185,
			'mana' => 40,
			'cap' => 470,
			'soul' => 100
		),
		// health, mana cap etc are calculated with $config['vocations_gain'] and 'base' values of $config['player']
		'create' => array(
			'level' => 8,
			'novocation' => array( // vocation id 0 (No vocation) special settings
				'level' => 1, // Level
				'forceTown' => true,
				'townId' => 30
			),
			'skills' => array( // See $config['vocations'] for proper vocation names of these IDs
				// No vocation
				0 => array(
					'magic' => 0,
					'fist' => 10,
					'club' => 10,
					'axe' => 10,
					'sword' => 10,
					'dist' => 10,
					'shield' => 10,
					'fishing' => 10,
				),
				// Sorcerer
				1 => array(
					'magic' => 0,
					'fist' => 10,
					'club' => 10,
					'axe' => 10,
					'sword' => 10,
					'dist' => 10,
					'shield' => 10,
					'fishing' => 10,
				),
				// Druid
				2 => array(
					'magic' => 0,
					'fist' => 10,
					'club' => 10,
					'axe' => 10,
					'sword' => 10,
					'dist' => 10,
					'shield' => 10,
					'fishing' => 10,
				),
				// Paladin
				3 => array(
					'magic' => 0,
					'fist' => 10,
					'club' => 10,
					'axe' => 10,
					'sword' => 10,
					'dist' => 10,
					'shield' => 10,
					'fishing' => 10,
				),
				// Knight
				4 => array(
					'magic' => 0,
					'fist' => 10,
					'club' => 10,
					'axe' => 10,
					'sword' => 10,
					'dist' => 10,
					'shield' => 10,
					'fishing' => 10,
				),
			),
			'male_outfit' => array(
				'id' => 128,
				'head' => 78,
				'body' => 68,
				'legs' => 58,
				'feet' => 76
			),
			'female_outfit' => array(
				'id' => 136,
				'head' => 78,
				'body' => 68,
				'legs' => 58,
				'feet' => 76
			)
		)
	);

	// Minimum allowed character name letters. Etc 4 letters: "Kåre".
	$config['minL'] = 3;
	// Maximum allowed character name letters. Etc 20 letters: "Bobkåreolesofiesberg"
	$config['maxL'] = 20;

	// Maximum allowed character name words. Etc 2 words = "Bob Kåre", 3 words: "Bob Arne Kåre" as max char name words.
	$config['maxW'] = 3;

	// -------------- \\
	// WEBSITE STUFF  \\
	// -------------- \\

	// News to be displayed per page
	$config['news_per_page'] = 5;

	// Enable or disable changelog ticker in news page.
	$config['UseChangelogTicker'] = true;

	// Highscore configuration
	$config['highscore'] = array(
		'rows' => 100,
		'rowsPerPage' => 20,
		'ignoreGroupId' => 2, // Ignore this and higher group ids (staff)
	);

	// ONLY FOR TFS 0.2 (TFS 0.3/4 users don't need to care about this, as its fully loaded from db)
	$config['house'] = array(
		'house_file' => 'C:\test\Mystic Spirit_0.2.5\data\world\forgotten-house.xml',
		'price_sqm' => '50', // price per house sqm
	);

	$config['delete_character_interval'] = '3 DAY'; // Delay after user character delete request is executed eg. 1 DAY, 2 HOUR, 3 MONTH etc. 

	$config['validate_IP'] = false;
	$config['salt'] = false;

	// Restricted names
	$config['invalidNameTags'] = array(
		"owner", "gamemaster", "hoster", "admin", "staff", "tibia", "account", "god", "anal", "ass", "fuck", "sex", "hitler", "pussy", "dick", "rape", "cm", "gm", "amazon", "valkyrie", "carrion worm", "rotworm", "rotworm queen", "cockroach", "kongra", "merlkin", "sibang", "crystal spider", "giant spider", "poison spider", "scorpion", "spider", "tarantula", "achad", "axeitus headbanger", "bloodpaw", "bovinus", "colerian the barbarian", "cursed gladiator", "frostfur", "orcus the cruel", "rocky", "the hairy one", "avalanche", "drasilla", "grimgor guteater", "kreebosh the exile", "slim", "spirit of earth", "spirit of fire", "spirit of water", "the dark dancer", "the hag", "darakan the executioner", "deathbringer", "fallen mooh'tah master ghar", "gnorre chyllson", "norgle glacierbeard", "svoren the mad", "the masked marauder", "the obliverator", "the pit lord", "webster", "barbarian bloodwalker", "barbarian brutetamer", "barbarian headsplitter", "barbarian skullhunter", "bear", "panda", "polar bear", "braindeath", "beholder", "elder beholder", "gazer", "chicken", "dire penguin", "flamingo", "parrot", "penguin", "seagull", "terror bird", "bazir", "infernatil", "thul", "munster", "son of verminor", "xenia", "zoralurk", "big boss trolliver", "foreman kneebiter", "mad technomancer", "man in the cave", "lord of the elements", "the count", "the plasmother", "dracola", "the abomination", "the handmaiden", "mr. punish", "the countess sorrow", "the imperor", "massacre", "apocalypse", "brutus bloodbeard", "deadeye devious", "demodras", "dharalion", "fernfang", "ferumbras", "general murius", "ghazbaran", "grorlam", "lethal lissy", "morgaroth", "necropharus", "orshabaal", "ron the ripper", "the evil eye", "the horned fox", "the old widow", "tiquandas revenge", "apprentice sheng", "dog", "hellhound", "war wolf", "winter wolf", "wolf", "chakoya toolshaper", "chakoya tribewarden", "chakoya windcaller", "blood crab", "crab", "frost giant", "frost giantess", "ice golem", "yeti", "acolyte of the cult", "adept of the cult", "enlightened of the cult", "novice of the cult", "ungreez", "dark torturer", "demon", "destroyer", "diabolic imp", "fire devil", "fury", "hand of cursed fate", "juggernaut", "nightmare", "plaguesmith", "blue djinn", "efreet", "admin", "green djinn", "marid", "frost dragon", "wyrm", "sea serpent", "dragon lord", "dragon", "hydra", "dragon hatchling", "dragon lord hatchling", "frost dragon hatchling", "dwarf geomancer", "dwarf guard", "dwarf soldier", "dwarf", "dworc fleshhunter", "dworc venomsniper", "dworc voodoomaster", "elephant", "mammoth", "elf arcanist", "elf scout", "elf", "charged energy elemental", "energy elemental", "massive energy elemental", "overcharged energy elemental", "energy overlord", "cat", "lion", "tiger", "azure frog", "coral frog", "crimson frog", "green frog", "orchid frog", "toad", "jagged earth elemental", "muddy earth elemental", "earth elemental", "massive earth elemental", "earth overlord", "gargoyle", "stone golem", "ghost", "phantasm", "phantasm", "pirate ghost", "spectre", "cyclops smith", "cyclops drone", "behemoth", "cyclops", "slick water elemental", "roaring water elemental", "ice overlord", "water elemental", "massive water elemental", "ancient scarab", "butterfly", "bug", "centipede", "exp bug", "larva", "scarab", "wasp", "lizard sentinel", "lizard snakecharmer", "lizard templar", "minotaur archer", "minotaur guard", "minotaur mage", "minotaur", "squirrel", "goblin demon", "badger", "bat", "deer", "the halloween hare", "hyaena", "pig", "rabbit", "silver rabbit", "skunk", "wisp", "dark monk", "monk", "tha exp carrier", "necromancer", "priestess", "orc berserker", "orc leader", "orc rider", "orc shaman", "orc spearman", "orc warlord", "orc warrior", "orc", "goblin leader", "goblin scavenger", "goblin", "goblin assassin", "assasin", "bandit", "black knight", "hero", "hunter", "nomad", "smuggler", "stalker", "poacher", "wild warrior", "ashmunrah", "dipthrah", "mahrdis", "morguthis", "omruc", "rahemos", "thalas", "vashresamun", "pirate buccaneer", "pirate corsair", "pirate cutthroat", "pirate marauder", "carniphila", "spit nettle", "fire overlord", "massive fire elemental", "blistering fire elemental", "blazing fire elemental", "fire elemental", "hellfire fighter", "quara constrictor scout", "quara hydromancer scout", "quara mantassin scout", "quara pincher scout", "quara predator scout", "quara constrictor", "quara hydromancer", "quara mantassin", "quara pincher", "quara predator", "cave rat", "rat", "cobra", "crocodile", "serpent spawn", "snake", "wyvern", "black sheep", "sheep", "mimic", "betrayed wraith", "bonebeast", "demon skeleton", "lost soul", "pirate skeleton", "skeleton", "skeleton warrior", "undead dragon", "defiler", "slime2", "slime", "bog raider", "ice witch", "warlock", "witch", "bones", "fluffy", "grynch clan goblin", "hacker", "minishabaal", "primitive", "tibia bug", "undead minion", "annihilon", "hellgorak", "latrivan", "madareth", "zugurosh", "ushuriel", "golgordan", "thornback tortoise", "tortoise", "eye of the seven", "deathslicer", "flamethrower", "magicthrower", "plaguethrower", "poisonthrower", "shredderthrower", "troll champion", "frost troll", "island troll", "swamp troll", "troll", "banshee", "blightwalker", "crypt shambler", "ghoul", "lich", "mummy", "vampire", "grim reaper", "frost dragon", "mercenary", "zathroth", "goshnar", "durin", "demora", "orc champion", "dracula", "alezzo", "prince almirith", "elf warlord", "magebomb", "nightmare scion"
	);

	// Use guild logo system
	$config['use_guild_logos'] = true;
	
	// Use country flags
	$config['country_flags'] = array(
		'enabled' => true,
		'highscores' => true,
		'onlinelist' => true,
		'characterprofile' => true,
		'server' => 'http://flag.znote.eu'
	);

	// Show outfits
	$config['show_outfits'] = array(
		'shop' => true,
		'highscores' => true,
		'characterprofile' => true,
		'onlinelist' => true,
		// Image server may be unreliable and only for test,
		// host yourself: https://otland.net/threads/item-images-10-92.242492/
		'imageServer' => 'http://outfit-images.ots.me/animatedOutfits1099/animoutfit.php'
	);

	// Level requirement to create guild? (Just set it to 1 to allow all levels).
	$config['create_guild_level'] = 8;

	// Change Gender can be purchased in shop, or perhaps you want to allow everyone to change gender for free?
	$config['free_sex_change'] = false;

	// Do you need to have premium account to create a guild?
	$config['guild_require_premium'] = true;

	$config['guildwar_enabled'] = false;

	// Use htaccess rewrite? (basically this makes website.com/username work instead of website.com/characterprofile.php?name=username
	// Linux users needs to enable mod_rewrite php extention to make it work properly, so set it to false if your lost and using Linux.
	$config['htwrite'] = true;

	// What client version and server port are you using on this OT?
	// Used for the Downloads page.
	$config['client'] = 1098; // 954 = client 9.54

	 // Download link to client.
	$config['client_download'] = 'http://clients.halfaway.net/windows.php?tibia='. $config['client'] .'';
	$config['client_download_linux'] = 'http://clients.halfaway.net/linux.php?tibia='. $config['client'] .'';

	$config['port'] = 7171; // Port number to connect to your OT.
	
	$config['status'] = array(
		'status_check' => false, //enable or disable status checker
		'status_ip' => '127.0.0.1',
		'status_port' => "7171",
	);

	// Gameserver info is used for client 11+ loginWebService
	$config['gameserver'] = array(
		'ip' => '127.0.0.1',
		'port' => 7172,
		'name' => 'OTXServer-Global' // Must be identical to config.lua (OT config file) server name.
	);

	// How often do you want highscores to update?
	$config['cache_lifespan'] = 5;//60 * 15; // 15 minutes.

	// WARNING! Account names written here will have admin access to web page!
	$config['page_admin_access'] = array(
		'firstaccountName',
		'secondaccountName',
	);

	// Built-in FORUM
	// Enable forum, enable guildboards, level to create threads/post in them
	// How long do they have to wait to create thread or post?
	// How to design/display hidden/closed/sticky threads.
	$config['forum'] = array(
		'enabled' => true,
		'outfit_avatars' => true, // Show character outfit as forum avatar?
		'player_position' => true, // Tutor, Community manager, God etc..?
		'guildboard' => true,
		'level' => 5,
		'cooldownPost' => 1,//60,
		'cooldownCreate' => 1,//180,
		'newPostsBumpThreads' => true,
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

	// Email Server configurations (SMTP)
	/*	Please consider using a released stable version of PHPMailer or you may run into issues.
		Download PHPMailer: https://github.com/PHPMailer/PHPMailer/releases
		Extract to Znote AAC directory (where this config.php file is located)
		Rename the folder to "PHPMailer". Then configure this with your SMTP mail settings from your email provider.
	*/
	$config['mailserver'] = array(
		'register' => false, // Send activation mail
		'accountRecovery' => false, // Recover username or password through mail
		'host' => "mailserver.znote.eu", // Outgoing mail server host.
		'securityType' => 'ssl', // ssl or tls
		'port' => 465, // SMTP port number - likely to be 465(ssl) or 587(tls)
		'email' => 'noreply@znote.eu', 
		'username' => 'noreply@znote.eu', // Likely the same as email
		'password' => 'emailpassword', // The password.
		'debug' => false, // Enable debugging if you have problems and are looking for errors.
		'fromName' => $config['site_title'],
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
	$config['os'] = ZNOTE_OS;

	// Measure how much players are lagging in-game. (Not completed). 
	$config['ping'] = false;

	// BAN STUFF - Don't touch this unless you know what you are doing.
	// You can order the lines the way you want, from top to bot, in which order you
	// wish for them to be displayed in admin panel. Just make sure key[#] represent your description.
	$config['ban_type'] = array(
		4 => 'NOTATION_ACCOUNT',
		2 => 'NAMELOCK_PLAYER',
		3 => 'BAN_ACCOUNT',
		5 => 'DELETE_ACCOUNT',
		1 => 'BAN_IPADDRESS',
	);

	// BAN STUFF - Don't touch this unless you know what you are doing.
	// You can order the lines the way you want, from top to bot, in which order you
	// wish for them to be displayed in admin panel. Just make sure key[#] represent your description.
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
	// seconds => description
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
	// Set up captcha keys on https://www.google.com/recaptcha/
	$config['use_captcha'] = false;
	$config['captcha_site_key'] = "Site key";
	$config['captcha_secret_key'] = "Secret key";
	$config['captcha_use_curl'] = false; // Set to false if you don't have cURL installed, otherwise set it to true

	// Session prefix, if you are hosting multiple sites, make the session name different to avoid conflict.
	$config['session_prefix'] = 'znote_';

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
		'enabled' => false,
		'email' => 'edit@me.com', // Example: paypal@mail.com
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

	/////////////////
	/// PAGSEGURO ///
	/////////////////
	// Write your pagseguro address here, and what currency you want to recieve money in.
	$config['pagseguro'] = array(
		'enabled' => false,
		'sandbox' => false,
		'email' => '', // Example: pagseguro@mail.com
		'token' => '',
		'currency' => 'BRL',
		'product_name' => '',
		'price' => 100, // 1 real
		'ipn' => "http://".$_SERVER['HTTP_HOST']."/pagseguro_ipn.php",
		'urls' => array(
			'www' => 'pagseguro.uol.com.br',
			'ws'  => 'ws.pagseguro.uol.com.br',
			'stc' => 'stc.pagseguro.uol.com.br'
		)
	);

	if ($config['pagseguro']['sandbox']) {
		$config['pagseguro']['urls'] = array_map(function ($item) {
			return str_replace('pagseguro', 'sandbox.pagseguro', $item);
		}, $config['pagseguro']['urls']);
	}

	//////////////////
	/// PAYGOL SMS ///
	//////////////////
	// !!! Paygol takes 60%~ of the money, and send aprox 40% to your paypal.
	// You can configure paygol to send each month, then they will send money
	// to you 1 month after recieving 50+ eur.
	$config['paygol'] = array(
		'enabled' => false,
		'serviceID' => 86648, // Service ID from paygol.com
		'secretKey' => 'xxxx-xxxx-xxxx-xxxx', // Secret key from paygol.com. Never share your secret key
		'currency' => 'SEK',
		'price' => 20,
		'points' => 20,
		'name' => '20 points',
		'returnURL' => "http://".$_SERVER['HTTP_HOST']."/success.php",
		'cancelURL' => "http://".$_SERVER['HTTP_HOST']."/failed.php"
	);

	////////////
	/// SHOP ///
	////////////
	// If useDB is set to true, player can shop in-game as well using Znote LUA shop system plugin.
	$config['shop'] = array(
		'enabled' => false,
		'loginToView' => false, // Do user need to login to see the shop offers?
		'enableShopConfirmation' => true, // Verify that user wants to buy with popup
		'useDB' => false, // Fetch offers from database, or the below config array
		'showImage' => true,
		'imageServer' => 'items.znote.eu',
		'imageType' => 'gif',
	);

	//////////
	/// Let players sell, buy and bid on characters.
	/// Creates a deeper shop economy, encourages players to spend more money in shop for points.
	/// Pay to win/progress mechanic, but also lets people who can barely afford points to gain it
	/// by leveling characters to sell. It can also discourages illegal/risky third-party account 
	/// services. Since players can buy officially & support the server, dodgy competitors have to sell for cheaper.
	/// Without admin interference this is organic to each individual community economy inflation.
	/////////
	$config['shop_auction'] = array(
		'characterAuction' => false, // Enable/disable this system
		// Account ID of the account that stores players in the auction.
		// Make sure storage account has a very secure password!
		'storage_account_id' => 5, // Separate secure account ID, not your GM.
		'step' => 5, // Minimum amount someone can raise a bid by
		'step_duration' => 1 * 60 * 60, // When bidding over someone else, extend bid period by 1 hour.
		'lowestLevel' => 20, // Minimum level of sold character
		'lowestPrice' => 10, // Lowest donation points a char can be sold for.
		'biddingDuration' => 1 * 24 * 60 * 60, // = 1 day, 0 to disable bidding
		'deposit' => 10 // Seller has to add 10=10% deposit to auction which he gets back later.
	);

	/*
		type 1 = items
		type 2 = Premium days
		type 3 = Change character gender
		type 4 = Change character name
		type 5 = Buy outfit (put outfit id as itemid), 
		(put addon id as count [0 = nothing, 1 = first addon, 2 = second addon, 3 = both addons])
		type 6 = Buy mount (put mount id as itemid)
		type 7+ = custom coded stuff
	*/
	$config['shop_offers'] = array(
		1 => array(
			'type' => 1,
			'itemid' => 2160, // item to get in-game
			'count' => 5, // Stack number (5x itemid)
			'description' => "Crystal coin", // Description shown on website
			'points' => 100, // How many points this offer costs
		),
		2 => array(
			'type' => 1,
			'itemid' => 2392,
			'count' => 1,
			'description' => "Fire sword",
			'points' => 10,
		),
		3 => array(
			'type' => 2,
			'itemid' => 12466, // Item to display on page
			'count' => 7, // Days of premium account
			'description' => "Premium membership",
			'points' => 25,
		),
		4 => array(
			'type' => 3,
			'itemid' => 12666, // Item to display on page
			'count' => 3,
			'description' => "Change character gender",
			'points' => 10,
		),
		5 => array(
			'type' => 3,
			'itemid' => 12666, // Item to display on page
			'count' => 0, // 0 = unlimited
			'description' => "Change character gender",
			'points' => 20,
		),
		6 => array(
			'type' => 4,
			'itemid' => 12666, // Item to display on page
			'count' => 1,
			'description' => "Change character name",
			'points' => 20,
		),
		7 => array(
			'type' => 5,
			'itemid' => 132, // Outfit ID
			'count' => 3, // Addon 0 = none, 1 = first, 2 = second, 3 = both
			'description' => "Nobleman with both addons",
			'points' => 20,
		),
		8 => array(
			'type' => 5,
			'itemid' => 140,
			'count' => 3,
			'description' => "Noblewoman with both addons",
			'points' => 20,
		),
		9 => array(
			'type' => 6,
			'itemid' => 32, // Mount ID
			'count' => 1,
			'description' => "Gnarlhound mount",
			'points' => 20,
		),
		10 => array(
			'type' => 6,
			'itemid' => 17,
			'count' => 1,
			'description' => "War horse",
			'points' => 20,
		),
	);

	//////////////////////////
	/// OTServers.eu voting
	//
	// Start by creating an account at OTServers.eu and add your server.
	// You can find your secret token by logging in on OTServers.eu and go to 'MY SERVER' then 'Encourage players to vote'.
	$config['otservers_eu_voting'] = [
		'enabled' => false,
		'simpleVoteUrl' => '', //This url is used if the player isn't logged in.
		'voteUrl' => 'https://api.otservers.eu/vote_link.php',
		'voteCheckUrl' => 'https://api.otservers.eu/vote_check.php',
		'secretToken' => '', //Enter your secret token. Do not share with anyone!
		'landingPage' => '/voting.php?action=reward', //The user will be redirected to this page after voting
		'points' => '1' //Amount of points to give as reward
	];

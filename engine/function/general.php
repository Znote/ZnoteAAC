<?php
// Fetch and sanitize POST and GET values
function getValue($post) {
	return (!empty($post)) ? sanitize($post) : false;
}

function SendGet($getArray, $location = 'error.php') {
	$string = "";
	$count = 0;
	foreach ($getArray as $getKey => $getValue) {
		if ($count > 0) $string .= '&';
		$string .= "{$getKey}={$getValue}";
	}
	header("Location: {$location}?{$string}");
	exit();
}

// Sweet error reporting
function data_dump($print = false, $var = false, $title = false) {
	if ($title !== false) echo "<pre><font color='red' size='5'>$title</font><br>";
	else echo '<pre>';
	if ($print !== false) {
		echo 'Print: - ';
		print_r($print);
		echo "<br>";
	}
	if ($var !== false) {
		echo 'Var_dump: - ';
		var_dump($var);
	}
	echo '</pre><br>';
}

function accountAccess($accountId, $TFS) {
	$accountId = (int)$accountId;
	$access = 0;

	// TFS 0.3/4
	$yourChars = mysql_select_multi("SELECT `name`, `group_id`, `account_id` FROM `players` WHERE `account_id`='$accountId';");
	if ($yourChars !== false) {
		foreach ($yourChars as $char) {
			if ($TFS === 'TFS_03') {
				if ($char['group_id'] > $access) $access = $char['group_id'];
			} else {
				if ($char['group_id'] > 1) {
					if ($access == 0) {
						$acc = mysql_select_single("SELECT `type` FROM `accounts` WHERE `id`='". $char['account_id'] ."' LIMIT 1;");
						$access = $acc['type'];
					}
				}
			}
		}
		if ($access == 0) $access++;
		return $access;
	} else return false;
	//
}
// Generate recovery key
function generate_recovery_key($lenght) {
	$lenght = (int)$lenght;
	$tmp = rand(1000, 9000);
	$tmp += time();
	$tmp = sha1($tmp);
	
	$results = '';
	for ($i = 0; $i < $lenght; $i++) $results = $results.''.$tmp[$i];
	
	return $results;
}

// Calculate discount
function calculate_discount($orig, $new) {
	$orig = (int)$orig;
	$new = (int)$new;
	
	$tmp = '';
	if ($new >= $orig) {
		if ($new != $orig) {
			$calc = ($new/$orig) - 1;
			$calc *= 100;
			$tmp = '+'. $calc .'%';
		} else $tmp = '0%';
	} else {
		$calc = 1 - ($new/$orig);
		$calc *= 100;
		$tmp = '-'. $calc .'%';
	}
	return $tmp;
}

// Proper URLs
function url($path = false) {
	$protocol = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://';
	$domain   = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : null);
	$folder   = dirname($_SERVER['SCRIPT_NAME']);

	return $protocol . $domain . $folder . '/' . $path;
}

// Get last cached
function getCache() {
	$results = mysql_select_single("SELECT `cached` FROM `znote`;");
	return ($results !== false) ? $results['cached'] : false;
}

function setCache($time) {
	$time = (int)$time;
	mysql_update("UPDATE `znote` set `cached`='$time'");
}

// Get visitor basic data
function znote_visitors_get_data() {
	return mysql_select_multi("SELECT `ip`, `value` FROM `znote_visitors`");
}

// Set visitor basic data
function znote_visitor_set_data($visitor_data) {
	$exist = false;
	$ip = ip2long(getIP());
	
	foreach ((array)$visitor_data as $row) {
		if ($ip == $row['ip']) {
			$exist = true;
			$value = $row['value'];
		}
	}
	
	if ($exist && isset($value)) {
		// Update the value
		$value++;
		mysql_update("UPDATE `znote_visitors` SET `value` = '$value' WHERE `ip` = '$ip'");
	} else {
		// Insert new row
		mysql_insert("INSERT INTO `znote_visitors` (`ip`, `value`) VALUES ('$ip', '1')");
	}
}

// Get visitor basic data
function znote_visitors_get_detailed_data($cache_time) {
	$period = (int)time() - (int)$cache_time;
	return mysql_select_multi("SELECT `ip`, `time`, `type`, `account_id` FROM `znote_visitors_details` WHERE `time` >= '$period' LIMIT 0, 50");
}

function znote_visitor_insert_detailed_data($type) {
	$type = (int)$type;
	/*
	type 0 = normal visits
	type 1 = register form
	type 2 = character creation
	type 3 = fetch highscores
	type 4 = search character
	*/
	$time = time();
	$ip = ip2long(getIP());
	if (user_logged_in()) {
		$acc = $_SESSION['user_id'];
		mysql_insert("INSERT INTO `znote_visitors_details` (`ip`, `time`, `type`, `account_id`) VALUES ('$ip', '$time', '$type', '$acc')");
	} else mysql_insert("INSERT INTO `znote_visitors_details` (`ip`, `time`, `type`, `account_id`) VALUES ('$ip', '$time', '$type', '0')");
}

function something () {
	// Make acc data compatible:
	$ip = ip2long(getIP());
}

// Secret token
function create_token() {
	echo 'Checking whether to create token or not<br />';
	#if (empty($_SESSION['token'])) {
		echo 'Creating token<br />';
		$token = sha1(uniqid(time(), true));
		$token2 = $token;
		var_dump($token, $token2);
		$_SESSION['token'] = $token2;
	#}
	
	echo "<input type=\"hidden\" name=\"token\" value=\"". $_SESSION['token'] ."\" />";
}
function reset_token() {
	echo 'Reseting token<br />';
	unset($_SESSION['token']);
}

// Time based functions
// 60 seconds to 1 minute
function second_to_minute($seconds) {
	return ($seconds / 60);
}

// 1 minute to 60 seconds
function minute_to_seconds($minutes) {
	return ($minutes * 60);
}

// 60 minutes to 1 hour
function minute_to_hour($minutes) {
	return ($minutes / 60);
}

// 1 hour to 60 minutes
function hour_to_minute($hours) {
	return ($hour * 60);
}

// seconds / 60 / 60 = hours.
function seconds_to_hours($seconds) {
	$minutes = second_to_minute($seconds);
	$hours = minute_to_hour($minutes);
	return $hours;
}

function remaining_seconds_to_clock($seconds) {
	return date("(H:i)",time() + $seconds);
}

// Returns false if name contains more than configured max words, returns name otherwise.
function validate_name($string) {
	//edit: make sure only one space separates words: 
	//(found this regex through a search and havent tested it)
	$string  = preg_replace("/\\s+/", " ", $string);

	//trim off beginning and end spaces;
	$string = trim($string);

	//get an array of the words
	$wordArray = explode(" ", $string);

	//get the word count
	$wordCount = sizeof($wordArray);

	//see if its too big
	if($wordCount > config('maxW')) {
		return false;
	} else {
		return $string;
	}
}

// Checks if an IPv4 address is valid
function validate_ip($ip) {
	$ipL = ip2long($ip);
	$ipR = long2ip($ipL);
	
	if ($ip === $ipR) {
		return true;
	} else {
		return false;
	}
}

// Fetch a config value. Etc config('vocations') will return vocation array from config.php.
function config($value) {
	global $config;
	return $config[$value];
}

// Some functions uses several configurations from config.php, so it sounds
// smarter to give them the whole array instead of calling the function all the time.
function fullConfig() {
	global $config;
	return $config;
}

// Capitalize Every Word In String.
function format_character_name($name) {
	return ucwords(strtolower($name));
}

// Returns a list of players online
function online_list() {
	if (config('TFSVersion') == 'TFS_10') return mysql_select_multi("SELECT `o`.`player_id` AS `id`, `p`.`name` as `name`, `p`.`level` as `level`, `p`.`vocation` as `vocation` FROM `players_online` as `o` INNER JOIN `players` as `p` ON o.player_id = p.id");
	else return mysql_select_multi("SELECT `name`, `level`, `vocation` FROM `players` WHERE `online`='1' ORDER BY `name` DESC;");
}

// Gets you the actual IP address even from users behind ISP proxies and so on.
function getIP() {
	/*
  $IP = '';
  if (getenv('HTTP_CLIENT_IP')) {
    $IP =getenv('HTTP_CLIENT_IP');
  } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
    $IP =getenv('HTTP_X_FORWARDED_FOR');
  } elseif (getenv('HTTP_X_FORWARDED')) {
    $IP =getenv('HTTP_X_FORWARDED');
  } elseif (getenv('HTTP_FORWARDED_FOR')) {
    $IP =getenv('HTTP_FORWARDED_FOR');
  } elseif (getenv('HTTP_FORWARDED')) {
    $IP = getenv('HTTP_FORWARDED');
  } else {
    $IP = $_SERVER['REMOTE_ADDR'];
  } */
return $_SERVER['REMOTE_ADDR'];
}

function array_length($ar) {
	$r = 1;
	foreach($ar as $a) {
		$r++;
	}
	return $r;
}
// Parameter: level, returns experience for that level from an experience table.
function level_to_experience($level) {
	
	// Generated experience table. Currently up to level 200.
	$experience = array (1 => 0, 2 => 100, 3 => 200, 4 => 400, 5 => 800, 6 => 1500, 7 => 2600, 8 => 4200, 9 => 6400, 10 => 9300, 11 => 13000, 12 => 17600, 13 => 23200, 14 => 29900, 15 => 37800, 16 => 47000, 17 => 57600, 18 => 69700, 19 => 83400, 20 => 98800, 21 => 116000, 22 => 135100, 23 => 156200, 24 => 179400, 25 => 204800, 26 => 232500, 27 => 262600, 28 => 295200, 29 => 330400, 30 => 368300, 31 => 409000, 32 => 452600, 33 => 499200, 34 => 548900, 35 => 601800, 36 => 658000, 37 => 717600, 38 => 780700, 39 => 847400, 40 => 917800, 41 => 992000, 42 => 1070100, 43 => 1152200, 44 => 1238400, 45 => 1328800, 46 => 1423500, 47 => 1522600, 48 => 1626200, 49 => 1734400, 50 => 1847300, 51 => 1965000, 52 => 2087600, 53 => 2215200, 54 => 2347900, 55 => 2485800, 56 => 2629000, 57 => 2777600, 58 => 2931700, 59 => 3091400, 60 => 3256800, 61 => 3428000, 62 => 3605100, 63 => 3788200, 64 => 3977400, 65 => 4172800, 66 => 4374500, 67 => 4582600, 68 => 4797200, 69 => 5018400, 70 => 5246300, 71 => 5481000, 72 => 5722600, 73 => 5971200, 74 => 6226900, 75 => 6489800, 76 => 6760000, 77 => 7037600, 78 => 7322700, 79 => 7615400, 80 => 7915800, 81 => 8224000, 82 => 8540100, 83 => 8864200, 84 => 9196400, 85 => 9536800, 86 => 9885500, 87 => 10242600, 88 => 10608200, 89 => 10982400, 90 => 11365300, 91 => 11757000, 92 => 12157600, 93 => 12567200, 94 => 12985900, 95 => 13413800, 96 => 13851000, 97 => 14297600, 98 => 14753700, 99 => 15219400, 100 => 15694800, 101 => 16180000, 102 => 16675100, 103 => 17180200, 104 => 17695400, 105 => 18220800, 106 => 18756500, 107 => 19302600, 108 => 19859200, 109 => 20426400, 110 => 21004300, 111 => 21593000, 112 => 22192600, 113 => 22803200, 114 => 23424900, 115 => 24057800, 116 => 24702000, 117 => 25357600, 118 => 26024700, 119 => 26703400, 120 => 27393800, 121 => 28096000, 122 => 28810100, 123 => 29536200, 124 => 30274400, 125 => 31024800, 126 => 31787500, 127 => 32562600, 128 => 33350200, 129 => 34150400, 130 => 34963300, 131 => 35789000, 132 => 36627600, 133 => 37479200, 134 => 38343900, 135 => 39221800, 136 => 40113000, 137 => 41017600, 138 => 41935700, 139 => 42867400, 140 => 43812800, 141 => 44772000, 142 => 45745100, 143 => 46732200, 144 => 47733400, 145 => 48748800, 146 => 49778500, 147 => 50822600, 148 => 51881200, 149 => 52954400, 150 => 54042300, 151 => 55145000, 152 => 56262600, 153 => 57395200, 154 => 58542900, 155 => 59705800, 156 => 60884000, 157 => 62077600, 158 => 63286700, 159 => 64511400, 160 => 65751800, 161 => 67008000, 162 => 68280100, 163 => 69568200, 164 => 70872400, 165 => 72192800, 166 => 73529500, 167 => 74882600, 168 => 76252200, 169 => 77638400, 170 => 79041300, 171 => 80461000, 172 => 81897600, 173 => 83351200, 174 => 84821900, 175 => 86309800, 176 => 87815000, 177 => 89337600, 178 => 90877700, 179 => 92435400, 180 => 94010800, 181 => 95604000, 182 => 97215100, 183 => 98844200, 184 => 100491400, 185 => 102156800, 186 => 103840500, 187 => 105542600, 188 => 107263200, 189 => 109002400, 190 => 110760300, 191 => 112537000, 192 => 114332600, 193 => 116147200, 194 => 117980900, 195 => 119833800, 196 => 121706000, 197 => 123597600, 198 => 125508700, 199 => 127439400, 200 => 129389800);
	
	return ($level > 0 && $level <= 200) ? $experience[$level] : false;
}

// Parameter: players.hide_char returns: Status word inside a font with class identifier so it can be designed later on by CSS.
function hide_char_to_name($id) {
	$id = (int)$id;
	if ($id == 1) {
		return 'hidden';
	} else {
		return 'visible';
	}
}

// Parameter: players.online returns: Status word inside a font with class identifier so it can be designed later on by CSS.
function online_id_to_name($id) {
	$id = (int)$id;
	if ($id == 1) {
		return '<font class="status_online">ONLINE</font>';
	} else {
		return '<font class="status_offline">offline</font>';
	}
}

// Parameter: players.vocation_id. Returns: Configured vocation name.
function vocation_id_to_name($id) {
	$vocations = config('vocations');

	return ($vocations[$id] >= 0) ? $vocations[$id] : false;
}

function gender_exist($gender) {
	// Range of allowed gender ids, fromid toid
	if ($gender >= 0 && $gender <= 1) {
		return true;
	} else {
		return false;
	}
}

function skillid_to_name($skillid) {
	$skillname = array(
		0 => 'fist fighting',
		1 => 'club fighting',
		2 => 'sword fighting',
		3 => 'axe fighting',
		4 => 'distance fighting',
		5 => 'shielding',
		6 => 'fishing',
		7 => 'experience', // Hardcoded, does not actually exist in database as a skillid.
		8 => 'magic level' // Hardcoded, does not actually exist in database as a skillid.
	);

	return ($skillname[$skillid] >= 0) ? $skillname[$skillid] : false;
}

// Parameter: players.town_id. Returns: Configured town name.
function town_id_to_name($id) {
	$towns = config('towns');
	
	return (array_key_exists($id, $towns)) ? $towns[$id] : 'Missing Town';
}

// Unless you have an internal mail server then mail sending will not be supported in this version.
function email($to, $subject, $body) {
	mail($to, $subject, $body, 'From: TEST');
}

function logged_in_redirect() {
	if (user_logged_in() === true) {
		header('Location: myaccount.php');
	}
}

function protect_page() {
	if (user_logged_in() === false) {
		header('Location: protected.php');
		exit();
	}
}

// When function is called, you will be redirected to protect_page and deny access to rest of page, as long as you are not admin.
function admin_only($user_data) {	
	// Chris way
	$gotAccess = is_admin($user_data);
	
	if ($gotAccess == false) {
		logged_in_redirect();
		exit();
	}
}

function is_admin($user_data) {
	return in_array($user_data['name'], config('page_admin_access')) ? true : false;
}

function array_sanitize(&$item) {
	$item = htmlentities(strip_tags(mysql_znote_escape_string($item)));
}

function sanitize($data) {
	return htmlentities(strip_tags(mysql_znote_escape_string($data)));
}

function output_errors($errors) {
	return '<ul><li>'. implode('</li><li>', $errors) .'</li></ul>';
}
?>
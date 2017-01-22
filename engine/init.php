<?php if (version_compare(phpversion(), '5.3.3', '<')) die('PHP version 5.3.3 or higher is required.');

$l_time = microtime();
$l_time = explode(' ', $l_time);
$l_time = $l_time[1] + $l_time[0];
$l_start = $l_time;

function elapsedTime($l_start = false, $l_time = false) {
	if ($l_start === false) global $l_start;
	if ($l_time === false) global $l_time;
	
	$l_time = explode(' ', microtime());
	$l_finish = $l_time[1] + $l_time[0];
	return round(($l_finish - $l_start), 4);
}

$time = time();
$version = '1.5_SVN';

$aacQueries = 0;
$accQueriesData = array();

session_start();
ob_start();
require_once 'config.php';
$sessionPrefix = $config['session_prefix'];
if ($config['paypal']['enabled'] || $config['use_captcha']) {
	$curlcheck = extension_loaded('curl');
	if (!$curlcheck) die("php cURL is not enabled. It is required to for paypal or captcha services.<br>1. Find your php.ini file.<br>2. Uncomment extension=php_curl<br>Restart web server.<br><br><b>If you don't want this then disable paypal & use_captcha in config.php.</b>");
}
if ($config['use_captcha'] && !extension_loaded('openssl')) {
	die("php openSSL is not enabled. It is required to for captcha services.<br>1. Find your php.ini file.<br>2. Uncomment extension=php_openssl<br>Restart web server.<br><br><b>If you don't want this then disable use_captcha in config.php.</b>");
}

require_once 'database/connect.php';
require_once 'function/general.php';
require_once 'function/users.php';
require_once 'function/cache.php';
require_once 'function/mail.php';
require_once 'function/token.php';
require_once 'function/itemparser/itemlistparser.php';

if (isset($_SESSION['token'])) {
	$_SESSION['old_token'] = $_SESSION['token'];
}
Token::generate();

if (user_logged_in() === true) {
	$session_user_id = getSession('user_id');
	$user_data = user_data($session_user_id, 'id', 'name', 'password', 'email', 'premdays');
	$user_znote_data = user_znote_account_data($session_user_id, 'ip', 'created', 'points', 'cooldown', 'flag');
}
$errors = array();
// Log IP
if ($config['log_ip']) {
	$visitor_config = $config['ip_security'];
	
	$flush = $config['flush_ip_logs'];
	if ($flush != false) {
		$timef = $time - $flush;
		if (getCache() < $timef) {
			$timef = $time - $visitor_config['time_period'];
			mysql_delete("DELETE FROM znote_visitors_details WHERE time <= '$timef'");
			setCache($time);
		}
	}
	
	$visitor_data = znote_visitors_get_data();
	
	znote_visitor_set_data($visitor_data); // update or insert data
	znote_visitor_insert_detailed_data(0); // detailed data
	
	$visitor_detailed = znote_visitors_get_detailed_data($visitor_config['time_period']);
	
	// max activity
	$v_activity = 0;
	$v_register = 0;
	$v_highscore = 0;
	$v_c_char = 0;
	$v_s_char = 0;
	$v_form = 0;
	foreach ((array)$visitor_detailed as $v_d) {
		// Activity
		if ($v_d['ip'] == getIPLong()) {
			// count each type of visit			
			switch ($v_d['type']) {
				case 0: // max activity
					$v_activity++;
				break;
				
				case 1: // account registered
					$v_register++;
					$v_form++;
				break;
				
				case 2: // character creations
					$v_c_char++;
					$v_form++;
				break;
				
				case 3: // Highscore fetched
					$v_highscore++;
					$v_form++;
				break;
				
				case 4: // character searched
					$v_s_char++;
					$v_form++;
				break;
				
				case 5: // Other forms (login.?)
					$v_form++;
				break;
			}
			
		}
	}
	
	// Deny access if activity is too high
	if ($v_activity > $visitor_config['max_activity']) die("Chill down. Your web activity is too big. max_activity");
	if ($v_register > $visitor_config['max_account']) die("Chill down. You can't create multiple accounts that fast. max_account");
	if ($v_c_char > $visitor_config['max_character']) die("Chill down. Your web activity is too big. max_character");
	if ($v_form > $visitor_config['max_post']) die("Chill down. Your web activity is too big. max_post");
	
	//var_dump($v_activity, $v_register, $v_highscore, $v_c_char, $v_s_char, $v_form);
	//echo ' <--- IP logging activity past 10 seconds.';
}

// Sub page override system
if ($config['allowSubPages']) {
	require_once 'layout/sub.php';
	$filename = explode('/', $_SERVER['PHP_SELF']);
	$filename = $filename[count($filename)-1];
	if (isset($subpages) && !empty($subpages)) {
		foreach ($subpages as $page) {
			if ($page['override'] && $page['file'] === $filename) {
				require_once 'layout/overall/header.php';
				require_once 'layout/sub/'.$page['file'];
				require_once 'layout/overall/footer.php';
				exit;
			}
		}
	} else {
		?>
		<div style="background-color: white; padding: 20px; width: 100%; float:left;">
			<h2 style="color: black;">Old layout!</h2>
			<p style="color: black;">The layout is running an outdated sub system which is not compatible with this version of Znote AAC.</p>
			<p style="color: black;">The file /layout/sub.php is outdated.
			<br>Please update it to look like <a style="color: orange;" target="_BLANK" href="https://github.com/Znote/ZnoteAAC/blob/master/layout/sub.php">THIS.</a>
			</p>
		</div>
		<?php
	}
}
?>
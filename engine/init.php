<?php
// Verify the PHP version, gives tutorial if fail.
if (version_compare(phpversion(), '5.3.3', '<')) die('PHP 5.3.3 is required<br><br>WINDOWS:<br>Download and use the latest Uniform Server.<br><a href="http://www.uniformserver.com/">CLICK ME</a> to get to their website. <br> XAMPP sucks and is insecure. Kthxbye.<br><br>LINUX DEBIAN:<br>Edit /etc/apt/sources.list<br>etc if you use nano text editor, make sure you are root and do<br>nano /etc/apt/sources.list<br><br>At the bottom, add this:<br><br>deb http://packages.dotdeb.org stable all<br>deb-src http://packages.dotdeb.org stable all<br><br>save file. <br><br>Then in terminal, do these 2 commands:<br>gpg --keyserver keys.gnupg.net --recv-key 89DF5277<br><br>gpg -a --export 89DF5277 | sudo apt-key add -<br><br>And then do these 2 commands:<br><br>apt-get update<br>apt-get upgrade<br><br>You now have the latest stable PHP version.<br>');

$time = time();
$version = '1.5_SVN';

session_start();
ob_start();
require 'config.php';
require 'database/connect.php';
require 'function/general.php';
require 'function/users.php';
require 'function/cache.php';
require 'function/token.php';

if (isset($_SESSION['token'])) {
	$_SESSION['old_token'] = $_SESSION['token'];
	//var_dump($_SESSION['old_token'], $_SESSION['token']);
}
Token::generate();

if (user_logged_in() === true) {
	$session_user_id = $_SESSION['user_id'];
	$user_data = user_data($session_user_id, 'id', 'name', 'password', 'email');
	$user_znote_data = user_znote_account_data($session_user_id, 'ip', 'created', 'points', 'cooldown');
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
			mysql_query("DELETE FROM znote_visitors_details WHERE time <= '$timef'") or die(mysql_error());
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
		if ($v_d['ip'] == ip2long(getIP())) {
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
?>
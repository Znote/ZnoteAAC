<?php
$time = time();
if (!isset($version)) $version = '1.5_SVN';

if (!function_exists("elapsedTime")) {
	function elapsedTime($l_start = false, $l_time = false) {
		if ($l_start === false) global $l_start;
		if ($l_time === false) global $l_time;

		$l_time = explode(' ', microtime());
		$l_finish = $l_time[1] + $l_time[0];
		return round(($l_finish - $l_start), 4);
	}
}

// ALTER TABLE `znote_accounts` ADD `active_email` TINYINT(4) NOT NULL DEFAULT '0' AFTER `active`;

$install = "
<h2>Install:</h2>
<ol>
	<li>
		<p>
			Make sure you have imported TFS database. (OTdir/schema.sql OR OTdir/schemas/mysql.sql OR OTdir/forgottenserver.sql)
		</p>
	</li>
	<li>Import the <a href='/engine/database/znote_schema.sql'>Znote AAC schema</a> to a <b>TFS database in phpmyadmin</b>.</li>
	<li>
		<p>
			Edit config.php with correct mysql connection details.
		</p>
	</li>
</ol>
";

$connect = new mysqli($config['sqlHost'], $config['sqlUser'], $config['sqlPassword'], $config['sqlDatabase']);

if ($connect->connect_errno) {
	die("Failed to connect to MySQL: (" . $connect->connect_errno . ") " . $connect->connect_error . $install);
}

function mysql_znote_escape_string($escapestr) {
	global $connect;
	return mysqli_real_escape_string($connect, $escapestr);
}

// Select single row from database
function mysql_select_single($query) {
	global $connect;
	global $aacQueries;
	$aacQueries++;

	global $accQueriesData;
	$accQueriesData[] = "[" . elapsedTime() . "] " . $query;
	$result = mysqli_query($connect,$query) or die(var_dump($query)."<br>(query - <font color='red'>SQL error</font>) <br>Type: <b>select_single</b> (select single row from database)<br><br>".mysqli_error($connect));
	$row = mysqli_fetch_assoc($result);
	return !empty($row) ? $row : false;
}

// Selecting multiple rows from database.
function mysql_select_multi($query){
	global $connect;
	global $aacQueries;
	$aacQueries++;
	global $accQueriesData;
	$accQueriesData[] = "[" . elapsedTime() . "] " . $query;
	$array = array();
	$results = mysqli_query($connect,$query) or die(var_dump($query)."<br>(query - <font color='red'>SQL error</font>) <br>Type: <b>select_multi</b> (select multiple rows from database)<br><br>".mysqli_error($connect));
	while($row = mysqli_fetch_assoc($results)) {
		$array[] = $row;
	}
	return !empty($array) ? $array : false;
}

//////
// Query database without expecting returned results

// - mysql update
function mysql_update($query){ voidQuery($query); }
// mysql insert
function mysql_insert($query){ voidQuery($query); }
// mysql delete
function mysql_delete($query){ voidQuery($query); }
// Send a void query
function voidQuery($query) {
	global $connect;
	global $aacQueries;
	$aacQueries++;
	global $accQueriesData;
	$accQueriesData[] = "[" . elapsedTime() . "] " . $query;
	mysqli_query($connect,$query) or die(var_dump($query)."<br>(query - <font color='red'>SQL error</font>) <br>Type: <b>voidQuery</b> (voidQuery is used for update, insert or delete from database)<br><br>".mysqli_error($connect));
}
?>

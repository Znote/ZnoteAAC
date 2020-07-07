<?php require_once 'engine/init.php'; include 'layout/overall/header.php';

	// Cache the results
	$cache = new Cache('engine/cache/topGuilds');
	if ($cache->hasExpired()) {
		$guilds = mysql_select_multi("SELECT `g`.`id` AS `id`, `g`.`name` AS `name`, COUNT(`g`.`name`) as `frags` FROM `players` p LEFT JOIN `player_deaths` pd ON `pd`.`killed_by` = `p`.`name` LEFT JOIN `guild_membership` gm ON `p`.`id` = `gm`.`player_id` LEFT JOIN `guilds` g ON `gm`.`guild_id` = `g`.`id` WHERE `pd`.`unjustified` = 1 GROUP BY `name` ORDER BY `frags` DESC, `name` ASC LIMIT 0, 10;");

		$cache->setContent($guilds);
		$cache->save();
	} else {
		$guilds = $cache->load();
	}
	$count = 1;

	function convert_number_to_words($number) {

	$hyphen = '-';
	$conjunction = ' and ';
	$separator = ', ';
	$negative= 'negative ';
	$decimal = ' point ';
	$dictionary = array(
		0					=> 'zero',
		1					=> 'first',
		2					=> 'second',
		3					=> 'third',
		4					=> 'fourth',
		5					=> 'fifth',
		6					=> 'sixth',
		7					=> 'seventh',
		8					=> 'eighth',
		9					=> 'ninth',
		10					=> 'tenth',
		11					=> 'eleventh',
		12					=> 'twelve',
		13					=> 'thirteen',
		14					=> 'fourteen',
		15					=> 'fifteen',
		16					=> 'sixteen',
		17					=> 'seventeen',
		18					=> 'eighteen',
		19					=> 'nineteen',
		20					=> 'twenty',
		30					=> 'thirty',
		40					=> 'fourty',
		50					=> 'fifty',
		60					=> 'sixty',
		70					=> 'seventy',
		80					=> 'eighty',
		90					=> 'ninety',
		100					=> 'hundred',
		1000				=> 'thousand',
		1000000				=> 'million',
		1000000000			=> 'billion',
		1000000000000		=> 'trillion',
		1000000000000000	=> 'quadrillion',
		1000000000000000000	=> 'quintillion'
	);

	if (!is_numeric($number)) {
		return false;
	}

	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		// overflow
		trigger_error(
			'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
			E_USER_WARNING
		);
		return false;
	}

	if ($number < 0) {
		return $negative . convert_number_to_words(abs($number));
	}

	$string = $fraction = null;

	if (strpos($number, '.') !== false) {
		list($number, $fraction) = explode('.', $number);
	}

	switch (true) {
		case $number < 21:
			$string = $dictionary[$number];
			break;
		case $number < 100:
			$tens   = ((int) ($number / 10)) * 10;
			$units  = $number % 10;
			$string = $dictionary[$tens];
			if ($units) {
				$string .= $hyphen . $dictionary[$units];
			}
			break;
		case $number < 1000:
			$hundreds  = $number / 100;
			$remainder = $number % 100;
			$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
			if ($remainder) {
				$string .= $conjunction . convert_number_to_words($remainder);
			}
			break;
		default:
			$baseUnit = pow(1000, floor(log($number, 1000)));
			$numBaseUnits = (int) ($number / $baseUnit);
			$remainder = $number % $baseUnit;
			$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
			if ($remainder) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= convert_number_to_words($remainder);
			}
			break;
	}

	if (null !== $fraction && is_numeric($fraction)) {
		$string .= $decimal;
		$words = array();
		foreach (str_split((string) $fraction) as $number) {
			$words[] = $dictionary[$number];
		}
		$string .= implode(' ', $words);
	}

	return $string;
}

if (!empty($guilds) && $guilds !== false) {
	?>
	<h3><center>Top 10 guilds with most frags</center></h3>
	<table id="onlinelistTable" class="table table-striped table-hover">
	    <tr class="yellow">
			<th>#</th>
	        <th>Name:</th>
	        <th>Frags:</th>
	    </tr>
	    <?php
		foreach ($guilds as $guild):
		    $url = url("guilds.php?name=". $guild['name']);
			?>
			<tr class="special" onclick="javascript:window.location.href='<?php echo $url; ?>'">
				<td><?php
					echo convert_number_to_words($count);
					$count++;
				?></td>
		        <td><a href="" onclick="return false"><?php echo $guild['name']; ?></a></td>
		        <td><?php echo $guild['frags']; ?></td>
		    </tr>
	    	<?php
		endforeach; ?>
	</table>
	<?php
} else {
	echo '<h1>No frags yet.</h1>';
}
include 'layout/overall/footer.php'; ?>

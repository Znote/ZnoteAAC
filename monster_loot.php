<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<?php 
###### MONSTER LOOT CHECKER ######
###### VERSION: 1.5

$otdir = 'misc/';

// In percent (highest first).
$rarity = array(
	'Not Rare'	=> 7,
	'Semi Rare'	=> 2,
	'Rare'		=> 0.5,
	'Very Rare'	=> 0
);
?>
<script language="javascript">
	function toggleVisibility(obj) {
		var el = document.getElementById('d' + obj.id);
		var name = obj.innerHTML.substring(4);

		if(el.style.display == 'none') {
			obj.innerHTML = '[ -]';
			el.style.display = 'block';
		} else {
			obj.innerHTML = '[+]';
			el.style.display = 'none';
		}
		obj.innerHTML += ' ' + name;
	}
</script>

<?php
	if(isset($_GET['lootrate']))
		$add = '&lootrate';
	echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF'] . ($add ? '?lootrate' : '')) . '">Hide None</a> | ';
	echo '<a href="?hidefail' . $add . '">Hide Not Found</a> | ';
	echo '<a href="?hideempty' . $add . '">Hide Monsters Without Loot</a> | ';
	echo '<a href="?hideempty&hidefail' . $add . '">Hide All</a> | ';
	echo '<a href="monsters_loot.php">Use Normal Loot Rate</a> | ';
	echo '<a href="?lootrate">Use Server Loot Rate</a>';
?>
<br><br>

<?php 
	$items = simplexml_load_file($otdir . '/data/items/items.xml') or die('<b>Could not load items!</b>');
	foreach($items->item as $v) 
		$itemList[(int)$v['id']] = $v['name']; 

	if(isset($_GET['lootrate'])) { 
		$config = parse_ini_file($otdir . '/config.lua');
		$lootRate = $config['rate_loot'];
	}

	$monsters = simplexml_load_file($otdir . '/data/monster/monsters.xml') or die('<b>Could not load monsters!</b>');
	foreach($monsters->monster as $monster) {
		$loot = simplexml_load_file($otdir . '/data/monster/' . $monster['file']);
		if($loot) {
			if($item = $loot->loot->item) {
				echo '
					<a id="' . ++$i . '" style="text-decoration: none; font: bold 14px verdana; color: orange;" href="javascript:void(0);" onclick="toggleVisibility(this)">[+] ' . $monster['name'] . '</a>
					<br><div style="display: none;" id="d' . $i . '"><br>';
				addLoot($item);
				echo '<br></pre></div>';
			} elseif(!isset($_GET['hideempty']))
				echo '<span style="font: bold 14px verdana; color: red;">[x] ' . $monster['name'] . '</span><br>';
		} elseif(!isset($_GET['hidefail']))
			echo '<span style="color: white;">Failed to load monster <b>' . $monster[name] . '</b> <i>(' . $monster[file] . ')</i><br>';
	}

function addLoot($loot, $level=1) {
	foreach($loot as $test) {
		$chance = $test['chance'];
		if(!$chance)
			$chance = $test['chance1'];

		printLoot($level, $test['id'], $test['countmax'], $chance);
		foreach($test as $k => $v)
			addLoot($v->item, $level + 1);
	}
}

function printLoot($level, $itemid, $count, $chance) {
	global $itemList, $rarity;

	$chance /= 1000;
	if(isset($_GET['lootrate'])) {
		global $lootRate;
		$chance *= $lootRate;
	}

	foreach($rarity as $lootRarity => $percent){
		if($chance >= $percent) {
			echo str_repeat("... ", $level) . '<u>' . ($count ? $count : 1) . '</u> <span style="color: #7878FF; font-weight: bold;">' . $itemList[(int)$itemid] . '</span> - <span style="color: #C45; font-weight: bold;">' . $lootRarity . '</span> (<span style="color: #FF9A9A;">' . $chance . '%</span>)<br>';
			break;
		}
	}
}
?>
<?php include 'layout/overall/footer.php'; ?>

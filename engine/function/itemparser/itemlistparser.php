<?php 

/* Returns a PHP array $id => 'name'
	 $items = getItemList();
	 echo $items[2160]; // Returns 'Crystal Coin'
*/

function getItemList() {	
	$items = parseItems();	
	return $items;
}

function getItemById($id) {
	$items = parseItems();
	if(isset($items[$id])) {
		return $items[$id];
	}
	return false;
}

function parseItems() {
	global $config;

	$items = simplexml_load_file($config['server_path'].'/data/items/items.xml');
	$out = array();
	
	// Create our parsed item list
	foreach ($items->children() as $item) {
		if ($item['id'] && $item['name'] != NULL) {
			$out[(string)$item['id']] = (string)$item['name'];
		}
	}

	return $out;
}

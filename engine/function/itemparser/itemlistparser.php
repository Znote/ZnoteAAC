<?php 
/* Returns a PHP array $id => 'name'
	 $items = getItemList();
	 echo $items[2160]; // Returns 'Crystal Coin'
*/

function getItemList() {	
	return parseItems();
}

function getItemById($id) {
	$items = parseItems();
	if(isset($items[$id])) {
		return $items[$id];
	}
	return false;
}

function parseItems() {
	$file = Config('server_path') . '/data/items/items.xml';
	if (file_exists($file)) {
		$itemList = array();
		$items = simplexml_load_file($file);
		// Create our parsed item list
		foreach ($items->children() as $item) {
			if ($item['id'] && $item['name'] != NULL) {
				$itemList[(int)$item['id']] = (string)$item['name'];
			}
		}
		return $itemList;
	}
	return $file;
}
?>
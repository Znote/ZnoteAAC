<?php

$items = simplexml_load_file($config['server_path'].'/data/items/items.xml');

// Create our parsed item list
$item_list = fopen('item_list.txt', 'w');

foreach ($items->children() as $item) {

	if ($item['id'] && $item['name'] != NULL) {

		fwrite($item_list, $item['id'].'@'.$item['name'].PHP_EOL);
	}
}

fclose($item_list);



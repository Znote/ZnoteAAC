<?php 

function getItemNameById($id) {

	$item_list = explode(PHP_EOL, file_get_contents('item_list.txt'));
	
	foreach ($item_list as $items) {

		$item_array = explode('@', $items);

		if ($item_array[0] == $id) {

			echo ucfirst($item_array[1]);
			break;
		}
	}
}

/* Returns a PHP array $id => 'name'
	 $items = getItemList();
	 echo $items[2160]; // Returns 'Crystal Coin'
	*/
function getItemList() {
	$item_list = explode(PHP_EOL, file_get_contents('item_list.txt'));
	$ia = array();
	for ($i = 0; $i < count($item_list) - 1; $i++) {
		$it = explode('@', $item_list[$i]);
		$ia[(int)$it[0]] = ucfirst($it[1]);
	}
	return $ia;
}

function getItemIdByName($name) {

	$item_list = explode(PHP_EOL, file_get_contents('item_list.txt'));
	
	foreach ($item_list as $items) {

		$item_array = explode('@', $items);

		if ($item_array[1] == $name) {

			echo ucfirst($item_array[0]);
			break;
		}
	}
}



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



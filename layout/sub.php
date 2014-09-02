<?php
/* Znote AAC Sub System
	-	Used to create custom pages
	-	Place the contents of the page in /layout/sub/ folder.
		: You don't need to include init, header or footer. 
		Its already taken care of, just write the contents you want.

	Then add that page to the configuration below. Config syntax:
	'PAGENAME' => array(
		'file' => 'fileName.php',
		'override' => false
	),
	................
	There are 2 ways to view your page, by using sub.php file, or by overriding an existing default page.
	1: yourwebiste.com/sub.php?page=PAGENAME
	2: By having override => true, then it will load your sub file instead of the default znote aac file. 

*/

$subpages = array(
	// website.com/sub.php?page=blank
	'blank' => array(
		// layout/sub/blank.php
		'file' => 'blank.php',
		// false means don't run this file instead of the regular file at website.com/blank.php
		'override' => false
	),
	'houses' => array(
		'file' => 'houses.php',
		'override' => false
	),
	'downloads' => array(
		'file' => 'downloads.php',
		'override' => false
	),
);
?>
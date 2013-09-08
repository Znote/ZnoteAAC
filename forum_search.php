<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; protect_page();

// Search CONFIG
$searchResults = 30; // How many max search results

/* Below HTML CODE = Plugin you can put anywhere in Znote AAC where you want search.
<h1>Search forum</h1>
<form action="forum_search.php" method="get">
	<select name="type">
		<option value="1">Title</option>
		<option value="2">Post</option>
		<option value="3">Author (threads)</option>
		<option value="4">Author (posts)</option>
		<option value="5">Latest Posts</option>
		<option value="6">Latest Threads</option>
	</select>
	<input type="text" name="text" placeholder="Search string">
	<input type="submit" value="Search">
</form>
*/
function stripBBCode($text_to_search) {
	$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
	$replace = '';
	return preg_replace($pattern, $replace, $text_to_search);
}

//data_dump($_GET, false, "Post data:");

// Fetch and sanitize values:
$type = getValue($_GET['type']);
if ($type !== false) $type = (int)$type;
$text = getvalue($_GET['text']);

$textTitleSql = "";
$textPostSql = "";
$textAuthorSql = "";
if ($text !== false) {
	$text = explode(' ', $text);
	for ($i = 0; $i < count($text); $i++) {
		if ($i != count($text) -1) {
			$textTitleSql .= "`title` LIKE '%". $text[$i] ."%' AND ";
			$textPostSql .= "`text` LIKE '%". $text[$i] ."%' AND ";
			$textAuthorSql .= "`player_name` LIKE '%". $text[$i] ."%' AND ";
		} else {
			$textTitleSql .= "`title` LIKE '%". $text[$i] ."%'";
			$textPostSql .= "`text` LIKE '%". $text[$i] ."%'";
			$textAuthorSql .= "`player_name` LIKE '%". $text[$i] ."%'";
		}
	}
	//data_dump($text, array($textTitleSql, $textPostSql, $textAuthorSql), "search");
}

?>
<h1>Search forum</h1>
<form method="" type="get">
	<select name="type">
		<option value="1" <?php if ($type == 1) echo "selected"; ?>>Title</option>
		<option value="2" <?php if ($type == 2) echo "selected"; ?>>Post</option>
		<option value="3" <?php if ($type == 3) echo "selected"; ?>>Author (threads)</option>
		<option value="4" <?php if ($type == 4) echo "selected"; ?>>Author (posts)</option>
		<option value="5" <?php if ($type == 5) echo "selected"; ?>>Latest Posts</option>
		<option value="6" <?php if ($type == 6) echo "selected"; ?>>Latest Threads</option>
	</select>
	<input type="text" name="text" value="<?php if ($text !== false) echo implode(' ', $text); ?>">
	<input type="submit" value="Search">
</form>
<?php

if ($type !== false && $text !== false && $type <= 4 || $type > 4 && $type <= 6) {
	$forums = mysql_select_multi("SELECT `id` FROM `znote_forum` WHERE `access`='1' AND `guild_id`='0';");
	$allowedForums = array();
	foreach($forums as $forum) $allowedForums[] = $forum['id'];

	//data_dump($allowedForums, false, "Allowed forums to search");
	// in_array(6, $allowedForums)

	$results = false;
	switch ($type) {
		case 1: // Search titles
			$results = mysql_select_multi("SELECT `id` AS `thread_id`, `forum_id`, `title`, `text`, `player_name` FROM `znote_forum_threads` WHERE $textTitleSql ORDER BY `id` DESC LIMIT $searchResults;");
			// Filter out search results in custom access boards.
			for ($i = 0; $i < count($results); $i++) 
				if (!in_array($results[$i]['forum_id'], $allowedForums)) 
					$results[$i]['forum_id'] = false;
				else {
					$results[$i]['title'] = stripBBCode($results[$i]['title']);
					$results[$i]['text'] = stripBBCode($results[$i]['text']);
				}

			//if ($results !== false) data_dump($results, false, "Search results");
			//else echo "<br><b>No results.</b>";
			break;
		
		case 2: // Search posts
			$results = mysql_select_multi("SELECT `thread_id`, `player_name`, `text` FROM `znote_forum_posts` WHERE $textPostSql ORDER BY `id` DESC LIMIT $searchResults;");
			// Missing ['forum_id'], ['title'], lets get them
			for ($i = 0; $i < count($results); $i++) {
				// $results[$i]['asd']
				$thread = mysql_select_single("SELECT `forum_id`, `title` FROM `znote_forum_threads` WHERE `id`='".$results[$i]['thread_id']."' LIMIT 1;");
				if ($thread !== false) {
					$results[$i]['forum_id'] = $thread['forum_id'];
					$results[$i]['title'] = $thread['title'];
					if (!in_array($results[$i]['forum_id'], $allowedForums)) $results[$i]['forum_id'] = false;
					else {
						$results[$i]['title'] = stripBBCode($results[$i]['title']);
						$results[$i]['text'] = stripBBCode($results[$i]['text']);
					}
				} else $results[$i]['forum_id'] = false;

			} // DONE. :)
			//data_dump(false, $results, "DATA");
			break;
		
		case 3: // Search authors last threads
			$results = mysql_select_multi("SELECT `id` AS `thread_id`, `forum_id`, `title`, `text`, `player_name` FROM `znote_forum_threads` WHERE $textAuthorSql ORDER BY `id` DESC LIMIT $searchResults;");
			// Filter out search results in custom access boards.
			for ($i = 0; $i < count($results); $i++) 
				if (!in_array($results[$i]['forum_id'], $allowedForums)) 
					$results[$i]['forum_id'] = false;
				else {
					$results[$i]['title'] = stripBBCode($results[$i]['title']);
					$results[$i]['text'] = stripBBCode($results[$i]['text']);
				}

			//if ($results !== false) data_dump($results, false, "Search results");
			//else echo "<br><b>No results.</b>";
			break;
		
		case 4: // Search authors last posts
			$results = mysql_select_multi("SELECT `thread_id`, `player_name`, `text` FROM `znote_forum_posts` WHERE $textAuthorSql ORDER BY `id` DESC LIMIT $searchResults;");
			// Missing ['forum_id'], ['title'], lets get them
			for ($i = 0; $i < count($results); $i++) {
				// $results[$i]['asd']
				$thread = mysql_select_single("SELECT `forum_id`, `title` FROM `znote_forum_threads` WHERE `id`='".$results[$i]['thread_id']."' LIMIT 1;");
				if ($thread !== false) {
					$results[$i]['forum_id'] = $thread['forum_id'];
					$results[$i]['title'] = $thread['title'];
					if (!in_array($results[$i]['forum_id'], $allowedForums)) $results[$i]['forum_id'] = false;
					else {
						$results[$i]['title'] = stripBBCode($results[$i]['title']);
						$results[$i]['text'] = stripBBCode($results[$i]['text']);
					}
				} else $results[$i]['forum_id'] = false;

			} // DONE. :)
			break;

		case 5: // Search latest titles
			$results = mysql_select_multi("SELECT `id` AS `thread_id`, `forum_id`, `title`, `text`, `player_name` FROM `znote_forum_threads` ORDER BY `id` DESC LIMIT $searchResults;");
			// Filter out search results in custom access boards.
			for ($i = 0; $i < count($results); $i++) 
				if (!in_array($results[$i]['forum_id'], $allowedForums)) 
					$results[$i]['forum_id'] = false;
				else {
					$results[$i]['title'] = stripBBCode($results[$i]['title']);
					$results[$i]['text'] = stripBBCode($results[$i]['text']);
				}

			//if ($results !== false) data_dump($results, false, "Search results");
			//else echo "<br><b>No results.</b>";
			break;

		case 6: // Search posts
			$results = mysql_select_multi("SELECT `thread_id`, `player_name`, `text` FROM `znote_forum_posts` ORDER BY `id` DESC LIMIT $searchResults;");
			// Missing ['forum_id'], ['title'], lets get them
			for ($i = 0; $i < count($results); $i++) {
				// $results[$i]['asd']
				$thread = mysql_select_single("SELECT `forum_id`, `title` FROM `znote_forum_threads` WHERE `id`='".$results[$i]['thread_id']."' LIMIT 1;");
				if ($thread !== false) {
					$results[$i]['forum_id'] = $thread['forum_id'];
					$results[$i]['title'] = $thread['title'];
					if (!in_array($results[$i]['forum_id'], $allowedForums)) $results[$i]['forum_id'] = false;
					else {
						$results[$i]['title'] = stripBBCode($results[$i]['title']);
						$results[$i]['text'] = stripBBCode($results[$i]['text']);
					}
				} else $results[$i]['forum_id'] = false;

			} // DONE. :)
			//data_dump(false, $results, "DATA");
			break;
		default:
			# code...
			break;
	}

	// Create table and show stuff!
	if ($results !== false) {
		$count = 0;
		foreach ($results as $r) if ($r['forum_id'] !== false) $count++;
		if ($count > 0) {
			?>
			<table>
				<tr>
					<th>Char</th>
					<th>Thread</th>
					<th>Post</th>
				</tr>
				<?php
				foreach ($results as $result) {
					if ($result['forum_id'] !== false) {
						// $result required fields = ['thread_id'], ['forum_id'], ['title'], ['text'], ['player_name']
						?>
						<tr>
							<td><a href="characterprofile.php?name=<?php echo $result['player_name']; ?>"><?php echo $result['player_name']; ?></a></td>
							<td>
		<a href="forum.php?thread=<?php echo $result['thread_id']; ?>&forum=Search&cat=<?php echo $result['forum_id']; ?>">
			<?php echo $result['title']; ?>
		</a>
							</td>
							<td><?php echo (strlen($result['text']) > 140) ? substr($result['text'],0,137).'...' : $result['text']; ?></td>
						</tr>
						<?php
					}
				}
				?>
			</table>
			<?php
		} else echo "No results.";
	} else echo "No results.";
} else echo "<br><b>You must fill in all fields!</b>";

include 'layout/overall/footer.php'; 
?>
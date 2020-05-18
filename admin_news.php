<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; 
protect_page();
admin_only($user_data);


// Recieving POST
if (empty($_POST) === false) {
	list($action, $id) = explode('!', sanitize($_POST['option']));

	// Delete
	if ($action === 'd') {
		echo '<font color="green"><b>News deleted!</b></font>';
		mysql_delete("DELETE FROM `znote_news` WHERE `id`='$id';");
		$cache = new Cache('engine/cache/news');
		$news = fetchAllNews();
		$cache->setContent($news);
		$cache->save();
	}
	// Add news
	if ($action === 'a') {
		// fetch data
		$char_array = user_character_list($user_data['id']);
		?>

		<script src="engine/js/nicedit.js" type="text/javascript"></script>
		<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
		<form action="" method="post">
			<input type="hidden" name="option" value="i!0">
			Select character:<select name="selected_char">
			<?php
			$count = 0;
			if ($char_array !== false) {
				foreach ($char_array as $name) {
					$name = $name['name'];
					$charD = user_character_data(user_character_id($name), 'group_id', 'id');
					if ($charD['group_id'] > 1) {
						echo '<option value="'. user_character_id($name) .'">'. $name .'</option>';
						$count++;
					}
				}
			}
			?>
			</select>		
			<input type="text" name="title" value="" placeholder="Title"> [youtube]wK0w0x62PjA[/youtube] <br />
			<textarea name="text" id="area1" cols="75" rows="10" placeholder="Contents..." style="width: 100%"></textarea><br />
			<input type="submit" value="Create News">
		</form>

		<?php
		if ($count === 0) echo "<font size='6' color='red'>ERROR: NO GMs or Tutors on this account!</font>";
	}
	// Insert news
	if ($action === 'i') {
		echo '<font color="green"><b>News created successfully!</b></font>';
		list($charid, $title, $text) = array((int)$_POST['selected_char'], mysql_znote_escape_string($_POST['title']), mysql_znote_escape_string($_POST['text']));
		$date = time();
		mysql_insert("INSERT INTO `znote_news` (`title`, `text`, `date`, `pid`) VALUES ('$title', '$text', '$date', '$charid');");
		// Reload the cache.
		$cache = new Cache('engine/cache/news');
		$news = fetchAllNews();
		$cache->setContent($news);
		$cache->save();
	}
	// Save
	if ($action === 's') {
		echo '<font color="green"><b>News successfully updated!</b></font>';
		list($title, $text) = array(mysql_znote_escape_string($_POST['title']), mysql_znote_escape_string($_POST['text']));
		mysql_update("UPDATE `znote_news` SET `title`='$title',`text`='$text' WHERE `id`='$id';");
		$cache = new Cache('engine/cache/news');
		$news = fetchAllNews();
		$cache->setContent($news);
		$cache->save();
	}
	// Edit
	if ($action === 'e') {
		$news = fetchAllNews();
		$edit = array();
		foreach ($news as $n) if ($n['id'] == $id) $edit = $n;
		?>
		<script src="engine/js/nicedit.js" type="text/javascript"></script>
		<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
		<form action="" method="post">
			<input type="hidden" name="option" value="s!<?php echo $id; ?>">
			<input type="text" name="title" value="<?php echo $edit['title']; ?>"><br />
			<textarea name="text" cols="75" rows="10" style="width: 100%"><?php echo $edit['text']; ?></textarea><br />
			<input type="submit" value="Save Changes">
		</form>
		<br>
		<p>
			[b]<b>Bold Text</b>[/b]<br>
			[size=5]Size 5 text[/size]<br>
			[img]<a href="https://imgur.com/" target="_BLANK">Direct Image Link</a>[/img]<br>
			[center]Cented Text[/center]<br>
			[link]<a href="https://youtube.com/" target="_BLANK">https://youtube.com/</a>[/link]<br>
			[link=https://youtube.com/]<a href="http://youtube.com/" target="_BLANK">Click to View youtube</a>[/link]<br>
			[color=<font color="green">GREEN</font>]<font color="green">Green Text!</font>[/color]<br>
			[*]* Noted text [/*]
		</p>
		<?php
	}
}

?>
<h1>News admin panel</h1>
<form action="" method="post">
	<input type="hidden" name="option" value="a!0">
	<input type="submit" value="Create new article">
</form>
<?php
// pre stuff
$news = fetchAllNews();
if ($news !== false) {
	?>
	<table id="news">
		<tr class="yellow">
			<td>Date</td>
			<td>By</td>
			<td>Title</td>
			<td>Edit</td>
			<td>Delete</td>
		</tr>
		<?php
		foreach ($news as $n) {
			echo '<tr>';
			echo '<td>'. getClock($n['date'], true) .'</td>';
			echo '<td><a href="characterprofile.php?name='. $n['name'] .'">'. $n['name'] .'</a></td>';
			echo '<td>'. $n['title'] .'</td>';
			echo '<td>';
			// edit
			?>
			<form action="" method="post">
				<input type="hidden" name="option" value="e!<?php echo $n['id']; ?>">
				<input type="submit" value="Edit">
			</form>
			<?php
			echo '</td>';
			echo '<td>';
			// delete
			?>
			<form action="" method="post">
				<input type="hidden" name="option" value="d!<?php echo $n['id']; ?>">
				<input type="submit" value="Delete">
			</form>
			<?php
			echo '</td>';
			echo '</tr>';
		}
		?>
	</table>
	<?php
}
include 'layout/overall/footer.php'; ?>
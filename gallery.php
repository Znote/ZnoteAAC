<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; 
$logged_in = user_logged_in();
if ($logged_in === true) {
	if (!empty($_POST['new'])) {
		?>
		<h1>Create image article</h1>
		<p>Only works with "Direct link" URLs from <a href="http://www.imgland.net/">imgland.net</a>
		<br />Don't understand? Don't worry! Watch this <a href="http://youtu.be/r9pEc7T3cJg" target="_BLANK">video guide!</a></p>
		<form action="" method="post">
			Image URL:<br /><input type="text" name="image" size="70"><br />
			Image Title:<br /><input type="text" name="title" size="70"><br />
			Image Describtion:<br /><textarea name="desc" cols="55" rows="15"></textarea><br />
			<input type="submit" name="Submit" value="Post Image Article">
		</form>
		<?php
	}
	if (!empty($_POST['image']) && !empty($_POST['title']) && !empty($_POST['desc'])) {
		$image = sanitize($_POST['image']);
		$image = str_replace("www", "", str_replace(":", "", str_replace("/", "", str_replace(".", "!", str_replace("ii.gl", "", str_replace("http", "", $image))))));
		$title = sanitize($_POST['title']);
		$desc = sanitize($_POST['desc']);
		
		// Insert to database
		insertImage((int)$session_user_id, $title, $desc, $image);
		
		$pw = explode("!", $image);
		?>
		<h1>Image Posted</h1>
		<p>However, your image will not be listed until a GM have verified it.<br />
		Feel free to remind the GM in-game to login on website and approve the image post.</p>
		<h2>Preview:</h2>
		<table>
			<tr class="yellow">
				<td><h3><?php echo $title; ?></h3></td>
			</tr>
			<tr>
				<td>
<a href="<?php echo 'http://'. $pw[0] .'.ii.gl/'. $pw[1] .'.'. $pw[2]; ?>" target="_BLANK"><img class="galleryImage" src="<?php echo 'http://'. $pw[0] .'.ii.gl/'. $pw[1] .'.'. $pw[2]; ?>"/></a>
				</td>
			</tr>
			<tr>
				<td>
				<?php
				$descr = str_replace("\\r", "", $desc);
				$descr = str_replace("\\n", "<br />", $descr);
				?>
				<p><?php echo $descr; ?></p>
				</td>
			</tr>
		</table>
		<?php
	}
}
if (empty($_POST)) {
	?>
	<h1>Gallery</h1>
	<?php if ($logged_in === true) { ?>
	<form action="" method="post">
		Got some cool images to show the community? <input type="submit" name="new" value="Add Image">
	</form>
	<?php
	}
	
	$cache = new Cache('engine/cache/gallery');
	$images = $cache->load();
	if ($images != false) {
		foreach($images as $image) {
			$pw = explode("!", $image['image']);
			?>
			<table>
				<tr class="yellow">
					<td><h3><?php echo $image['title']; ?></h3></td>
				</tr>
				<tr>
					<td>
<a href="<?php echo 'http://'. $pw[0] .'.ii.gl/'. $pw[1] .'.'. $pw[2]; ?>" target="_BLANK"><img class="galleryImage" src="<?php echo 'http://'. $pw[0] .'.ii.gl/'. $pw[1] .'.'. $pw[2]; ?>"/></a>
					</td>
				</tr>
				<tr>
					<td>
					<?php
					$descr = str_replace("\\r", "", $image['desc']);
					$descr = str_replace("\\n", "<br />", $descr);
					?>
					<p><?php echo $descr; ?></p>
					</td>
				</tr>
			</table>
		<?php }
	} else echo '<h2>There are currently no public images.</h2>';
	
	if ($logged_in === false) echo 'You need to be logged in to add images.';
}
include 'layout/overall/footer.php';
/*
$url = strtolower("HTTP://1.imgland.net/pxPmUL.jpg");
echo $url .'<br />';
$url = str_replace("www", "", str_replace(":", "", str_replace("/", "", str_replace(".", "!", str_replace("imgland.net", "", str_replace("http", "", $url))))));
$url = sanitize($url);
echo $url;
$url = explode("!", $url);
<a href="<?php echo 'http://'. $url[0] .'.imgland.net/'. $url[1] .'.'. $url[2]; ?>"><img src="<?php echo 'http://'. $url[0] .'.imgland.net/'. $url[1] .'.'. $url[2]; ?>" width="650"/></a>
echo time();
//insertImage(2, "Yaay!", "Super describtion!", "1!pxpmul!jpg");
*/?>
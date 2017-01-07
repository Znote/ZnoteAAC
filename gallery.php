<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; 
$logged_in = user_logged_in();
if ($logged_in === true) {
	if (!empty($_POST['new'])) {
		?>
		<h1>Create image article</h1>
		<p>Works with "Image Code" text from <a href="http://www.freeimagehosting.net/" target="_BLANK">www.freeimagehosting.net</a></p>
		<form action="" method="post">
			Image Code:<br /><input type="text" name="image" size="70"><br />
			Image Title:<br /><input type="text" name="title" size="70"><br />
			Image Description:<br /><textarea name="desc" cols="55" rows="15"></textarea><br />
			<input type="submit" name="Submit" value="Post Image Article">
		</form>
		<?php
	}
	if (!empty($_POST['image']) && !empty($_POST['title']) && !empty($_POST['desc'])) {
		$imageDom = $_POST['image'];
		$imageSrc = false;
		$doc=new DOMDocument();
		$doc->loadHTML($imageDom);
		$xml=simplexml_import_dom($doc); // just to make xpath more simple
		$images=$xml->xpath('//img');
		foreach ($images as $img) { 
			$imageSrc = (string)$img['src'];
		}
		$title = $_POST['title'];
		$desc = $_POST['desc'];

		if ($imageSrc !== false) {

			// Insert to database
			$inserted = insertImage((int)$session_user_id, $title, $desc, $imageSrc);
			if ($inserted === true) {
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
							<a href="<?php echo $imageSrc; ?>" target="_BLANK"><img class="galleryImage" src="<?php echo $imageSrc; ?>" alt="<?php echo $title; ?>"/></a>
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
			} else { // Image not inserted because it already exist
				?>
				<h1>Image already exist</h1>
				<p>The image has already been posted. However, images will not be listed until a GM have verified it.</p>
				<?php
			}
			
		} else { // Failed to locate imageSrc
			?>
			<h1>Failed to find the image</h1>
			<p>We failed to find the image, did you give us the Image code from <a href="http://www.freeimagehosting.net/">www.freeimagehosting.net</a>?</p>
			<?php
		}
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
	if (is_array($images) && !empty($images)) {
		foreach($images as $image) {
			?>
			<table>
				<tr class="yellow">
					<td><h3><?php echo $image['title']; ?></h3></td>
				</tr>
				<tr>
					<td>
						<a href="<?php echo $image['image']; ?>" target="_BLANK"><img class="galleryImage" src="<?php echo $image['image']; ?>" alt="<?php echo $image['title']; ?>"/></a>
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
?>
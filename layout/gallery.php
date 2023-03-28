<?php require_once 'engine/init.php'; include 'layout/overall/header.php';

/* SETUP INSTALLATION
 - See comments above $config['gallery'] in config.php */
 
$logged_in = user_logged_in();
if ($logged_in === true) {
	if (!empty($_POST['new'])) {
		?>
		<h1>Create image article</h1>
		<p>This gallery is powered by IMGUR image host.</p>
		<form action="" method="post" enctype="multipart/form-data">
			Select image to upload:<br><input type="file" name="imagefile" id="imagefile"><br>
			Image Title:<br /><input type="text" name="title" size="70"><br />
			Image Description:<br /><textarea name="desc" cols="55" rows="15"></textarea><br />
			<input type="submit" value="Upload Image" name="submit">
		</form>
		<?php
	}

	if (isset($_FILES['imagefile']) && !empty($_FILES['imagefile'])) {
		$image = file_get_contents($_FILES['imagefile']['tmp_name']);
		$imgurClientID = $config['gallery']['Client ID'];

		// Post image to imgur
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.imgur.com/3/image/");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, [
			"type" => "file", 
			"name" => $_FILES['imagefile']['name'],
			"image" => $image
		]);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Authorization: Client-ID {$imgurClientID}"
		));
		$response = json_decode(curl_exec($ch));
		$image_url = $response->data->link;
		$image_delete = $response->data->deletehash;
		$title = $_POST['title'];
		$desc = $_POST['desc'];

		if ($image_url !== false) {

			// Insert to database
			$inserted = insertImage((int)$session_user_id, $title, $desc, $image_url, $image_delete);
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
							<a href="<?php echo $image_url; ?>" target="_BLANK"><img class="galleryImage" style="max-width: 100%;" src="<?php echo $image_url; ?>" alt="<?php echo $title; ?>"/></a>
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
						<a href="<?php echo $image['image']; ?>" target="_BLANK"><img class="galleryImage" style="max-width: 100%;" src="<?php echo $image['image']; ?>" alt="<?php echo $image['title']; ?>"/></a>
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

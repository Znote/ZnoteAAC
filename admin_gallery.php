<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; 
protect_page();
admin_only($user_data);
// start

// Delete
if (isset($_POST['delete'])) {
	$data = explode(":", $_POST['delete']);
	echo 'Image '. $data[0] .' deleted.';
	updateImage($data[0], 3);
}

// Remove
if (isset($_POST['remove'])) {
	$data = explode(":", $_POST['remove']);
	$did = (int)$data[0];
	echo 'Image '. $did .' removed.';
	mysql_delete("DELETE FROM `znote_images` WHERE `id`='$did' LIMIT 1;");
}

// Accept
if (isset($_POST['accept'])) {
	$data = explode(":", $_POST['accept']);
	echo 'Image '. $data[0] .' accepted and is now public.';
	updateImage($data[0], 2);
}

// Wether we accept or delete, re-create the cache
if (isset($_POST['accept']) || isset($_POST['delete'])) {
	$cache = new Cache('engine/cache/gallery');
	$images = fetchImages(2);
	if ($images != false) {
		$data = array();
		foreach ($images as $image) {
			$row['title'] = $image['title'];
			$row['desc'] = $image['desc'];
			$row['date'] = $image['date'];
			$row['image'] = $image['image'];
			$data[] = $row;
		}
	} else $data = "";
	$cache->setContent($data);
	$cache->save();
}

?><h1>Images in need of moderation:</h1><?php
$images = fetchImages(1);
if ($images != false) {
	foreach($images as $image) {
		?>
		<table>
			<tr class="yellow">
				<td><h2><?php echo $image['title']; ?><form action="" method="post"><input type="submit" name="accept" value="<?php echo $image['id']; ?>:Accept Image"/></form><form action="" method="post"><input type="submit" name="delete" value="<?php echo $image['id']; ?>:Delete Image"/></form></h2></td>
			</tr>
			<tr>
				<td>
					<a href="<?php echo $image['image']; ?>"><img src="<?php echo $image['image']; ?>" alt="<?php echo $image['title']; ?>" style="max-width: 100%;"/></a>
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
} else echo '<h2>All good, no new images to moderate.</h2>';

?><h1>Public Images:</h1><?php
$images = fetchImages(2);
if ($images != false) {
	foreach($images as $image) {
		?>
		<table>
			<tr class="yellow">
				<td><h2><?php echo $image['title']; ?><form action="" method="post"><input type="submit" name="delete" value="<?php echo $image['id']; ?>:Delete Image"/></form></h2></td>
			</tr>
			<tr>
				<td>
					<a href="<?php echo $image['image']; ?>"><img src="<?php echo $image['image']; ?>" alt="<?php echo $image['title']; ?>" style="max-width: 100%;"/></a>
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

?><h1>Deleted Images:</h1><?php
$images = fetchImages(3);
if ($images != false) {
	foreach($images as $image) {
		?>
		<table>
			<tr class="yellow">
				<td><h2><?php echo $image['title']; ?><form action="" method="post">
				<input type="submit" name="accept" value="<?php echo $image['id']; ?>:Recover Image"/>
				<input type="submit" name="remove" value="<?php echo $image['id']; ?>:Remove Image"/>
				</form></h2></td>
			</tr>
			<tr>
				<td>
					<a href="<?php echo $image['image']; ?>"><img src="<?php echo $image['image']; ?>" alt="<?php echo $image['title']; ?>" style="max-width: 100%;"/></a>
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
} else echo '<h2>There are currently no deleted images.</h2>';
// end
 include 'layout/overall/footer.php'; ?>
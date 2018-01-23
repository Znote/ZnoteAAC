<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<h1>Downloads</h1>
<p>In order to play, you need an compatible IP changer and a Tibia client.</p>

<p>Download IP changer <a href="https://github.com/jo3bingham/tibia-ip-changer/releases/latest">HERE</a>.</p>
<p>Download Tibia client <?php echo ($config['client'] / 100); ?> for windows <a href="<?php echo $config['client_download']; ?>">HERE</a>.</p>
<p>Download Tibia client <?php echo ($config['client'] / 100); ?> for linux <a href="<?php echo $config['client_download_linux']; ?>">HERE</a>.</p>

<h2>How to connect and play:</h2>
<ol>
	<li>
		<a href="<?php echo $config['client_download']; ?>">Download</a> and install the tibia client if you havent already.
	</li>
	<li>
		<a href="https://github.com/jo3bingham/tibia-ip-changer/releases/latest">Download</a> and run the IP changer.
	</li>
	<li>
		In the IP changer, change Client Path to the tibia.exe file where you installed the client.</strong>
	</li>
	<li>
		In the IP changer, write this in the IP field: <?php echo $_SERVER['SERVER_NAME']; ?>
	</li>
	<li>
		Now you can successfully login on the tibia client and play clicking on <strong>Apply</strong>.<br>
		If you do not have an account to login with, you need to register an account <a href="register.php">HERE</a>.
	</li>
</ol>

<?php 
include 'layout/overall/footer.php'; ?>

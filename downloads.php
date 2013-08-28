<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<h1>Downloads</h1>
<p>In order to play, you need an compatible IP changer and a Tibia client.</p>

<p>Download otland IP changer <a href="http://static0.otland.net/ipchanger.exe">HERE</a>.</p>
<p>Download Tibia client <?php echo ($config['client'] / 100); ?> <a href="<?php echo $config['client_download']; ?>">HERE</a>.</p>

<h2>How to connect and play:</h2>
<ol>
	<li>
		<a href="http://remeresmapeditor.com/rmedl.php?file=tibia<?php echo ($config['client']); ?>.exe">Download</a>, install and start the tibia client if you havent already.
	</li>
	<li>
		<a href="http://static0.otland.net/ipchanger.exe">Download</a> and run the IP changer.
	</li>
	<li>
		In the IP changer, write this in the IP field: <?php echo $_SERVER['SERVER_NAME']; ?>
	</li>
	<li>
		In the IP changer, write this in the Port field: <?php echo $config['port']; ?>
	</li>
	<li>
		Now you can successfully login on the tibia client and play. <br>
		If you do not have an account to login with, you need to register an account <a href="register.php">HERE</a>.
	</li>
</ol>

<?php 
include 'layout/overall/footer.php'; ?>
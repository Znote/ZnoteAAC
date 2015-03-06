<div style="background-color: pink;">
	<h1>Downloads</h1>
	<h2>Sub system Override DEMO</h2>
	<p>In order to play, you need an compatible IP changer and a Tibia client.</p>

	<p>Download otland IP changer <a href="http://static0.otland.net/ipchanger.exe">HERE</a>.</p>
	<p>Download Tibia client <?php echo ($config['client'] / 100); ?> for windows <a href="<?php echo $config['client_download']; ?>">HERE</a>.</p>
	<p>Download Tibia client <?php echo ($config['client'] / 100); ?> for linux <a href="<?php echo $config['client_download_linux']; ?>">HERE</a>.</p>

	<h2>How to connect and play:</h2>
	<ol>
		<li>
			<a href="<?php echo $config['client_download']; ?>">Download</a> and install the tibia client if you havent already.
		</li>
		<li>
			<a href="http://static0.otland.net/ipchanger.exe">Download</a> and run the IP changer.
		</li>
		<li>
			In the IP changer, write this in the IP field: <?php echo $_SERVER['SERVER_NAME']; ?>
		</li>
		<li>
			In the IP changer, click on <strong>Settings</strong> and then <strong>Add new Tibia client.</strong>
		</li>
		<li>
			In the IP changer, in the Version field, write your desired version.
		</li>
		<li>
			In the IP changer, click on <strong>Browse</strong>, navigate to your desired Tibia version folder, select Tibia.exe and click <strong>Add</strong>. Then click <strong>Close</strong>
		</li>
		<li>
			Now you can successfully login on the tibia client and play clicking on <strong>Apply</strong> every time you want.<br>
			If you do not have an account to login with, you need to register an account <a href="register.php">HERE</a>.
		</li>
	</ol>
</div>
<div class="sidebar">
	<h2>Server Information</h2>
	<div class="inner">
		<ul>
			<?php
			$status = true;
			if ($config['status']['status_check']) {
				@$sock = fsockopen ($config['status']['status_ip'], $config['status']['status_port'], $errno, $errstr, 1);
				if(!$sock) {
					echo "<span style='color:red;font-weight:bold;'><center>Server Offline!</center></span><br/>";
					$status = false;
				}
				else {
					$info = chr(6).chr(0).chr(255).chr(255).'info';
					fwrite($sock, $info);
					$data='';
					while (!feof($sock))$data .= fgets($sock, 1024);
					fclose($sock);
					echo "<span style='color:green;font-weight:bold;'><center>Server Online!</center></span><br />";
				}
			}
			if ($status) {
				?>
				<li><a href="onlinelist.php">Players online: <?php echo user_count_online();?></a></li>
				<?php
			}
			?>
			<li>Registered accounts: <?php echo user_count_accounts();?></li>
		</ul>
	</div>
</div>
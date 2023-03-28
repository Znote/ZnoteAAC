							</div>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
			</div>	
			<div class="container_right">
				<a class="download_client" href="#"></a>
				<div class="right_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/quick.gif"><span style="background-image: url(layout/widget_texts/quicklogin.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
								<?php if (user_logged_in() === false) { ?>
								<div class="login"></div>
										<form action="login.php" method="post" style="margin-bottom: 0;">
											<input type="text" name="username" value="Account number" class="inputtext" onfocus="this.value=''" onblur="if(this.value=='') { this.value='Account number'};">
											<input type="password" name="password" value="Password" class="inputtext" onfocus="this.value=''" onblur="if(this.value=='') { this.value='Password'};">
											<input type="submit" name="Submit" value="" class="loginbtn"> <a class="createbtn" href="register.php"></a>
											<?php
												/* Form file */
												Token::create();
											?>
											<center style="font-size: 12px;">
												Lost <a href="recovery.php?mode=username">ACC Number</a> or <a href="recovery.php?mode=password">Password</a>?
											</center>
										</form>
								<?php }else{ ?>
								<div class="acc_menu">
									<center>
										Welcome, <?php echo $user_data['name']; ?>
												<a href='myaccount.php' class="inputbtn">Manage Account</a>
												<a style="color: orange;" href='createcharacter.php' class="inputbtn">Create Character</a>
												<a href='logout.php' class="inputbtn">Logout</a>
												
												<?php if (is_admin($user_data)){ ?>
												<font color="red">ADMIN PANEL</font>
													<a href='admin.php'>Admin Page</a>
													<a href='admin_news.php'>Admin News</a>
													<a href='admin_gallery.php'>Admin Gallery</a>
													<a href='admin_skills.php'>Admin Skills</a>
													<a href='admin_reports.php'>Admin Reports</a>
													<a href='admin_helpdesk.php'>Admin Helpdesk</a>
													<a href='admin_shop.php'>Admin Shop</a>
												<?php } ?>
									</center>
								</div>
								<?php } ?>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<div class="right_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/gallery.gif"><span style="background-image: url(layout/widget_texts/gallery.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
							<div class="slider">
								<div class="sbox">
									<div id="slides">
										<img src="layout/slides/1.png">
										<img src="layout/slides/1.png">
										<img src="layout/slides/1.png">
										<img src="layout/slides/1.png">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<div class="right_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/info.gif"><span style="background-image: url(layout/widget_texts/serverinfo.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
								<table class="sinfotable" cellspacing="0">
								<?php
								$status = true;
								
									@$sock = fsockopen ($config['status']['status_ip'], $config['status']['status_port'], $errno, $errstr, 1);
									if(!$sock) {
										echo "<tr><td><b>Status:</b></td><td> <img style=\"vertical-align:middle;\" src=\"layout/img/off.png\"></td></tr>";
										$status = false;
									}
									else {
										$info = chr(6).chr(0).chr(255).chr(255).'info';
										fwrite($sock, $info);
										$data='';
										while (!feof($sock))$data .= fgets($sock, 1024);
										fclose($sock);
										echo "<tr><td><b>Status:</b></td><td> <img style=\"vertical-align:middle;\" src=\"layout/img/on.png\"></td></tr>";
									}
								
								
									?>
									<tr><td><b>Players: </b></td><td>
										<a href="onlinelist.php"><?php echo user_count_online(); ?></a></td></tr>
									<?php
								
								?>
								<tr><td><b>Accounts: </b></td><td><?php echo user_count_accounts();?></td></tr>
								<tr><td><b>Characters: </b></td><td><?php echo user_count_characters();?></td></tr>
								</table>
								<center><a href="serverinfo.php">&raquo; Server information</a></center>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<div class="right_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/exp.gif"><span style="background-image: url(layout/widget_texts/powergamers.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
						<ul class="toplvl">
						<?php
							function coloured_value($valuein)
							{
								error_reporting(E_ALL ^ E_NOTICE);
								$value2 = $valuein;
								while(strlen($value2) > 3)
								{
									$value .= '.'.substr($value2, -3, 3);
									$value2 = substr($value2, 0, strlen($value2)-3);
								}
								@$value = $value2.$value;
								if($valuein > 0)
									return '<b><font color="green">+'.$value.'</font></b>';
								elseif($valuein < 0)
									return '<font color="red">'.$value.'</font>';
								else
									return $value;
							}
							$cache = new Cache('engine/cache/topPowergamers');
							if ($cache->hasExpired()) {
								$znotePlayers = mysql_select_multi('SELECT * FROM players WHERE group_id < 2 ORDER BY  experience - exphist_lastexp DESC LIMIT 5;');
								$cache->setContent($znotePlayers);
								$cache->save();
							} else {
								$znotePlayers = $cache->load();
							}

							if($znotePlayers){
								foreach($znotePlayers as $player)
								{
									$nam = $player['name'];
									if (strlen($nam) > 15)
									{$nam = substr($nam, 0, 12) . '...';}
									echo '<li style="margin: 6px 0;"><div style="position:relative; left:-48px; top:-48px;"><div style="background-image: url(layout/outfitter/outfit.php?id='.$player['looktype'].'&head='.$player['lookhead'].'&body='.$player['lookbody'].'&legs='.$player['looklegs'].'&feet='.$player['lookfeet'].');width:64px;height:64px;position:absolute;background-repeat:no-repeat;background-position:right bottom;"></div></div>
									<a style="margin-left: 19px;" href="characterprofile.php?name=' .$player['name']. '">' .$nam. '</a>';
									
									echo '<span style="float: right;">'.coloured_value($player['experience']-$player['exphist_lastexp']).'</span></li>';
								}
							}
							?>
							</ul>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<div class="right_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/casts.gif"><span style="background-image: url(layout/widget_texts/casts.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
						<ul class="toplvl">
						<?php
							$cache = new Cache('engine/cache/topCasts');
							if ($cache->hasExpired()) {
								$znotePlayers = mysql_select_multi('SELECT * FROM players WHERE group_id < 2 AND broadcasting = 1 ORDER BY  viewers DESC LIMIT 5;');
								$cache->setContent($znotePlayers);
								$cache->save();
							} else {
								$znotePlayers = $cache->load();
							}
							if($znotePlayers){
								foreach($znotePlayers as $player)
								{
									$nam = $player['name'];
									if (strlen($nam) > 15)
									{$nam = substr($nam, 0, 12) . '...';}
									echo '<li style="margin: 6px 0;"><div style="position:relative; left:-48px; top:-48px;"><div style="background-image: url(layout/outfitter/outfit.php?id='.$player['looktype'].'&head='.$player['lookhead'].'&body='.$player['lookbody'].'&legs='.$player['looklegs'].'&feet='.$player['lookfeet'].');width:64px;height:64px;position:absolute;background-repeat:no-repeat;background-position:right bottom;"></div></div>
									<a style="margin-left: 19px;" href="characterprofile.php?name=' .$player['name']. '">' .$nam. '</a>';
									
									echo '<span style="float: right;">'.$player['viewers'].'</span></li>';
								}
								
							}
							else
							{
								echo '<center>No active casts.</center>';
							}
							?>
							</ul>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
			</div>
			<div class="footer_cnt">
				<center>Copyright &copy; <?php echo date('Y', time()); ?> <strong>Arkonia.eu</strong>. All rights reserved.<br><a target="_blank" href="https://otland.net/members/hemrenus321.88336/" style="color: #3d4654;font-size: 11px;">by Hemrenus321</a></center>
			</div>
		</div>
	</body>
</html>

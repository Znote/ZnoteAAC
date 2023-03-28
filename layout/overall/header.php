
<!DOCTYPE html>
	<head>
		<meta charset="UTF-8">
		<title><?php echo $config['site_title']; ?></title>
		<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Amarante|Mirza" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="layout/style.css">
		<link rel="stylesheet" type="text/css" href="layout/tibia.css">
		<script src="../engine/js/jquery-1.10.2.min.js"></script>
		<script src="layout/Cufon-yui.js"></script>
		<script src="layout/jquery.slides.min.js"></script>
		<script src="layout/Trajan_Pro_400.font.js"></script>
		<script type="text/javascript">
		Cufon.replace('.cufon', {
				color: '-linear-gradient(#ffa800, #6a3c00)',
				textShadow: '#14110c 1px 1px, #14110c -1px 1px'
			});
		</script>
		<style>
.display-none {
	display: none !important;
}

.display-inline {
	display: inline !important;
}
		</style>
		<script>
			jQuery(function(){
				jQuery('.changelog_trigger').click(function(e){
					e.preventDefault();
				jQuery('.minus'+$(this).attr('targetid')).toggle();
				jQuery('.plus'+$(this).attr('targetid')).toggle();	 
				
						jQuery('.changelog_big'+$(this).attr('targetid')).toggleClass("display-inline");
					  
					  jQuery('.changelog_small'+$(this).attr('targetid')).toggleClass("display-none");
					
				});
			});
		</script>
		<script>
		   $(function() {
		   $('#slides').slidesjs({
			width: 207,
			height: 100,
			navigation: true,
			play: {
			active: false,
			auto: true,
			interval: 3000,
			swap: true,
			pauseOnHover: false,
			restartDelay: 2500
			  }
		   });
		   });
	  </script>
	</head>
	<body>
	<?php
		function user_count_characters() {
			$result = mysql_select_single("SELECT COUNT(`id`) AS `id` from `players`;");
			return ($result !== false) ? $result['id'] : 0;
		}
	?>
	
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.8";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<div class="top-bar">
		<a href="register.php">
		
							<?php
							$date = 'Dec 23 2017 16:00:00 CET';
							$exp_date = strtotime($date);
							$now = time();

							if ($now < $exp_date) {
							?>
							<script>
							// Count down milliseconds = server_end - server_now = client_end - client_now
							var server_end = <?php echo $exp_date; ?> * 1000;
							var server_now = <?php echo time(); ?> * 1000;
							var client_now = new Date().getTime();
							var end = server_end - server_now + client_now; // this is the real end time

							var _second = 1000;
							var _minute = _second * 60;
							var _hour = _minute * 60;
							var _day = _hour *24
							var timer;

							function showRemaining()
							{
								var now = new Date();
								var distance = end - now;
								if (distance < 0 ) {
								   clearInterval( timer );
								   document.getElementById('countdown').innerHTML = 'EXPIRED!';

								   return;
								}
								var days = Math.floor(distance / _day);
								var hours = Math.floor( (distance % _day ) / _hour );
								var minutes = Math.floor( (distance % _hour) / _minute );
								var seconds = Math.floor( (distance % _minute) / _second );

								var countdown = document.getElementById('countdown');
								countdown.innerHTML = '';
								if (days) {
									countdown.innerHTML += ' <span style="color:white;">' + days + '</span> DAYS ';
								}
								countdown.innerHTML += ' <span style="color:white;">' + hours+ '</span> HOURS';
								countdown.innerHTML += ' <span style="color:white;">' + minutes+ '</span> MINUTES';
								countdown.innerHTML += ' <span style="color:white;">' + seconds+ '</span> SECONDS';
							}

							timer = setInterval(showRemaining, 1000);
							</script>
							Arkonia Online 7.4 Will Start In: <span style="color: yellow;" id="countdown">loading...</span>
							<?php
							} else {
								echo 'Arkonia Online 7.4 Will Start In: <span style="color: yellow;">SERVER STARTED!</span>';
							}
							?>
		</a>
	</div>
		<div class="container_main">
			<div class="container_left">
				<div class="left_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/news.gif"><span style="background-image: url(layout/widget_texts/news.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
							<ul>
								<li><a href="index.php">Home</a></li>
								<li><a href="downloads.php">Downloads</a></li>
							</ul>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<div class="left_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/account.gif"><span style="background-image: url(layout/widget_texts/account.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
							<ul>
								<li><a href="register.php"><b><font color="orange">Create Account</font></b></a></li>
								<li><a href="recovery.php">Lost Account Interface</a></li>
								<li><a href="forum.php">Forum</a></li>
							</ul>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<div class="left_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/community.gif"><span style="background-image: url(layout/widget_texts/community.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
							<ul>
								<li><a href="sub.php?page=charactersearch">Search Character</a></li>
								<li><a href="sub.php?page=highscores">Highscores</a></li>
								<li><a href="market.php">Item Market</a></li>
								<li><a href="gallery.php">Gallery</a></li>
								
								<li><a href="helpdesk.php">Helpdesk</a></li>
								<li><a href="houses.php">Houses</a></li>
								<li><a href="deaths.php">Deaths</a></li>
								<li><a href="killers.php">Killers</a></li>
									<?php //if ($config['guildwar_enabled'] === true) { ?>
											<li><a href="guilds.php">Guild List</a></li>
											<li><a href="guildwar.php">Guild Wars</a></li>
									<?php //} ?>
                        	</ul>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<div class="left_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/library.gif"><span style="background-image: url(layout/widget_texts/library.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
							<ul>
								<li><a href="serverinfo.php">Server Information</a></li>
								<li><a href="support.php">Support</a></li>	
								<li><a href="changelog.php">Changelog</a></li>	
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<div class="left_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/shop.gif"><span style="background-image: url(layout/widget_texts/shop.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
							<ul>
								<li><a href="buypoints.php">Buy Points</a></li>
								<li><a href="shop.php">Shop Offers</a></li>
							</ul>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
			</div>
			<div class="container_mid">
				<!-- FACEBOOK -->
				<div class="center_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><span style="background-image: url(layout/widget_texts/facebook.png);"></span></div>
					<div class="content_bg">
						<div class="content">
							<div class="rise-up-content" style="min-height: 150px;">
								<div class="fb-page" style="padding: 10px 47px;" data-href="https://www.facebook.com/arkoniaonline" data-width="500" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false"><blockquote cite="https://www.facebook.com/arkoniaonline" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/arkoniaonline">Arkonia Online</a></blockquote></div>
							</div>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<!-- CHANGELOG SYSTEM -->
				<?php
					if ($config['UseChangelogTicker'] && basename($_SERVER["SCRIPT_FILENAME"], '.php') == 'index') {
						?>
						<div class="center_box">
							<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
							<div class="title"><span style="background-image: url(layout/widget_texts/changelog.png);"></span></div>
							<div class="content_bg">
								<div class="content">
									<div class="rise-up-content">
						<?php
						//////////////////////
						// Changelog ticker //
						// Load from cache
						$changelogCache = new Cache('engine/cache/changelognews');
						$changelogs = $changelogCache->load();

						if (isset($changelogs) && !empty($changelogs) && $changelogs !== false) {
							?>
							<table class="stripped" cellpadding="2" style="margin: 5px 0;">
								<?php
								for ($i = 0; $i < count($changelogs) && $i < 5; $i++) {
									?>
									<tr>
										<td><small><?php echo getClock($changelogs[$i]['time'], true, true); ?></small> - 
											<div class="changelog_small<?php echo $i; ?>"  style="display: inline-block;"><?php if(strlen($changelogs[$i]['text']) > 57) {echo substr($changelogs[$i]['text'], 0, 60) . '...';}else { echo $changelogs[$i]['text'];} ?></div>
											<div class="changelog_big<?php echo $i; ?>"  style="display: none;"><?php echo $changelogs[$i]['text']; ?></div>
										</td>
										<td width="5%" valign="top"><center><a href="#" targetid="<?php echo $i; ?>" class="changelog_trigger"><img class="plus<?php echo $i; ?>" src="layout/tibia_img/plus.gif"><img class="minus<?php echo $i; ?>" style="display: none;" src="layout/tibia_img/minus.gif"></a></center></td>
									</tr>
									<?php
								}
								?>
							</table>
							<?php
						} else echo "<center>No changelogs submitted.</center>";
						
						?>
									</div>
								</div>
							</div>
							<div class="border_bottom"></div>
						</div>
						<?php
					}
				?>
				<!-- MAIN CONTENT -->
				<div class="center_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><span class="cufon" style="text-transform: uppercase;text-align: center;line-height: 43px;font-size: 16px;"><?php echo basename($_SERVER["SCRIPT_FILENAME"], '.php'); ?></span></div>
					<div class="content_bg">
						<div class="content">
							<div class="rise-up-content" style="min-height: 565px;">
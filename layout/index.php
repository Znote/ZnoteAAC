<?php
	$follow = array(
		"twitter" => "https://www.twitter.com/",
		"facebook" => "https://www.facebook.com/",
		"youtube" => "https://www.youtube.com/",
		"twitch" => "https://www.twitch.tv/"
	);
	$url_param = "/?subtopic=";

	/*
		isNew -- determines wether the page is new or not, set to _true_ if it is a new page

		isPage -- if the menu item is not a page but a link to a file to be downloaded, set to _false_

	*/
	$menu_items = array(
		"Main" => array(
			"fontIcon" 		=> "home",
			"Latest News" 		=> array("latestnews", 	"isNew" => false, 	"isPage" => true),
			"News Archive" 	=> array("newsarchive", 	"isNew" => false, 	"isPage" => true),
			"Report Bug(s)"	=> array("reportbug", 	"isNew" => true, 	"isPage" => true)
		),
		"Account" => array(
			"fontIcon"		=> "user-circle",
			"My Account" 		=> array("accountmanagement", "isNew" => false, "isPage" => true),
			"Create Account"	=> array("createaccount", 	"isNew" => false, "isPage" => true),
			"Downloads" 		=> array("downloads", 		"isNew" => false, "isPage" => true),
			"Recover Password"	=> array("loastaccount", 	"isNew" => true, "isPage" => true)
		),
		"Community" => array(
			"fontIcon"		=> "users",
			"Characters" 		=> array("characters", 		"isNew" => false, "isPage" => true),
			"Who is online" 	=> array("whoisonline", 		"isNew" => false, "isPage" => true),
			"Highscores" 		=> array("highscores", 		"isNew" => false, "isPage" => true),
			"Houses" 			=> array("houses", 			"isNew" => false, "isPage" => true),
			"Latest Kills"		=> array("killstatistics", 	"isNew" => false, "isPage" => true),
			"Guilds"			=> array("guilds", 			"isNew" => false, "isPage" => true)
		),
		"Library" => array(
			"fontIcon"		=> "book",
			"Server Rules" 	=> array("tibiarules", 		"isNew" => false, "isPage" => true),
			"Server Info" 		=> array("serverinfo", 		"isNew" => false, "isPage" => true),
			"Exp Table" 		=> array("experiencetable", 	"isNew" => true, "isPage" => true)
		),
		"Support" => array(
			"fontIcon"		=> "info-circle",
			"Team"			=> array("team", 	"isNew" => false, "isPage" => true),
			"testDLFILE"		=> array("file.txt", "isNew" => true, "isPage" => false)
		),
		"Shop" => array(
			"fontIcon"		=> "shopping-cart",
			"Donate"			=> array("donate", 		"isNew" => true, "isPage" => true),
			"Buy Points" 		=> array("buypoints", 	"isNew" => false, "isPage" => true),
			"Items" 			=> array("shopoffer", 	"isNew" => false, "isPage" => true)
		)
	);

	$countDown = "Apr 5, 2019 15:37:25";
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
		<title>OTS Title | Layout</title>

		<!-- Stylesheet(s) -->
		<link rel="stylesheet" href="./css/style.css">
		<link rel="stylesheet" href="./fontawesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="./css/resp.css">

		<!-- JavaScript(s) -->
		<script src="./js/jq331.js" charset="utf-8"></script>
		<script src="./js/countdown.js" charset="utf-8"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				countDown("countDownTimer", $("#countDownTimer").data("date"));

				$('.loginBtn').click(function(){
					$('.loginContainer').fadeIn(2000);
				});
			});
		</script>
	</head>
	<body>
		<!--
			Author: Blackwolf (Snavy on otland)

			Otland: https://otland.net/members/snavy.155163/
			Facebook: http://www.facebook.com/idont.reallywolf.1
			Twitter: @idontreallywolf
		-->
		<!-- Main container -->
		<div class="main">
			<nav>
				<div class="container">
					<div class="pull-left">
						<ul>
							<?php foreach ($menu_items as $category => $items){ ?>
								<li><a><i class="fa fa-<?=$items["fontIcon"]?>"></i> <?=$category?></a>
									<ul>
										<?php foreach ($items as $item => $properties){
											if($item == "fontIcon") continue; ?>
											<li><a href="<?=($properties["isPage"] ? $url_param.$properties[0]:$properties[0])?>"><?=$properties[0]?></a> </li>
										<?php } ?>
									</ul>
								</li>
							<?php } ?>
						</ul>
					</div>
					<div class="pull-right">
						<ul>
							<li><a class="modIcon loginBtn"><i class="fa fa-lock"></i><i class="fa fa-unlock"></i> Login</a> </li>
							<li><a href="/?subtopic=createaccount"><i class="fa fa-key"></i> Register</a> </li>
						</ul>
					</div>
				</div>
			</nav>

			<div class="well banner"></div>

			<div class="well feedContainer preventCollapse">

				<div class="well topPane preventCollapse">
					<div class="well pull-left">
						<div id="countDownTimer" data-date="<?=$countDown?>"></div>
					</div>

					<div class="well pull-right">
						<form class="searchForm" action="/?subtopic=characters" method="post">
							<input type="text" name="name" placeholder="e.g: John Sheppard">
						</form>
					</div>
				</div>
				<!-- MAIN FEED -->
				<div class="pull-left leftPane">


<div class="postHolder">
	<div class="well">
		<div class="header">
			test
		</div>
		<div class="body">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
		</div>
	</div>
</div>

<div class="postHolder">
	<div class="well">
		<div class="header">
			test
		</div>
		<div class="body">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
		</div>
	</div>
</div>


				</div>
				<!-- MAIN FEED END -->

				<!-- RIGHT PANE -->
				<div class="pull-right rightPane">

					<div class="well loginContainer">
						<div class="header">
							Login
						</div>
						<div class="body">
							<form class="loginForm" action="/?subtopic=accountmanagement" method="post">
								<input type="password" name="accountLogin" placeholder="•••••••">
								<input type="password" name="accountLogin" placeholder="••••••••">
								<button type="submit" name="submit">continue</button>
							</form>
						</div>
					</div>

					<div class="well">
						<div class="header">
							Follow Us
						</div>
						<div class="body">
							<table class="smedia centralizeContent">
								<tr>
									<td><a href="<?=$follow["facebook"]?>" target="_blank"><i class="fa fa-facebook"></i> </a></td>
									<td><a href="<?=$follow["twitter"]?>" target="_blank"><i class="fa fa-twitter"></i> </a></td>
									<td><a href="<?=$follow["youtube"]?>" target="_blank"><i class="fa fa-youtube"></i> </a></td>
									<td><a href="<?=$follow["twitch"]?>" target="_blank"><i class="fa fa-twitch"></i> </a></td>
								</tr>
							</table>

						</div>
					</div>

					<div class="well">
						<div class="header">
							Events
						</div>
						<div class="body">
							<table>
								<tr><td>Event Name</td><td><i class="fa fa-clock-o"></i> 2h 5m 10s</td></tr>
								<tr><td>Event Name</td><td><i class="fa fa-clock-o"></i> 2h 5m 10s</td></tr>
								<tr><td>Event Name</td><td><i class="fa fa-clock-o"></i> 2h 5m 10s</td></tr>
								<tr><td>Event Name</td><td><i class="fa fa-clock-o"></i> 2h 5m 10s</td></tr>
								<tr><td>Event Name</td><td><i class="fa fa-clock-o"></i> 2h 5m 10s</td></tr>
							</table>
						</div>
					</div>

					<div class="well">
						<div class="header">
							Top 10 Players
						</div>
						<div class="body">
							<table>
								<tr><td>#</td><td>Name</td></tr>
								<tr><td>1</td><td>Name</td></tr>
								<tr><td>2</td><td>Name</td></tr>
								<tr><td>3</td><td>Name</td></tr>
								<tr><td>4</td><td>Name</td></tr>
								<tr><td>5</td><td>Name</td></tr>
								<tr><td>6</td><td>Name</td></tr>
								<tr><td>7</td><td>Name</td></tr>
								<tr><td>8</td><td>Name</td></tr>
								<tr><td>9</td><td>Name</td></tr>
								<tr><td>10</td><td>Name</td></tr>
							</table>
						</div>
					</div>
				</div>
				<!-- RIGHT PANE END -->
			</div>

			<footer class="well preventCollapse">
				<div class="pull-left">
					Designed By <a href="https://otland.net/members/snavy.155163/" target="_blank">Snavy</a>
				</div>
				<div class="pull-right">
					Github repository : <a href="https://github.com/idontreallywolf/ots_layouts" target="_blank">ots_layouts</a>
				</div>
			</footer>
		</div><!-- Main container END -->
	</body>
</html>
<!--
	Author: Blackwolf (Snavy on otland)

	Otland: https://otland.net/members/snavy.155163/
	Facebook: http://www.facebook.com/idont.reallywolf.1
	Twitter: @idontreallywolf
-->
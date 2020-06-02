<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<h1>Znote AAC</h1>
<p>This website is powered by the <a href="https://github.com/Znote/ZnoteAAC">Znote AAC</a> engine.</p>
<p>An OT website (<strong>A</strong>utomatic <strong>A</strong>ccount <strong>C</strong>reator) created by <a href="https://otland.net/members/znote.5993/">Znote</a> from the OT forum community <a href="https://otland.net">otland.net</a>.</p>
<p>Znote AAC is an open source project where everyone can help with development.</p>

<h2>Developers:</h2>
<?php // If CURL isn't enabled show default version.
if(!function_exists('curl_version')):
	?>
	<p>See the full list of developers <a href="https://github.com/Znote/ZnoteAAC/graphs/contributors">HERE</a>.</p>
<?php else:
	// CURL enabled. Lets create an API web request to github.
	$request = curl_init();
	curl_setopt($request, CURLOPT_URL, 'https://api.github.com/repos/Znote/ZnoteAAC/contributors');
	curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($request, CURLOPT_USERAGENT, 'ZnoteAAC'); // GitHub requires user agent header.
	curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);

	// Load contributors and close the request.
	$developers = json_decode(curl_exec($request), true); // Sorted by contributions.
	curl_close($request);
	?>
	<div class="developers">
		<?php foreach ($developers as $developer): ?>
			<div class="developer">
				<div class="avatar"><img src="<?php echo $developer['avatar_url']; ?>" alt="Avatar of: <?php echo $developer['login']; ?>"></div>
				<p class="username"><a href="<?php echo $developer['html_url']; ?>"><?php echo $developer['login']; ?></a>
					<br>Updates: <?php echo $developer['contributions']; ?></p>
			</div>
		<?php endforeach; ?>
	</div>
	<style type="text/css">
		/* Credits.php specific CSS alterations */
		.developers {
			width: 100%;
		}
		.developers:after {
			content: '';
			display: block;
			clear: both;
		}
		.developer {
			width: calc(20% - 16px);
			float: left;
			padding: 0 8px 16px;
		}
		.developer img {
			width: 100%;
		}
		.username {
			margin: 8px 0 0;
			text-align: center;
			overflow: hidden;
		}
	</style>
	<?php
endif;
?>

<h2>Thanks to: (in no particular order)</h2>
<p>
	<a href="https://otland.net/members/chris.13882/">Chris</a> - PHP OOP file samples, testing, bugfixing.
	<br><a href="https://otland.net/members/kiwi-dan.152/">Kiwi Dan</a> - Researching TFS 0.2 for me, participation in developement.
	<br><a href="https://otland.net/members/amoaz.26626/">Amoaz</a> - Pentesting and security tips.
	<br><a href="https://otland.net/members/evan.40401/">Evan</a>, <a href="https://otland.net/members/gremlee.12075/">Gremlee</a> - Researching TFS 0.3, constructive feedback, suggestion and participation.
	<br><a href="https://otland.net/members/att3.98289/">ATT3</a> - Reporting and fixing bugs, TFS 1.0 research. 
	<br><a href="https://otland.net/members/mark.1/">Mark</a> - Old repository, TFS distributions which this AAC was primarily built for.
	<br><a href="https://github.com/tedbro">Tedbro</a>, <a href="https://github.com/exura">Exura</a>, <a href="https://github.com/PrinterLUA">PrinterLUA</a> - Reporting bugs.
	<br><a href="https://github.com/Nottinghster">Nottinghster</a> - OTHIRE distribution compatibility. 
</p>
<style>
.contributors {
	margin-top: 10px;
	padding: 5px;
	border: 1px solid rgb(184, 184, 184);
	display: inline-flex;
	width: 100%;
}
.contributor {
	padding: 10px;
	text-align: center;
}
</style>
<?php include 'layout/overall/footer.php'; ?>

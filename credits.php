<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<h1>Znote AAC</h1>
<p>This website is powered by the Znote AAC engine.</p>

<h2>Developers:</h2>
<p>Main: <a href="http://otland.net/members/znote.5993/">Znote</a>.
<?php
if(!function_exists('curl_version')) { // If CURL isn't enabled show default version.
?>
<br>Contributor: <a href="https://github.com/Kuzirashi">Kuzirashi</a>.
<br>Contributor: <a href="https://github.com/ninjalulz">ninjalulz</a>.
<br>Contributor: <a href="https://github.com/att3">att3</a>.
<?php } else { // CURL enabled.
	echo '<br />
	Contributors:';

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'https://api.github.com/repos/Znote/ZnoteAAC/contributors');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_USERAGENT, 'ZnoteAAC'); // GitHub requires user agent header.
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$contributors = json_decode(curl_exec($curl), true); // Sorted by contributions.

	$CONTRIBUTORS_PER_ROW = 7;

	$i = 0;
	$first_container = true;
	$div_closed = true;
	foreach($contributors as $contributor)
	{
		if($contributor['login'] != 'Znote') { // Exclude Znote as he is mentioned above as main developer.
			$new_div = ctype_digit((String)($i / $CONTRIBUTORS_PER_ROW));

			if($new_div) {
				if(!$first_container)
					echo '</div>';

				echo '<div class="contributors">';
				$div_closed = false;
				$first_container = false;
			}

			echo '<div class="contributor">
					<a href="' . $contributor['html_url'] . '">
						<img src="' . $contributor['avatar_url'] . 'size=80" style="width: 80px; height: 80px" /><br/>
						<span>' . $contributor['login'] . '</span>
					</a>
				</div>';
			$i++;
		}
	}
	if(!$div_closed)
		echo '</div>';
} ?>
</p>

<h3>Thanks to: (in no particular order)</h3>
<p>
<a href="http://otland.net/members/chris.13882/">Chris</a> - PHP OOP file samples, testing, bugfixing.
<br><a href="http://otland.net/members/kiwi-dan.152/">Kiwi Dan</a> - Researching TFS 0.2 for me, participation in developement.
<br><a href="http://otland.net/members/amoaz.26626/">Amoaz</a> - Pentesting and security tips.
<br><a href="http://otland.net/members/evan.40401/">Evan</a>, <a href="http://otland.net/members/gremlee.12075/">Gremlee</a> - Researching TFS 0.3, constructive feedback, suggestion and participation.
<br><a href="http://otland.net/members/att3.98289/">ATT3</a> - Reporting and fixing bugs, TFS 1.0 research. 
<br><a href="http://otland.net/members/mark.1/">Mark</a> - Old repository, TFS distributions which this AAC works against.
<br><a href="https://github.com/tedbro">Tedbro</a>, <a href="https://github.com/exura">Exura</a>, <a href="https://github.com/PrinterLUA">PrinterLUA</a> - Reporting bugs.
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

<?php
require_once 'engine/init.php';
protect_page();

if (empty($_POST) === false) {
	// $_POST['']
	/* Token used for cross site scripting security */
	if (!Token::isValid($_POST['token'])) {
		$errors[] = 'Token is invalid.';
	}
	$required_fields = array('new_email', 'new_flag');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}
	
	if (empty($errors) === true) {
		if (filter_var($_POST['new_email'], FILTER_VALIDATE_EMAIL) === false) {
			$errors[] = 'A valid email address is required.';
		} else if (user_email_exist($_POST['new_email']) === true && $user_data['email'] !== $_POST['new_email']) {
			$errors[] = 'That email address is already in use.';
		}
	}
}
?>
<h1>Settings</h1>
<?php
if (isset($_GET['success']) === true && empty($_GET['success']) === true) {
	echo 'Your settings have been updated.';
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		$update_data = array(
			'email' => $_POST['new_email'],
		);
		
		$update_znote_data = array(
			'flag' => getValue($_POST['new_flag']),
		);
		
		user_update_account($update_data);
		user_update_znote_account($update_znote_data);
		header('Location: settings.php?success');
		exit();
		
	} else if (empty($errors) === false) {
		echo output_errors($errors);
	}
	?>
	
	<form action="" method="post">
			<div class="RowsWithOverEffect" style="margin: 5px;">
				<div class="TableContainer"> 
				<div class="CaptionContainer">
					<div class="CaptionInnerContainer">
						<span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
						<span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
							<div class="Text">Account Settings</div>
						<span class="CaptionVerticalRight" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
						<span class="CaptionBorderBottom" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
						<span class="CaptionEdgeLeftBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionEdgeRightBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
					</div>
				</div>
				<table class="Table3" cellspacing="1">
							<tr>
								<td>
					<div class="InnerTableContainer">

								   <div class="TableShadowContainerRightTop">
									  <div class="TableShadowRightTop" style="background-image:url(layout/tibia_img/table-shadow-rt.gif);"></div>
								   </div>
								   <div class="TableContentAndRightShadow" style="background-image:url(layout/tibia_img/table-shadow-rm.gif);">
									  <div class="TableContentContainer">
										 <table class="TableContent" width="100%" style="border:1px solid #faf0d7;">
										<tr>
											<td>
												Email:
											</td>
											<td>
												<input type="text" name="new_email" value="<?php echo $user_data['email']; ?>">
											</td>
										</tr>
										<tr>
											<td>
												Country:
											</td>
											<td>
												<select name="new_flag" id="flag_select">
													<option value="">(Please choose)</option><option value="af"> Afghanistan </option><option value="al"> Albania </option><option value="dz"> Algeria </option><option value="as"> American Samoa </option><option value="ad"> Andorra </option><option value="ao"> Angola </option><option value="ai"> Anguilla </option><option value="aq"> Antarctica </option><option value="ag"> Antigua and Barbuda </option><option value="ar"> Argentina </option>
													<option value="am"> Armenia </option><option value="aw"> Aruba </option><option value="au"> Australia </option><option value="at"> Austria </option><option value="az"> Azerbaijan </option><option value="bs"> Bahamas </option><option value="bh"> Bahrain </option><option value="bd"> Bangladesh </option><option value="bb"> Barbados </option><option value="by"> Belarus </option><option value="be"> Belgium </option><option value="bz"> Belize </option><option value="bj"> Benin </option><option value="bm"> Bermuda </option><option value="bt"> Bhutan </option><option value="bo"> Bolivia </option><option value="ba"> Bosnia and Herzegowina </option><option value="bw"> Botswana </option><option value="bv"> Bouvet Island </option><option value="br"> Brazil </option><option value="io"> British Indian Ocean Territory </option><option value="bn"> Brunei Darussalam </option><option value="bg"> Bulgaria </option><option value="bf"> Burkina Faso </option><option value="bi"> Burundi </option>
													<option value="kh"> Cambodia </option><option value="cm"> Cameroon </option><option value="ca"> Canada </option><option value="cv"> Cape Verde </option><option value="ky"> Cayman Islands </option><option value="cf"> Central African Republic </option><option value="td"> Chad </option><option value="cl"> Chile </option><option value="cn"> China </option><option value="cx"> Christmas Island </option><option value="cc"> Cocos Islands </option><option value="co"> Colombia </option><option value="km"> Comoros </option><option value="cd"> Congo </option><option value="cg"> Congo </option><option value="ck"> Cook Islands </option><option value="cr"> Costa Rica </option><option value="ci"> Cote DIvoire </option><option value="hr"> Croatia </option><option value="cu"> Cuba </option><option value="cy"> Cyprus </option><option value="cz"> Czech Republic </option><option value="dk"> Denmark </option><option value="dj"> Djibouti </option><option value="dm"> Dominica </option>
													<option value="do"> Dominican Republic </option><option value="tp"> East Timor </option><option value="ec"> Ecuador </option><option value="eg"> Egypt </option><option value="sv"> El Salvador </option><option value="gq"> Equatorial Guinea </option><option value="er"> Eritrea </option><option value="ee"> Estonia </option><option value="et"> Ethiopia </option><option value="fk"> Falkland Islands </option><option value="fo"> Faroe Islands </option><option value="fj"> Fiji </option><option value="fi"> Finland </option><option value="fr"> France </option><option value="gf"> French Guiana </option><option value="pf"> French Polynesia </option><option value="tf"> French Southern Territories </option><option value="ga"> Gabon </option><option value="gm"> Gambia </option><option value="ge"> Georgia </option><option value="de"> Germany </option><option value="gh"> Ghana </option><option value="gi"> Gibraltar </option><option value="gr"> Greece </option>
													<option value="gl"> Greenland </option><option value="gd"> Grenada </option><option value="gp"> Guadeloupe </option><option value="gu"> Guam </option><option value="gt"> Guatemala </option><option value="gn"> Guinea </option><option value="gw"> Guinea-Bissau </option><option value="gy"> Guyana </option><option value="ht"> Haiti </option><option value="hm"> Heard and Mc Donald Islands </option><option value="hn"> Honduras </option><option value="hk"> Hong Kong </option><option value="hu"> Hungary </option><option value="is"> Iceland </option><option value="in"> India </option><option value="id"> Indonesia </option><option value="ir"> Iran </option><option value="iq"> Iraq </option><option value="ie"> Ireland </option><option value="il"> Israel </option><option value="it"> Italy </option><option value="jm"> Jamaica </option><option value="jp"> Japan </option><option value="jo"> Jordan </option><option value="kz"> Kazakhstan </option><option value="ke"> Kenya </option>
													<option value="ki"> Kiribati </option><option value="kr"> Korea </option><option value="kp"> Korea </option><option value="kw"> Kuwait </option><option value="kg"> Kyrgyzstan </option><option value="la"> Lao Peoples Democratic Republic </option><option value="lv"> Latvia </option><option value="lb"> Lebanon </option><option value="ls"> Lesotho </option><option value="lr"> Liberia </option><option value="ly"> Libyan Arab Jamahiriya </option><option value="li"> Liechtenstein </option><option value="lt"> Lithuania </option><option value="lu"> Luxembourg </option><option value="mo"> Macau </option><option value="mk"> Macedonia </option><option value="mg"> Madagascar </option><option value="mw"> Malawi </option><option value="my"> Malaysia </option><option value="mv"> Maldives </option><option value="ml"> Mali </option><option value="mt"> Malta </option><option value="mh"> Marshall Islands </option><option value="mq"> Martinique </option>
													<option value="mr"> Mauritania </option><option value="mu"> Mauritius </option><option value="yt"> Mayotte </option><option value="mx"> Mexico </option><option value="fm"> Micronesia </option><option value="md"> Moldova </option><option value="mc"> Monaco </option><option value="mn"> Mongolia </option><option value="ms"> Montserrat </option><option value="ma"> Morocco </option><option value="mz"> Mozambique </option><option value="mm"> Myanmar </option><option value="na"> Namibia </option><option value="nr"> Nauru </option><option value="np"> Nepal </option><option value="nl"> Netherlands </option><option value="an"> Netherlands Antilles </option><option value="nc"> New Caledonia </option><option value="nz"> New Zealand </option><option value="ni"> Nicaragua </option><option value="ne"> Niger </option><option value="ng"> Nigeria </option><option value="nu"> Niue </option><option value="nf"> Norfolk Island </option><option value="mp"> Northern Mariana Islands </option>
													<option value="no"> Norway </option><option value="om"> Oman </option><option value="pk"> Pakistan </option><option value="pw"> Palau </option><option value="pa"> Panama </option><option value="pg"> Papua New Guinea </option><option value="py"> Paraguay </option><option value="pe"> Peru </option><option value="ph"> Philippines </option><option value="pn"> Pitcairn </option><option value="pl"> Poland </option><option value="pt"> Portugal </option><option value="pr"> Puerto Rico </option><option value="qa"> Qatar </option><option value="re"> Reunion </option><option value="ro"> Romania </option><option value="ru"> Russian Federation </option><option value="rw"> Rwanda </option><option value="kn"> Saint Kitts and Nevis </option><option value="lc"> Saint Lucia </option><option value="ws"> Samoa </option><option value="sm"> San Marino </option><option value="st"> Sao Tome and Principe </option><option value="sa"> Saudi Arabia </option><option value="sn"> Senegal </option>
													<option value="sc"> Seychelles </option><option value="sl"> Sierra Leone </option><option value="sg"> Singapore </option><option value="sk"> Slovakia </option><option value="si"> Slovenia </option><option value="sb"> Solomon Islands </option><option value="so"> Somalia </option><option value="za"> South Africa </option><option value="es"> Spain </option><option value="lk"> Sri Lanka </option><option value="sh"> St. Helena </option><option value="pm"> St. Pierre and Miquelon </option><option value="sd"> Sudan </option><option value="sr"> Suriname </option><option value="sj"> Svalbard and Jan Mayen Islands </option><option value="sz"> Swaziland </option><option value="se"> Sweden </option><option value="ch"> Switzerland </option><option value="sy"> Syrian Arab Republic </option><option value="tw"> Taiwan </option><option value="tj"> Tajikistan </option><option value="tz"> Tanzania </option>
													<option value="th"> Thailand </option><option value="tg"> Togo </option><option value="tk"> Tokelau </option><option value="to"> Tonga </option><option value="tt"> Trinidad and Tobago </option><option value="tn"> Tunisia </option><option value="tr"> Turkey </option><option value="tm"> Turkmenistan </option><option value="tc"> Turks and Caicos Islands </option><option value="tv"> Tuvalu </option><option value="ug"> Uganda </option><option value="ua"> Ukraine </option><option value="ae"> United Arab Emirates </option><option value="gb"> United Kingdom </option><option value="us"> United States </option><option value="uy"> Uruguay </option><option value="uz"> Uzbekistan </option><option value="vu"> Vanuatu </option><option value="va"> Vatican </option><option value="ve"> Venezuela </option><option value="vn"> Viet Nam </option><option value="vg"> Virgin Islands (British) </option><option value="vi"> Virgin Islands (US) </option>
													<option value="wf"> Wallis and Futuna Islands </option><option value="eh"> Western Sahara </option><option value="ye"> Yemen </option><option value="yu"> Yugoslavia </option><option value="zm"> Zambia </option><option value="zw"> Zimbabwe </option>
												</select>
											</td>
										</tr>
										 </table>
										 
									  </div>
								   </div>
								   <div class="TableShadowContainer">
									  <div class="TableBottomShadow" style="background-image:url(layout/tibia_img/table-shadow-bm.gif);">
										 <div class="TableBottomLeftShadow" style="background-image:url(layout/tibia_img/table-shadow-bl.gif);"></div>
										 <div class="TableBottomRightShadow" style="background-image:url(layout/tibia_img/table-shadow-br.gif);"></div>
									  </div>
								   </div>
								   
								   <br>
								   
								   <center>
											<input name="submit" style="margin: 0 5px;display: inline-block;" class="BigButton btn" value="Update settings" type="submit" >

											<a href="myaccount.php"><div class="BigButton btn" style="margin: 0 5px;display: inline-block;background-image:url(layout/tibia_img/sbutton.gif)">
												Back
											</div>
										
										</a>
								   </center>
								   <br>

	
					 	</div> 

						
						</td>
						</tr>
						
						</table>
				


							
					</div>
				</div>
				
				
				
			<?php
				/* Form file */
				Token::create();
			?>
	</form>
	<script>
		function selectCurrentFlag(flag) {
			document.getElementById("flag_select").value = flag != null ? flag : "";
		}
		<?php echo "selectCurrentFlag('".$user_znote_data['flag']."');"; ?>
	</script>
<?php
}
?>
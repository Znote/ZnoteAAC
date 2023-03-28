<?php require_once 'engine/init.php';
protect_page();

if (empty($_POST) === false) {
	// $_POST['']
	$required_fields = array('name', 'selected_town');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}
	
	// check errors (= user exist, pass long enough
	if (empty($errors) === true) {
		if (!Token::isValid($_POST['token'])) {
			$errors[] = 'Token is invalid.';
		}
		$_POST['name'] = validate_name($_POST['name']);
		if ($_POST['name'] === false) {
			$errors[] = 'Your name can not contain more than 2 words.';
		} else {
			if (user_character_exist($_POST['name']) !== false) {
				$errors[] = 'Sorry, that character name already exist.';
			}
			if (!preg_match("/^[a-zA-Z_ ]+$/", $_POST['name'])) {
				$errors[] = 'Your name may only contain a-z, A-Z and spaces.';
			}
			if (strlen($_POST['name']) < $config['minL'] || strlen($_POST['name']) > $config['maxL']) {
				$errors[] = 'Your character name must be between ' . $config['minL'] . ' - ' . $config['maxL'] . ' characters long.';
			}
			// name restriction
			$resname = explode(" ", $_POST['name']);
			foreach($resname as $res) {
				if(in_array(strtolower($res), $config['invalidNameTags'])) {
					$errors[] = 'Your username contains a restricted word.';
				}
				else if(strlen($res) == 1) {
					$errors[] = 'Too short words in your name.';
				}
			}
			// Validate vocation id
			if (!in_array((int)$_POST['selected_vocation'], $config['available_vocations'])) {
				$errors[] = 'Permission Denied. Wrong vocation.';
			}
			// Validate town id
			if (!in_array((int)$_POST['selected_town'], $config['available_towns'])) {
				$errors[] = 'Permission Denied. Wrong town.';
			}
			// Validate gender id
			if (!in_array((int)$_POST['selected_gender'], array(0, 1))) {
				$errors[] = 'Permission Denied. Wrong gender.';
			}
			if (vocation_id_to_name($_POST['selected_vocation']) === false) {
				$errors[] = 'Failed to recognize that vocation, does it exist?';
			}
			if (town_id_to_name($_POST['selected_town']) === false) {
				$errors[] = 'Failed to recognize that town, does it exist?';
			}
			if (gender_exist($_POST['selected_gender']) === false) {
				$errors[] = 'Failed to recognize that gender, does it exist?';
			}
			// Char count
			$char_count = user_character_list_count($session_user_id);
			if ($char_count >= $config['max_characters']) {
				$errors[] = 'Your account is not allowed to have more than '. $config['max_characters'] .' characters.';
			}
			if (validate_ip(getIP()) === false && $config['validate_IP'] === true) {
				$errors[] = 'Failed to recognize your IP address. (Not a valid IPv4 address).';
			}
		}
	}
}
?>
<?php
if (isset($_GET['success']) && empty($_GET['success'])) {
	echo 'Congratulations! Your character has been created. See you in-game!';
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		if ($config['log_ip']) {
			znote_visitor_insert_detailed_data(2);
		}
		//Register
		$character_data = array(
			'name'		=>	format_character_name($_POST['name']),
			'account_id'=>	$session_user_id,
			'vocation'	=>	$_POST['selected_vocation'],
			'town_id'	=>	$_POST['selected_town'],
			'sex'		=>	$_POST['selected_gender'],
			'lastip'	=>	getIPLong(),
			'created'	=>	time()
		);
		
		user_create_character($character_data);
		header('Location: createcharacter.php?success');
		exit();
		//End register
		
	} else if (empty($errors) === false){
		echo '<font color="red"><b>';
		echo output_errors($errors);
		echo '</b></font>';
	}
	?>
	
	<p style="margin: 5px 5px 10px 5px;">
	Please choose a name and sex for your character.<br>
	In any case the name must not violate the naming conventions stated in the Rules, or your character might get deleted or name locked.</p>
		<form action="" method="post">
		
			<div class="RowsWithOverEffect" style="margin: 5px;">
				<div class="TableContainer"> 
				<div class="CaptionContainer">
					<div class="CaptionInnerContainer">
						<span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
						<span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
							<div class="Text">Create Character</div>
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

										<tr class="LabelH">
											<td colspan="2">
												Character Name
											</td>
											<td>
												Sex
											</td>
											<td>
												Town
											</td>
										</tr>
										
										<tr>
											<td>
												Name: 
											</td>
											<td>
												<input type="text" name="name">
											</td>
											<td>
												<select name="selected_gender">
												<option value="1">Male(boy)</option>
												<option value="0">Female(girl)</option>
												</select>
											</td>
											<td>
												<?php
												$available_towns = $config['available_towns'];
												if (count($available_towns) > 1):
													?>
														<select name="selected_town">
															<?php 
															foreach ($available_towns as $tid): 
																?>
																<option value="<?php echo $tid; ?>"><?php echo town_id_to_name($tid); ?></option>
																<?php 
															endforeach; 
															?>
														</select>
													<?php
												else:
													?>
													<input type="hidden" name="selected_town" value="<?php echo end($available_towns); ?>">
													<?php 
												endif;
												?>
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
								   
								   <div class="TableShadowContainerRightTop">
									  <div class="TableShadowRightTop" style="background-image:url(layout/tibia_img/table-shadow-rt.gif);"></div>
								   </div>
								   <div class="TableContentAndRightShadow" style="background-image:url(layout/tibia_img/table-shadow-rm.gif);">
									  <div class="TableContentContainer">
										 <table class="TableContent" width="100%" style="border:1px solid #faf0d7;">

										<tr class="LabelH">
											<td colspan="2">
												Select vocation
											</td>
										</tr>
										
										<tr>
											<td>
												<?php foreach ($config['available_vocations'] as $id) { ?>
												
												<div style="width: 25%; padding: 20px 0;float: left;text-align: left;">
													<input type="radio" name="selected_vocation" id="<?php echo $id; ?>" value="<?php echo $id; ?>">
													<label for="<?php echo $id; ?>"><?php echo vocation_id_to_name($id); ?></label>
												</div>

												<?php } ?>

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
											<input name="submit" class="BigButton btn" type="submit" style="margin: 0 5px;display: inline-block;" value="Create Character">

										<a href="myaccount.php">
											<div class="BigButton btn" style="margin: 0 5px;display: inline-block;background-image:url(layout/tibia_img/sbutton.gif)">
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
	<?php
}
?>
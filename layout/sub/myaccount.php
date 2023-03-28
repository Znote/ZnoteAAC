<?php require_once 'engine/init.php';
protect_page();

#region CANCEL CHARACTER DELETE
$undelete_id = @$_GET['cancel_delete_id'];
if($undelete_id) {
	$undelete_id = (int)$undelete_id;
	$undelete_q1 = mysql_select_single('SELECT `character_name` FROM `znote_deleted_characters` WHERE `done` = 0 AND `id` = ' . $undelete_id . ' AND `original_account_id` = ' . $session_user_id . ' AND NOW() < `time`');
	if($undelete_q1) {
		mysql_delete('DELETE FROM `znote_deleted_characters` WHERE `id` = ' . $undelete_id);
		echo 'Pending delete of ' . $undelete_q1['character_name'] . ' has been successfully cancelled.<br/>';
	}
}
#endregion

// Variable used to check if main page should be rendered after handling POST (Change comment page)
$render_page = true;

// Handle POST
if (!empty($_POST['selected_character'])) {
	if (!empty($_POST['action'])) {
		// Validate token
		if (!Token::isValid($_POST['token'])) {
			exit();
		}
		// Sanitize values
		$action = getValue($_POST['action']);
		$char_name = getValue($_POST['selected_character']);

		// Handle actions
		switch($action) {
			// Change character comment PAGE2 (Success).
			case 'update_comment':
				if (user_character_account_id($char_name) === $session_user_id) {
					user_update_comment(user_character_id($char_name), getValue($_POST['comment']));
					echo 'Successfully updated comment.';
				}
				break;
			// end

			// Hide character
			case 'toggle_hide':
				$hide = (user_character_hide($char_name) == 1 ? 0 : 1);
				if (user_character_account_id($char_name) === $session_user_id) {
					user_character_set_hide(user_character_id($char_name), $hide);
				}
				break;
			// end

			// DELETE character
			case 'delete_character':
				if (user_character_account_id($char_name) === $session_user_id) {
					$charid = user_character_id($char_name);
					if ($charid !== false) {
						if ($config['TFSVersion'] === 'TFS_10') {
							if (!user_is_online_10($charid)) {
								if (guild_leader_gid($charid) === false) user_delete_character_soft($charid);
								else echo 'Character is leader of a guild, you must disband the guild or change leadership before deleting character.';
							} else echo 'Character must be offline first.';
						} else {
							$chr_data = user_character_data($charid, 'online');
							if ($chr_data['online'] != 1) {
								if (guild_leader_gid($charid) === false) user_delete_character_soft($charid);
								else echo 'Character is leader of a guild, you must disband the guild or change leadership before deleting character.';
							} else echo 'Character must be offline first.';
						}
					}
				}
				break;
			// end

			// CHANGE character name
			case 'change_name':
				$oldname = $char_name;
				$newname = isset($_POST['newName']) ? getValue($_POST['newName']) : '';

				$player = false;
				if ($config['TFSVersion'] === 'TFS_10') {
					$player = mysql_select_single("SELECT `id`, `account_id` FROM `players` WHERE `name` = '$oldname'");
					$player['online'] = (user_is_online_10($player['id'])) ? 1 : 0;
				} else $player = mysql_select_single("SELECT `id`, `account_id`, `online` FROM `players` WHERE `name` = '$oldname'");

				// Check if user is online
				if ($player['online'] == 1) {
					$errors[] = 'Character must be offline first.';
				}

				// Check if player has bough ticket
				$accountId = $player['account_id'];
				$order = mysql_select_single("SELECT `id`, `account_id` FROM `znote_shop_orders` WHERE `type`='4' AND `account_id` = '$accountId' LIMIT 1;");
				if ($order === false) {
					$errors[] = 'Did not find any name change tickets, buy them in our <a href="shop.php">shop!</a>';
				}

				// Check if player and account matches
				if ($session_user_id != $accountId || $session_user_id != $order['account_id']) {
					$errors[] = 'Failed to sync your account. :|';
				}

				$newname = validate_name($newname);
				if ($newname === false) {
					$errors[] = 'Your name can not contain more than 2 words.';
				} else {
					if (empty($newname)) {
						$errors[] = 'Please enter a name!';
					} else if (user_character_exist($newname) !== false) {
						$errors[] = 'Sorry, that character name already exist.';
					} else if (!preg_match("/^[a-zA-Z_ ]+$/", $newname)) {
						$errors[] = 'Your name may only contain a-z, A-Z and spaces.';
					} else if (strlen($newname) < $config['minL'] || strlen($newname) > $config['maxL']) {
						$errors[] = 'Your character name must be between ' . $config['minL'] . ' - ' . $config['maxL'] . ' characters long.';
					} else if (!ctype_upper($newname{0})) {
						$errors[] = 'The first letter of a name has to be a capital letter!';
					}

					// name restriction
					$resname = explode(" ", $_POST['newName']);
					foreach($resname as $res) {
						if(in_array(strtolower($res), $config['invalidNameTags'])) {
							$errors[] = 'Your username contains a restricted word.';
						} else if(strlen($res) == 1) {
							$errors[] = 'Too short words in your name.';
						}
					}
				}

				if (!empty($newname) && empty($errors)) {
					echo 'You have successfully changed your character name to ' . $newname . '.';
					mysql_update("UPDATE `players` SET `name`='$newname' WHERE `id`='".$player['id']."' LIMIT 1;");
					mysql_delete("DELETE FROM `znote_shop_orders` WHERE `id`='".$order['id']."' LIMIT 1;");

				} else if (!empty($errors)) {
					echo '<font color="red"><b>';
					echo output_errors($errors);
					echo '</b></font>';
				}

				break;
			// end

			// Change character sex
			case 'change_gender':
				if (user_character_account_id($char_name) === $session_user_id) {
					$char_id = (int)user_character_id($char_name);
					$account_id = user_character_account_id($char_name);

					if ($config['TFSVersion'] == 'TFS_10') {
						$chr_data['online'] = user_is_online_10($char_id) ? 1 : 0;
					} else $chr_data = user_character_data($char_id, 'online');
					if ($chr_data['online'] != 1) {
						// Verify that we are not messing around with data
						if ($account_id != $user_data['id']) die("wtf? Something went wrong, try relogging.");

						// Fetch character tickets
						$tickets = shop_account_gender_tickets($account_id);
						if ($tickets !== false || $config['free_sex_change'] == true) {
							// They are allowed to change gender
							$last = false;
							$infinite = false;
							$tks = 0;
							// Do we have any infinite tickets?
							foreach ($tickets as $ticket) {
								if ($ticket['count'] == 0) $infinite = true;
								else if ($ticket > 0 && $infinite === false) $tks += (int)$ticket['count'];
							}
							if ($infinite === true) $tks = 0;
							$dbid = (int)$tickets[0]['id'];
							// If they dont have unlimited tickets, remove a count from their ticket.
							if ($tickets[0]['count'] > 1) { // Decrease count
								$tks--;
								$tkr = ((int)$tickets[0]['count'] - 1);
								shop_update_row_count($dbid, $tkr);
							} else if ($tickets[0]['count'] == 1) { // Delete record
								shop_delete_row_order($dbid);
								$tks--;
							}

							// Change character gender:
							//
							user_character_change_gender($char_name);
							echo 'You have successfully changed gender on character '. $char_name .'.';
							if ($tks > 0) echo '<br>You have '. $tks .' gender change tickets left.';
							else if ($infinite !== true) echo '<br>You are out of tickets.';
						} else echo 'You don\'t have any character gender tickets, buy them in the <a href="shop.php">SHOP</a>!';
					} else echo 'Your character must be offline.';
				}
				break;
			// end

			// Change character comment PAGE1:
			case 'change_comment':
				$render_page = false; // Regular "myaccount" page should not render
				if (user_character_account_id($char_name) === $session_user_id) {
					$comment_data = user_znote_character_data(user_character_id($char_name), 'comment');
					?>
					<!-- Changing comment MARKUP -->
					<h1>Change comment on:</h1>
					<form action="" method="post">
						<ul>
							<li>
								<input name="action" type="hidden" value="update_comment">
								<input name ="selected_character" type="text" value="<?php echo $char_name; ?>" readonly="readonly">
							</li>
							<li>
								<font class="profile_font" name="profile_font_comment">Comment:</font> <br>
								<textarea name="comment" cols="70" rows="10"><?php echo $comment_data['comment']; ?></textarea>
							</li>
							<?php
								/* Form file */
								Token::create();
							?>
							<li><input type="submit" value="Update Comment"></li>
						</ul>
					</form>
					<?php
				}
				break;
			//end
		}
	}
}

if ($render_page) {
	$char_count = user_character_list_count($session_user_id);
	$pending_delete = user_pending_deletes($session_user_id);
	if ($pending_delete) {
		foreach($pending_delete as $delete) {
			if(new DateTime($delete['time']) > new DateTime())
				echo '<b>CAUTION!</b> Your character with name <b>' . $delete['character_name'] . ' will be deleted on ' . $delete['time'] . '</b>. <a href="myaccount.php?cancel_delete_id=' . $delete['id'] . '">Cancel this operation.</a><br/>';
			else {
				user_delete_character(user_character_id($delete['character_name']));
				mysql_update('UPDATE `znote_deleted_characters` SET `done` = 1 WHERE `id` = '. $delete['id']. '');
				echo '<b>Character ' . $delete['character_name'] . ' has been deleted</b>. This operation was requested by owner of this account.';
				$char_count--;
			}
		}
	}

	?>
	<div id="myaccount">
		<center><table style="width: auto;"><tr><td style="background: transparent;"><img src="layout/tibia_img/headline-bracer-left.gif"></td><td  style="background: transparent;text-align:center;vertical-align:middle;horizontal-align:center;font-size:17px;font-weight:bold;">Welcome to your account, <?php echo $user_data['name']; ?>!<br></td><td style="background: transparent;"><img src="layout/tibia_img/headline-bracer-right.gif"></td></tr></table></center>
		<center><p>You have <?php echo $user_data['premdays']; ?> days remaining premium account.</p></center>
		<?php
		if ($config['TFSVersion'] === 'TFS_10' && $config['twoFactorAuthenticator']) {

			$query = mysql_select_single("SELECT `secret` FROM `accounts` WHERE `id`='".(int)$session_user_id."' LIMIT 1;");
			$status = ($query['secret'] === NULL) ? false : true;
			?><p>Account security with Two-factor Authentication: <a href="twofa.php"><?php echo ($status) ? 'Enabled' : 'Disabled'; ?></a></p><?php
		}
		?>
		<?php
		// Echo character list!
		$char_array = user_character_list($user_data['id']);
		// Design and present the list
		if ($char_array) {
			?>
			<div class="RowsWithOverEffect" style="margin: 5px;">
				<div class="TableContainer"> 
				<div class="CaptionContainer">
					<div class="CaptionInnerContainer">
						<span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
						<span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
							<div class="Text">Character List: <?php echo $char_count; ?> characters.</div>
						<span class="CaptionVerticalRight" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
						<span class="CaptionBorderBottom" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
						<span class="CaptionEdgeLeftBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionEdgeRightBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
					</div>
				</div>
				<table class="Table3" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<div class="InnerTableContainer">
								<table style="width:100%;">
									<tr>
										<td>
											<div class="TableShadowContainerRightTop">
												<div class="TableShadowRightTop" style="background-image:url(layout/tibia_img/table-shadow-rt.gif);"></div>
											</div>
											<div class="TableContentAndRightShadow" style="background-image:url(layout/tibia_img/table-shadow-rm.gif);"> 
												<div class="TableContentContainer">
													<table class="TableContent" width="100%" style="border:1px solid #faf0d7;">
														<tr class="LabelH">
															<td style="width:40%;">Name</td>
															<td style="width:120px!important;">Town</td>
															<td style="width:90px!important;">Status</td>
															<td style="width:90px!important;">Visibility</td>
														</tr>
														
															<?php
															$characters = array();
															foreach ($char_array as $value) {
																// characters: [0] = name, [1] = level, [2] = vocation, [3] = town_id, [4] = lastlogin, [5] = online
																echo '<tr class="CharacterList" style="font-weight: bold; background-color: rgb(241, 224, 198);">
															<td id="CharacterCell2_1">
																<span style="white-space:nowrap;vertical-align:middle;line-height:12px;">
																<span id="CharacterNameOf_1" style="font-size:13pt;"><a style="font-weight: bold;" href="characterprofile.php?name='. $value['name'] .'">'. $value['name'] .'</a></span>
																<br><span id="CharacterNameOf_1"><small>'. $value['vocation'] .' - level '. $value['level'] .'</small></span></span>
															</td>
															<td id="CharacterCell2_1"><span style="white-space:nowrap;">'. $value['town_id'] .'</span></td>
															<td id="CharacterCell3_1">'. $value['online'] .'</td>
															<td id="CharacterCell4_1" style="text-align:center;">
																'. hide_char_to_name(user_character_hide($value['name'])) .'
															</td></tr>';
																$characters[] = $value['name'];
															}
															?>
														 
													</table>
												</div>
											</div>
											<div class="TableShadowContainer"> 
												<div class="TableBottomShadow" style="background-image:url(layout/tibia_img/table-shadow-bm.gif);">
													<div class="TableBottomLeftShadow" style="background-image:url(layout/tibia_img/table-shadow-bl.gif);"></div>
													<div class="TableBottomRightShadow" style="background-image:url(layout/tibia_img/table-shadow-br.gif);"></div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<table class="InnerTableButtonRow" cellpadding="0" cellspacing="0">
												<tr>
													<td></td>
													<td align="right" style="padding-right:7px;width:100%;">
														<form action="https://secure.tibia.com/account/?subtopic=accountmanagement&amp;page=createcharacter" method="post" style="padding:0px;margin:0px;">
															<input type="hidden" name="selectedcharacter" value="Aerohe">
																<a href="createcharacter.php">
																	<div class="BigButton btn" style="margin: 0 5px;float: right;background-image:url(layout/tibia_img/sbutton.gif)">
																			Create Character
																	</div>
																</a>
																<a href="changepassword.php">
																	<div class="BigButton btn" style="margin: 0 5px;float: right;background-image:url(layout/tibia_img/sbutton.gif)">
																			Change Password
																	</div>
																</a>
																
																<a href="settings.php">
																	<div class="BigButton btn" style="margin: 0 5px;float: right;background-image:url(layout/tibia_img/sbutton.gif); ">
																		Settings
																	</div>
																</a>
														</form>
													</td>
												</tr>
											</table>
										</td>
									</tr> 
								</table>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>	
			<br>
			<!-- FORMS TO EDIT CHARACTER-->
			<form action="" method="post">
		<div class="RowsWithOverEffect" style="margin: 5px;">
			<div class="TableContainer"> 
				<div class="CaptionContainer">
					<div class="CaptionInnerContainer">
						<span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
						<span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
							<div class="Text">Manage characters</div>
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
					   <table style="width:100%;">
							 <tr>
								<td>
								   <div class="TableShadowContainerRightTop">
									  <div class="TableShadowRightTop" style="background-image:url(layout/tibia_img/table-shadow-rt.gif);"></div>
								   </div>
								   <div class="TableContentAndRightShadow" style="background-image:url(layout/tibia_img/table-shadow-rm.gif);">
									  <div class="TableContentContainer">
										 <table class="TableContent" width="100%" style="border:1px solid #faf0d7;">

										<tr>
											<td>
												<select id="selected_character" name="selected_character" class="form-control">
												<?php
												for ($i = 0; $i < $char_count; $i++) {
													if (user_character_hide($characters[$i]) == 1) {
														echo '<option value="'. $characters[$i] . '">'. $characters[$i] .'</option>'; 	
													} else {
														echo '<option value="'. $characters[$i] . '">'. $characters[$i] .'</option>'; 	
													}
												}
												?>
												</select>
											</td>
											<td>
												<select id="action" name="action" class="form-control" onChange="changedOption(this)">
													<option value="none" selected>Select action</option>
													<option value="toggle_hide">Toggle hide</option>
													<option value="change_comment">Change comment</option>
													<option value="change_gender">Change gender</option>
													<option value="change_name">Change name</option>
													<option value="delete_character" class="needconfirmation">Delete character</option>
												</select>
											</td>
											<td id="submit_form">
												<?php
													/* Form file */
													Token::create();
												?>
												<input id="submit_button" type="submit" value="Submit" class="btn btn-primary btn-block"></input>
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
								</td>
							 </tr>
					   </table>
					</div>
					</td>
					</tr>
					
				</table>
			</div>
		</div>
			</form>
			<?php
		} else {
			echo '<center><h2>You don\'t have any characters. Why don\'t you <a href="createcharacter.php">create one</a>?</h2></center>';
		}
		?>
	</div>
	<script>
		function changedOption(e) {
			// If selection is 'Change name' add a name field in the form
			// Else remove name field if it exists
			if (e.value == 'change_name') {
				var lastCell = document.getElementById('submit_form');
				var x = document.createElement('TD');
				x.id = "new_name";
				x.innerHTML = '<input type="text" name="newName" placeholder="New Name" class="form-control">';
				lastCell.parentNode.insertBefore(x, lastCell);
			} else {
				var child = document.getElementById('new_name');
				if (child) {
					child.parentNode.removeChild(child);
				}
			}
		}
	</script>
	<script>
		$(document).ready(function(){
			$("#submit_button").click(function(e){
				if ($("#action").find(":selected").attr('class') == "needconfirmation") {
					var r = confirm("Do you really want to DELETE character: "+$('#selected_character').find(":selected").text()+"?")
					if (r == false) {
						e.preventDefault();
					}
				}
			});
		});
	</script>
	<?php
}

?>
<?php require_once 'engine/init.php';
 
if ($config['log_ip']) 
{
	znote_visitor_insert_detailed_data(4);
}

if (isset($_GET['name']) === true && empty($_GET['name']) === false) 
{
	$name = getValue($_GET['name']);
	$user_id = user_character_exist($name);
	
	if ($user_id !== false) 
	{	
		if ($config['TFSVersion'] == 'TFS_10') 
		{
			$profile_data = user_character_data($user_id, 'account_id', 'town_id', 'name', 'level', 'group_id', 'vocation', 'health', 'healthmax', 'experience', 'mana', 'manamax', 'sex', 'lastlogin', 'looktype', 'lookhead', 'lookbody', 'looklegs', 'lookfeet', 'exphist1', 'exphist2', 'exphist3', 'exphist4', 'exphist5', 'exphist6', 'exphist7');
			$profile_data['online'] = user_is_online_10($user_id);
			
			if ($config['Ach']) 
			{
				$achievementPoints = mysql_select_single("SELECT SUM(`value`) AS `sum` FROM `player_storage` WHERE `key` LIKE '30___' AND `player_id`=(int)$user_id");
			}
			
		} 
		else 
		{
			$profile_data = user_character_data($user_id, 'name', 'account_id', 'town_id', 'level', 'group_id', 'vocation', 'health', 'healthmax', 'experience', 'mana', 'manamax', 'lastlogin', 'online', 'sex', 'looktype', 'lookhead', 'lookbody', 'looklegs', 'lookfeet', 'exphist1', 'exphist2', 'exphist3', 'exphist4', 'exphist5', 'exphist6', 'exphist7');
		}
		
		$profile_znote_data = user_znote_character_data($user_id, 'created', 'hide_char', 'comment');
		$account_data = user_znote_account_data($profile_data['account_id'], 'flag');
		
		$guild_exist = false;
		
		if (get_character_guild_rank($user_id) > 0) 
		{
			$guild_exist = true;
			$guild = get_player_guild_data($user_id);
			$guild_name = get_guild_name($guild['guild_id']);
		}
		
		?>
		
		<!-- PROFILE MARKUP HERE-->
		
		<!-- Profile name -->
		<table class="stripped" style="width: 100%;" cellpadding="4">
		<tr><th colspan="3">Character Information</th></tr>
		
		
				<!-- Player country and name -->
				<tr><td width="25%"><strong>Name:</strong></td><td><?php echo $profile_data['name']; ?>
				<?php 
				if ($config['country_flags'])
				{ 
					 echo '<img src="flags/' . $account_data['flag'] . '.png">'; 
					
				} ?>
				</td>
				<td rowspan="2" style="width: 48px;">
				<?php 
				echo '
				<div style="position:relative; left:-25px; top:-48px;">
					<div style="background-image: url(layout/outfitter/outfit.php?id='.$profile_data['looktype'].'&head='.$profile_data['lookhead'].'&body='.$profile_data['lookbody'].'&legs='.$profile_data['looklegs'].'&feet='.$profile_data['lookfeet'].');
						width:64px;height:64px;position:absolute;background-repeat:no-repeat;background-position:right bottom;">
					</div>
				</td>';
				?>
				</tr>
				
				<!-- Player Profession: -->
				<tr><td><strong>Profession:</strong></td><td><?php echo vocation_id_to_name($profile_data['vocation']); ?></td></tr>
				
				<!-- Player level -->
				<tr>
					<td><strong>Level:</strong></td><td colspan="2">
						<?php 
						echo $profile_data['level']; 
						?>
					</td>
				</tr>
				
				<tr>
					<td><strong>Residence:</strong></td><td colspan="2">
						<?php 
							foreach ($config['towns'] as $key => $value) 
							{
								if ($key == $profile_data['town_id']) 
								{
									echo $value;
								}
							}
						?>
					</td>
				</tr>	
				<tr>
					<td><strong>Account Status:</strong></td><td colspan="2">
						<?php 
							$acc_id = $profile_data['account_id'];
							 $premdays = mysql_select_single("SELECT `premdays` FROM `accounts` WHERE `id` = '$acc_id'");
							if($premdays['premdays'] > 0)
								echo '<strong><span style="color: green;">Premium Account</span></strong>';
							else
								echo 'Free Account';
						?>
					</td>
				</tr>	
				
				<!-- Player male / female -->
				<tr>
					<td><strong>Sex:</strong></td><td colspan="2">
						<?php 
						if ($profile_data['sex'] == 1) 
						{
							echo 'Male';
						} 
						else 
						{
							echo 'Female';
						}
						?>
					</td>
				</tr>
				
				<!-- Player Position -->
				<?php if ($profile_data['group_id'] > 1) { ?>
				<tr><td><strong>Position:</strong></td><td colspan="2"><?php echo group_id_to_name($profile_data['group_id']); ?></td></tr>
				<?php } ?>
				
				
				<!-- Player guild -->
				<?php 
				if ($guild_exist) 
				{
				?>
				<td><strong>Guild Membership:</strong></td>
					<td colspan="2">
						<b><?php echo $guild['rank_name']; ?> </b> of <a href="guilds.php?name=<?php echo $guild_name; ?>"><?php echo $guild_name; ?></a>
					</td>
				</td>
				<?php
				}
				?>
				<!-- Player last login -->
				<tr>
					<td><strong>Last Login:</strong></td><td colspan="2">
					<?php
					if ($profile_data['lastlogin'] != 0) 
					{
						echo getClock($profile_data['lastlogin'], true, true);
					} 
					else 
					{
						echo 'Never.';
					}
					?>
					</td>
				</tr>
				
				<!-- Achievement start -->
				<?php 
				if ($config['Ach']) 
				{ 
					foreach ($achievementPoints as $achievement) 
					{
						//if player doesn't have any achievement points it won't echo the line below.
						if ($achievement > 0)
						{
							echo '<tr>Achievement Points:</td><td colspan="2">' . $achievement . '</td></tr>'; 
						}
					}
				}
				?>
				<!-- Achievement end -->
				
				<!-- Display house start -->
				<?php
				if ($config['TFSVersion'] !== 'TFS_02') 
				{
					$townid = ($config['TFSVersion'] === 'TFS_03') ? 'town' : 'town_id';
					$houses = mysql_select_multi("SELECT `id`, `owner`, `name`, `$townid` AS `town_id` FROM `houses` WHERE `owner` = $user_id;");
					
					if ($houses) 
					{
						$playerlist = array();
						foreach ($houses as $h) 
						{
							if ($h['owner'] > 0)
							{
								$playerlist[] = $h['owner'];
							}
								
							if ($profile_data['id'] = $h['owner']) 
							{
							?>
								<tr><td>House:</td><td colspan="2"><?php echo $h['name']; ?>, <?php 
									foreach ($config['towns'] as $key => $value) 
									{
										if ($key == $h['town_id']) 
										{
											echo $value;
										}
									}
							 ?>
								</td></tr>
							<?php
							}
						}
					}
				}
				?>
				<!-- Display house end -->
				
				<!-- Display player status -->
				<tr><td><strong>Status:</strong></td><td colspan="2"><?php
				if ($config['TFSVersion'] == 'TFS_10') 
				{
					if ($profile_data['online']) 
					{
						echo '<font class="profile_font" name="profile_font_online" color="green"><b>ONLINE</b></font>';
					} 
					else 
					{
						echo '<font class="profile_font" name="profile_font_online" color="red"><b>OFFLINE</b></font>';
					}
				} 
				else 
				{
					if ($profile_data['online']) 
					{
						echo '<font class="profile_font" name="profile_font_online" color="green"><b>ONLINE</b></font>';
					} 
					else 
					{
						echo '<font class="profile_font" name="profile_font_online" color="red"><b>OFFLINE</b></font>';
					}
				}
				?></td>
				</tr>
				<!-- Display player status end -->
				
				<!-- Player created -->
				<tr><td><strong>Created:</strong></td><td colspan="2"><?php echo getClock($profile_znote_data['created'], true); ?></td></tr>
				
				<!-- Player Comment -->
				<?php
				//if player doesnt have set a comment dont show it.
				if (!empty($profile_znote_data['comment']))
				{ ?>
					<tr>
						<td><strong>Comment:</strong></td><td colspan="2">
						<textarea name="profile_comment_textarea" cols="70" rows="10" readonly="readonly" class="span12"><?php echo $profile_znote_data['comment']; ?></textarea></td>
					</tr>
				<?php
				}
				?>
					<!-- Player addres -->
				<tr>
					<td><strong>Address:</strong></td><td colspan="2"><strong>
					<a href="
						<?php 
						if ($config['htwrite']) 
						{ 
							echo "http://" . $_SERVER['HTTP_HOST']."/" . $profile_data['name']; 
						}
						else 
						{ 
							echo "http://" . $_SERVER['HTTP_HOST'] . "/characterprofile.php?name=" . $profile_data['name']; 
						}	
						?>">
						<?php
						if ($config['htwrite']) 
						{ 
							echo "http://".$_SERVER['HTTP_HOST']."/". $profile_data['name']; 
						}
						else 
						{ 
							echo "http://".$_SERVER['HTTP_HOST']."/characterprofile.php?name=". $profile_data['name']; 
						}
						?>
				</a></strong></td></tr></table>
				<?php
					function format_exphist($val)
					{
						if($val > 0)
						{
							return 'This player has lost <strong>-'.$val.'</strong> experience this day.';
						}
						elseif($val < 0)
						{
							
						}
						else
						{
							return 'This player has not gained any experience this day.';
						}
					}
				?>
				<!-- Last Experience -->
				<table class="stripped" cellpadding="4">
					<tr><th colspan="2">Last Experience</th></tr>
					<tr>
						<td width="25%"><strong>Date</strong></td>
						<td><strong>Experience</strong></td>
					</tr>
					
					<tr>
						<td><?php echo date('d/M', strtotime("-1 day")).' - '.date('d/M', time()); ?></td>
						<td><?php echo format_exphist($profile_data['exphist1']); ?></td>
					</tr>
					<tr>
						<td><?php echo date('d/M', strtotime("-2 day")).' - '.date('d/M', strtotime("-1 day")); ?></td>
						<td><?php echo format_exphist($profile_data['exphist2']); ?></td>
					</tr>
					<tr>
						<td><?php echo date('d/M', strtotime("-3 day")).' - '.date('d/M', strtotime("-2 day")); ?></td>
						<td><?php echo format_exphist($profile_data['exphist3']); ?></td>
					</tr>	
					<tr>
						<td><?php echo date('d/M', strtotime("-4 day")).' - '.date('d/M', strtotime("-3 day")); ?></td>
						<td><?php echo format_exphist($profile_data['exphist4']); ?></td>
					</tr>	
					<tr>
						<td><?php echo date('d/M', strtotime("-5 day")).' - '.date('d/M', strtotime("-4 day")); ?></td>
						<td><?php echo format_exphist($profile_data['exphist5']); ?></td>
					</tr>	
					<tr>
						<td><?php echo date('d/M', strtotime("-6 day")).' - '.date('d/M', strtotime("-5 day")); ?></td>
						<td><?php echo format_exphist($profile_data['exphist6']); ?></td>
					</tr>	
					<tr>
						<td><?php echo date('d/M', strtotime("-7 day")).' - '.date('d/M', strtotime("-6 day")); ?></td>
						<td><?php echo format_exphist($profile_data['exphist7']); ?></td>
					</tr>	
				</table>
				
				<!-- Achievements start -->
				<?php if ($config['Ach']) 
				{ ?>			
					<table class="stripped" cellpadding="4">
					<tr><th colspan="2">Achievements</th></tr>
					<div id="accordion">
						
							
									<style>
										#secondD {
											margin-left:0px;
										}
									</style>
									<?php
									foreach ($config['achievements'] as $key => $achiv) 
									{
										$uery = mysql_select_single("SELECT `player_id`, `value`, `key` FROM `player_storage` WHERE `player_id`='$user_id' AND `key`='$key' LIMIT 1;");
										if (!empty($uery) || $uery !== false) 
										{
											foreach ($uery as $luery) 
											{
												if ($luery == $key) 
												{
													if (!array_key_exists($key, $achiv)) 
													{
														echo '<tr><td width="17%">' .$achiv[0]. '</td><td>' .$achiv[1]. '</td>';
														
														if (!isset($achiv['secret'])) 
														{
															echo '<td><img id="secondD" src="http://img04.imgland.net/PuMz0mVqSG.gif"></td>';
														}
														
														echo '<td>'. $achiv['points'] .'</td>';
														echo '<tr>';
													}
												}
												
											}
										}

									}
									?>
								</table>
						
					<br>
				<?php
				} 
				?>
				<!-- Achievements end -->
				
				<!-- DEATH LIST -->
				<table class="stripped" cellpadding="4">
					<tr><th colspan="2">Death List</th></tr>
					<?php
					if ($config['TFSVersion'] == 'TFS_02') 
					{
						$array = user_fetch_deathlist($user_id);
						if ($array) 
						{
						?>
							<tr>
							<?php
							// Design and present the list
							foreach ($array as $value) 
							{ ?>
								<?php
								$value['time'] = getClock($value['time'], true);
								
								if ($value['is_player'] == 1) 
								{
									$value['killed_by'] = 'player: <a href="characterprofile.php?name='. $value['killed_by'] .'">'. $value['killed_by'] .'</a>';
								} 
								else 
								{
									$value['killed_by'] = 'monster: '. $value['killed_by'] .'.';
								}
								
								echo '<td width="32%">'. $value['time'] .'</td><td>Killed at level '. $value['level'] .' by '. $value['killed_by'].'</td>'; ?>
							<?php
							}
							?>
							</tr>
							<?php
						} 
						else 
						{
							echo '<tr><td colspan="2">This player has never died.</td></tr>';
						}
					} 
					else if ($config['TFSVersion'] == 'TFS_10') 
					{
						$deaths = mysql_select_multi("SELECT 
							`player_id`, `time`, `level`, `killed_by`, `is_player`, 
							`mostdamage_by`, `mostdamage_is_player`, `unjustified`, `mostdamage_unjustified` 
							FROM `player_deaths` 
							WHERE `player_id`=$user_id ORDER BY `time` DESC LIMIT 10;");

						if ($deaths)
						{ 
							foreach ($deaths as $d) 
							{
								?>
								<tr>
									<?php echo "<td width=\"32%\">".getClock($d['time'], true, true)."</td><td>";
									$lasthit = ($d['is_player']) ? "<a href='characterprofile.php?name=".$d['killed_by']."'>".$d['killed_by']."</a>" : $d['killed_by'];
									echo ": Killed at level ".$d['level']." by $lasthit";
									if ($d['unjustified']) 
									{echo " <font color='red' style='font-style: italic;'>(unjustified)</font>";}
								
									$mostdmg = ($d['mostdamage_by'] !== $d['killed_by']) ? true : false;
									
									if ($mostdmg) 
									{
										$mostdmg = ($d['mostdamage_is_player']) ? "<a href='characterprofile.php?name=".$d['mostdamage_by']."'>".$d['mostdamage_by']."</a>" : $d['mostdamage_by'];
										echo "<br>and by $mostdmg.";
										
										if ($d['mostdamage_unjustified']) 
										{ echo " <font color='red' style='font-style: italic;'>(unjustified)</font>"; }
									} 
									else 
									{ echo " <b>(soloed)</b>"; }
									?>
								</td></tr>
								<?php
							}
						}
						else 
						{
							echo '<tr><td colspan="2">This player has never died.</td></tr>'; 
						}
					} 
					else if ($config['TFSVersion'] == 'TFS_03') 
					{
						//mysql_select_single("SELECT * FROM players WHERE name='TEST DEBUG';");
						$array = user_fetch_deathlist03($user_id);
						
						if ($array) 
						{?>
							
								<?php
								// Design and present the list
								foreach ($array as $value) 
								{ ?>
									<tr>
									<?php
									$value[3] = user_get_killer_id(user_get_kid($value['id']));
									
									if ($value[3] !== false && $value[3] >= 1) 
									{
										$namedata = user_character_data((int)$value[3], 'name');
										
										if ($namedata !== false) 
										{
											$value[3] = $namedata['name'];
											$value[3] = 'player: <a href="characterprofile.php?name='. $value[3] .'">'. $value[3] .'</a>';
										} 
										else 
										{
											$value[3] = 'deleted player.';
										}
									} 
									else 
									{
										$value[3] = user_get_killer_m_name(user_get_kid($value['id']));
										
										if ($value[3] === false) 
										{ $value[3] = 'deleted player.'; }
									}
									
									echo '<td width="32%">'. getClock($value['date'], true) .'</td><td>Killed at level '. $value['level'] .' by '. $value[3];
									echo '</td></tr>';
								}
								?>
							
							<?php
						} 
						else { echo '<tr><td colspan="2">This player has never died.</td></tr>'; }
					}
					?>
				</table>
				<!-- END DEATH LIST -->
				
				<!-- QUEST PROGRESSION -->
				<?php
				$totalquests = 0;
				$completedquests = 0;
				$firstrun = 1;
				
				if ($config['EnableQuests'] == true) 
				{
					$sqlquests =  mysql_select_multi("SELECT `player_id`, `key`, `value` FROM player_storage WHERE `player_id` = $user_id");
					foreach ($config['quests'] as $cquest) 
					{
						$totalquests = $totalquests + 1;
						foreach ($sqlquests as $dbquest) 
						{
							if ($cquest[0] == $dbquest['key'] && $cquest[1] == $dbquest['value']) 
							{
								$completedquests = $completedquests + 1;
							}
						}
						if ($cquest[3] == 1) 
						{
							if ($completedquests != 0) 
							{
								if ($firstrun == 1) 
								{
								?>
									<table class="stripped" cellpadding="4">
										<tr><th colspan="2">Quest progression </th></tr>
										
											<tr>
												<td>Quest:</td>
												<td>progression:</td>
											</tr>
								<?php
								$firstrun = 0;
								}
								$completed = $completedquests / $totalquests * 100;
								?>
								<tr>
									<td><?php echo $cquest[2]; ?></td>
									<td id="progress">
										<span id="percent"><?php echo round($completed); ?>%</span>
										<div id="bar" style="width: '.$completed.'%"></div>
									</td>
								</tr>
								<?php
							}
							
							$completedquests = 0;
							$totalquests = 0;
							
						}
					}
				}
				
				if ($firstrun == 0) 
				{ ?>
					</table>
				<?php
				}
				?>
				<!-- END QUEST PROGRESSION -->
				
				<!-- CHARACTER LIST -->
				<?php
				if (user_character_hide($profile_data['name']) != 1 && user_character_list_count(user_character_account_id($name)) > 1) 
				{
				?>
					<table class="stripped" cellpadding="4">
						<tr><th colspan="5">Other visible characters on this account</th></tr>
						<?php
						$characters = user_character_list(user_character_account_id($profile_data['name']));
						// characters: [0] = name, [1] = level, [2] = vocation, [3] = town_id, [4] = lastlogin, [5] = online
						if ($characters && count($characters) > 0) 
						{
							?>

								<tr>
									<td><strong>Name:</strong></td>
									<td><strong>Level:</strong></td>
									<td><strong>Vocation:</strong></td>
									<td><strong>Last login:</strong></td>
									<td><strong>Status:</strong></td>
								</tr>
								
								<?php
								// Design and present the list
								foreach ($characters as $char) 
								{
									if ($char['name'] != $profile_data['name']) 
									{
										if (hide_char_to_name(user_character_hide($char['name'])) != 'hidden') 
										{ ?>
											<tr>
												<td><strong><a href="characterprofile.php?name=<?php echo $char['name']; ?>"><?php echo $char['name']; ?></a></strong></td>
												<td><?php echo (int)$char['level']; ?></td>
												<td><?php echo $char['vocation']; ?></td>
												<td><?php echo $char['lastlogin']; ?></td>
												<td><?php echo $char['online']; ?></td>
											</tr>
										<?php
										}
									}
								}
							?>

							<?php
						} 
						else 
						{
							echo '<b><font color="green">This player has never died.</font></b>';
						}
						?>
					</table>
				<?php
				}
				?>
				<!-- END CHARACTER LIST -->
			
		<!-- END PROFILE MARKUP HERE-->
		</table>
		<?php
	} 
	else 
	{
		echo htmlentities(strip_tags($name, ENT_QUOTES)) . ' does not exist.';
	}
?>
	<form type="submit" action="characterprofile.php" method="get">
	
	<table>
		<tr><th >Search Character</th></tr>
		<tr><td>
			<table style="width: auto;margin: 0;">
			
				
				<tr>
					<td><strong>Name:</strong></td><td><input size="29" type="text" name="name" class="search"></td>
					<td>
					<input type="Submit" value="" class="hover" style="background: url(layout/tibia_img/sbutton_submit.gif); width:120px;height:18px;border: 0 none;" border="0"></td>
				</tr>
				

			</table>
		</td></tr>
	</table>
	
	</form>
<?php
} 
else 
{
	header('Location: index.php');
}

?>

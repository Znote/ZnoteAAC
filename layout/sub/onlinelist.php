<?php 
require_once 'engine/init.php';
error_reporting(E_ALL ^ E_NOTICE);
?>

	<?php
	$record = mysql_select_single('SELECT * FROM `server_record` ORDER BY `record` DESC LIMIT 1;');
	?>
	<table cellpadding="4">
		<tr>
			<th>
				Server Status
			</th>
		</tr>
		<tr>
			<td>
				Currently <strong><?php echo user_count_online(); ?></strong> players are online on <strong><?php echo $config['site_title']; ?></strong>. The record holds a total of <strong><?php echo $record['record'] ?> players</strong> on <?php echo date("M j Y", $record['timestamp']) ?>
			</td>
		</tr>
	</table>
<?php
function online_list_hemrenus($order) {
	if($order == 'name') { $orderby = 'name'; }
	elseif($order == 'lvl') { $orderby = 'level'; }
	elseif($order == 'voc') { $orderby = 'vocation'; }
	else { $orderby = 'name'; }
		
	if (config('TFSVersion') == 'TFS_10') return mysql_select_multi("SELECT `o`.`player_id` AS `id`, `p`.`name` as `name`, `p`.`level` as `level`, `p`.`vocation` as `vocation`, `g`.`name` as `gname` FROM `players_online` as `o` INNER JOIN `players` as `p` ON `o`.`player_id` = `p`.`id` LEFT JOIN `guild_membership` gm ON `o`.`player_id` = `gm`.`player_id` LEFT JOIN `guilds` g ON `gm`.`guild_id` = `g`.`id`");
	else return mysql_select_multi("SELECT `p`.`name` as `name`, `p`.`level` as `level`, `p`.`vocation` as `vocation`, `g`.`name` as `gname` FROM `players` p LEFT JOIN `guild_ranks` gr ON `gr`.`id` = `p`.`rank_id` LEFT JOIN `guilds` g ON `gr`.`guild_id` = `g`.`id` WHERE `p`.`online` = '1' ORDER BY `p`.`".$orderby."` DESC;");
}
$array = online_list_hemrenus(htmlspecialchars($_GET['order']));
if ($array) {
	?>

	<table class="stripped" cellpadding="4">
		<tr>
				<?php 
				if ($config['country_flags'])
				{ 
					echo '<th width="2%">Flag</th>';
					
				} ?>
			<th><strong><a style="color: #fff;" href="onlinelist.php?order=name">Name</a></strong></td>
			<th width="10%"><strong><a style="color: #fff;" href="onlinelist.php?order=lvl">Level</a></strong></th>
			<th width="20%"><strong><a style="color: #fff;" href="onlinelist.php?order=voc">Vocation</a></strong></th>
		</tr>
			<?php
			foreach ($array as $value) {
				echo '<tr>';
			$url = url("characterprofile.php?name=". $value['name']);
				
				if ($config['country_flags'])
				{ 
										$flag = user_znote_account_data(user_character_account_id($value['name']), 'flag');
					echo '<td><center><img src="flags/' . $flag['flag'] . '.png"></center></td>';
					
				} 
				
			echo '';
			echo '<td><strong><a href="characterprofile.php?name='. $value['name'] .'">'. $value['name'] .'</a></strong></td>';
			echo '<td>'. $value['level'] .'</td>';
			echo '<td>'. vocation_id_to_name($value['vocation']) .'</td>';
			echo '</tr>';
			}
			?>
	</table>

	<?php
} else {
	echo '<table cellpadding="4"><tr><th>Players Online</th></tr><tr><td>Nobody is online.</td></tr></table>';
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
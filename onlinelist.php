<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<h1>Who is online?</h1>
<?php
$array = online_list();
if ($array) {
	?>
	
	<table id="onlinelistTable" class="table table-striped table-hover">
		<tr class="yellow">
			<th>Name:</th>
			<th>Guild:</th>
			<th>Level:</th>
			<th>Vocation:</th>
		</tr>
			<?php
			foreach ($array as $value) {
			$url = url("characterprofile.php?name=". $value['name']);
			echo '<tr class="special" onclick="javascript:window.location.href=\'' . $url . '\'">';
			echo '<td><a href="characterprofile.php?name='. $value['name'] .'">'. $value['name'] .'</a></td>';
			if (!empty($value['gname'])) echo '<td><a href="guilds.php?name='. $value['gname'] .'">'. $value['gname'] .'</a></td>'; else echo '<td></td>'; 
			echo '<td>'. $value['level'] .'</td>';
			echo '<td>'. vocation_id_to_name($value['vocation']) .'</td>';
			echo '</tr>';
			}
			?>
	</table>

	<?php
} else {
	echo 'Nobody is online.';
}
?>
<?php include 'layout/overall/footer.php'; ?>
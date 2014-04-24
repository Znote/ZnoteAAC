<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<h1>Changelog</h1>
<?php
$changelogs = mysql_select_multi("SELECT `id`, `text`, `time`, `report_id`, `status` FROM `znote_changelog` ORDER BY `id` DESC;");
if ($changelogs !== false) {
	?>
	<table>
		<tr class="yellow">
			<td>Changelogs</td>
		</tr>
		<?php
		foreach ($changelogs as $changelog) {
		?>
		<tr>
			<td><b><?php echo getClock($changelog['time'], true, true); ?></b><br><?php echo $changelog['text']; ?></td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
} else {
	?>

	<?php
}
include 'layout/overall/footer.php'; ?>
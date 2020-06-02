<?php
require_once 'engine/init.php';
protect_page();
admin_only($user_data);
include 'layout/overall/header.php';

// Report status types. When a player make new report it will be default to 0.
// Feel free to add/remove and change name/color of status types.
$statusTypes = array(
	0 => '<font color="purple">Reported</font>',
	1 => '<font color="darkblue">To-Do List</font>',
	2 => '<font color="red">Confirmed bug</font>',
	3 => '<font color="grey">Invalid</font>',
	4 => '<font color="grey">Rejected</font>',
	5 => '<font color="green"><b>Fixed</b></font>'
);
// Which status IDs should give option to add to changelog?
$statusChangeLog = array(0,5);

// Autohide rows that have these status IDs:
$hideStatus = array(3, 4, 5);

// Fetch data from SQL
$reportsData = mysql_select_multi('SELECT id, name, posx, posy, posz, report_description, date, status FROM znote_player_reports ORDER BY id DESC;');
// If SQL data is not empty
if ($reportsData !== false) {
	// Order reports array by ID for easy reference later on.
	$reports = array();
	for ($i = 0; $i < count($reportsData); $i++)
		foreach ($statusTypes as $key => $value)
			if ($key == $reportsData[$i]['status'])
				$reports[$key][$reportsData[$i]['id']] = $reportsData[$i];
}

// POST logic (Update report and give player points)
if (!empty($_POST)) {
	// Fetch POST data
	$playerName = getValue($_POST['playerName']);
	$status = getValue($_POST['status']);
	$price = getValue($_POST['price']);
	$customPoints = getValue($_POST['customPoints']);
	$reportId = getValue($_POST['id']);

	$changelogReportId = (int)$_POST['changelogReportId'];
	$changelogValue = &$_POST['changelogValue'];
	$changelogText = getValue($_POST['changelogText']);
	$changelogStatus = ($changelogReportId !== false && $changelogValue === '2' && $changelogText !== false) ? true : false;

	if ($customPoints !== false) $price = (int)($price + $customPoints);

	// Update SQL
	mysql_update("UPDATE `znote_player_reports` SET `status`='$status' WHERE `id`='$reportId' LIMIT 1;");
	echo "<h1>Report status updated to ".$statusTypes[(int)$status] ."!</h1>";
	// Update local array representation
	foreach ($reports as $sid => $sa) 
		foreach ($sa as $rid => $ra) 
			if ($reportId == $rid) {
				$reports[$status][$reportId] = $reports[$sid][$rid];
				$reports[$status][$reportId]['status'] = $status;
				unset($reports[$sid][$rid]);
			}

	// If we should do anything with changelog:
	if ($changelogStatus) {
		$time = time();
		// Check if changelog exist (`id`, `text`, `time`, `report_id`, `status`)
		$changelog = mysql_select_single("SELECT * FROM `znote_changelog` WHERE `report_id`='$changelogReportId' LIMIT 1;");
		// If changelog exist
		$updatechangelog = false;
		if ($changelog !== false) {
			// Update it
			mysql_update("UPDATE `znote_changelog` SET `text`='$changelogText', `time`='$time' WHERE `id`='".$changelog['id']."' LIMIT 1;");
			echo "<h2>Changelog message updated!</h2>";
			$updatechangelog = true;
		} else {
			// Create it
			mysql_insert("INSERT INTO `znote_changelog` (`text`, `time`, `report_id`, `status`) 
				VALUES ('$changelogText', '$time', '$changelogReportId', '$status');");
			echo "<h2>Changelog message created!</h2>";
			$updatechangelog = true;
		}
		if ($updatechangelog) {
			// Cache changelog
			$cache = new Cache('engine/cache/changelog');
			$cache->setContent(mysql_select_multi("SELECT `id`, `text`, `time`, `report_id`, `status` FROM `znote_changelog` ORDER BY `id` DESC;"));
			$cache->save();
		}
		
	}
	// If we should give user price
	if ($price > 0) {
		$account = mysql_select_single("SELECT `a`.`id`, `a`.`email` FROM `accounts` AS `a` 
			INNER JOIN `players` AS `p` ON `p`.`account_id` = `a`.`id`
			WHERE `p`.`name` = '$playerName' LIMIT 1;");
		
		if ($account !== false) {
			// transaction log
			mysql_insert("INSERT INTO `znote_paypal` VALUES ('', '$reportId', 'report@admin.".$user_data['name']." to ".$account['email']."', '".$account['id']."', '0', '".$price."')");
			// Process payment
			$data = mysql_select_single("SELECT `points` AS `old_points` FROM `znote_accounts` WHERE `account_id`='".$account['id']."';");
			// Give points to user
			$new_points = $data['old_points'] + $price;
			mysql_update("UPDATE `znote_accounts` SET `points`='$new_points' WHERE `account_id`='".$account['id']."'");

			// Remind GM that he sent points to character
			echo "<font color='green' size='5'>".$playerName." has been granted ".$price." points for his reports.</font>";
		}
	}

// GET logic (Edit report data and specify how many [if any] points to give to user)
} elseif (!empty($_GET)) {
	// Fetch GET data
	$action = getValue($_GET['action']);
	$playerName = getValue($_GET['name']);
	$reportId = getValue($_GET['id']);

	// Fetch the report we intend to modify
	foreach ($reports as $sid => $sa)
		foreach ($sa as $rid => $ra)
			if ($rid == $reportId)
				$report = $reports[$sid][$reportId];

	// Create HTML form
	?>
	<div style="width: 300px; margin: auto;">
		<form action="admin_reports.php" method="POST">
			Player: <a target="_BLANK" href="characterprofile.php?name=<?php echo $report['name']; ?>"><?php echo $report['name']; ?></a>
			<input type="hidden" name="playerName" value="<?php echo $report['name']; ?>">
			<input type="hidden" name="id" value="<?php echo $report['id']; ?>">
			<br>Set status: 
			<select name="status">
				<?php
				foreach ($statusTypes as $sid => $sname)
					echo ($sid != $report['status']) ? "<option value='$sid'>$sname</option>" : "<option value='$sid' selected>$sname</option>";
				?>
			</select><br>
			Give user points:
			<select name="price">
				<option value='0'>0</option>
				<?php
				foreach ($config['paypal_prices'] as $price)
					echo "<option value='$price'>$price</option>";
				?>
			</select> + <input name="customPoints" type="text" style="width: 50px;" placeholder="0"><br>
			<?php
			if (in_array($report['status'], $statusChangeLog)) {
				?>
				<br>
				<input type="hidden" name="changelogReportId" value="<?php echo $report['id']; ?>">
				Add / update changelog message? <select name="changelogValue">
					<option value="1">No</option>
					<option value="2">Yes</option>
				</select><br>
				<textarea rows="7" cols="40" maxlength="254" name="changelogText"></textarea>
				<?php
			}
			?>
			<br>
			<input type="submit" value="Update Report" style="width: 100%;">
		</form>
	</div>
	<?php
}

// If SQL data is not empty
if ($reportsData !== false) {
	// Render HTML
	?>
	<center>
		<?php
		foreach ($reports as $statusId => $statusArray) {
			?>
			<h2 class="statusType"><?php echo $statusTypes[$statusId]; ?> (<span id="status-<?php echo $statusId; ?>">Visible</span>)</h2>
			<table class="table tbl" border="0" cellspacing="1" cellpadding="4" width="100%">
				<thead>
					<tr class="yellow" onclick="javascript:toggle('<?php echo $statusId; ?>')">
						<td width="38%">Info</td>
						<td>Description</td>
					</tr>
				</thead>
				<?php
				foreach ($statusArray as $reportId => $report) {
				?>
				<tbody class="row<?php echo $report['status']; ?>">
					<tr>
						<td>
							<b>Report ID:</b> #<?php echo $report['id']; ?>
							<br><b>Name:</b> <a href="characterprofile.php?name=<?php echo $report['name']; ?>"><?php echo $report['name']; ?></a>
							<br><b>Position:</b> <input type="text" disabled value="/pos <?php echo $report['posx'].', '.$report['posy'].', '.$report['posz']; ?>">
							<br><b>Reported:</b> <?php echo getClock($report['date'], true, true); ?>
							<br><b>Status:</b> <?php echo $statusTypes[$report['status']]; ?>. <a href="?action=edit&name=<?php echo $report['name'].'&id='.$report['id']; ?>">Edit</a>
						</td>
						<td><?php echo $report['report_description']; ?></td>
					</tr>
				</tbody>
				<?php
				}
			?></table><?php
		}
		?>
	</center>
	<?php 
} else echo "<h2>No reports submitted.</h2>";
?>
<style>
tr.yellow[onclick] td {
	font-weight: bold;
	color: white;
	text-align: center;
}
tbody[class^=row] td:last-of-type {
	text-align: center;
}
</style>
<script type="text/javascript">
	// Hide and show tables
	// Written in clean javascript to make it cross-layout compatible.
	function toggle(statusId) {
		var divStatus = 'row' + statusId,
			msgStatus = 'status-' + statusId;

		// Change visibility status
		statusElement = document.getElementById(msgStatus);

		statusElement.innerHTML = (statusElement.innerHTML == 'Visible') ? 'Hidden' : 'Visible';
		// Show/hide elements.
		var elements = document.getElementsByClassName(divStatus);
		for (var i = 0; i < elements.length; i++)
			elements[i].style.display = (elements[i].style.display == 'none') ? 'table-header-group' : 'none';
	}

	<?php // Hide configured tables by default
	foreach ($hideStatus as $statusId)
		echo "toggle($statusId);";
	?>

	var st = document.body.querySelectorAll('.statusType');
	for(i = 0; i < st.length; i++)
		st[i].addEventListener('click', function(e) {
			toggle(e.currentTarget.querySelector('span').id.match(/(\d)+/)[0]);
		});
</script>
<?php include 'layout/overall/footer.php'; ?>

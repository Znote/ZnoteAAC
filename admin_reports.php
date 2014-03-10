<?php require_once 'engine/init.php';
protect_page(); admin_only($user_data);
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

// Fetch data from SQL
$reportsData = mysql_select_multi("SELECT id, name, posx, posy, posz, report_description, date, status FROM znote_player_reports ORDER BY id DESC;");
// If sql data is not empty
if ($reportsData !== false) {
    // Order reports array by ID for easy reference later on.
    $reports = array();
    for ($i = 0; $i < count($reportsData); $i++) $reports[$reportsData[$i]['id']] = $reportsData[$i];
}

// POST logic (Update report and give player points)
if (!empty($_POST)) {
    // Fetch POST data
    $playerName = getValue($_POST['playerName']);
    $status = getValue($_POST['status']);
    $price = getValue($_POST['price']);
    $customPoints = getValue($_POST['customPoints']);
    $reportId = getValue($_POST['id']);

    if ($customPoints !== false) $price = (int)($price + $customPoints);

    // Update SQL
    mysql_update("UPDATE `znote_player_reports` SET `status`='$status' WHERE `id`='$reportId' LIMIT 1;");
    // Update local array representation
    $reports[$reportId]['status'] = $status;

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
    $playerName = getValue($_GET['playerName']);
    $reportId = getValue($_GET['id']);

    // Fetch the report we intend to modify
    $report = $reports[$reportId];

    // Create html form
    ?>
    <div style="width: 300px; margin: auto;">
        <form action="admin_reports.php" method="POST">
            Player: <a target="_BLANK" href="characterprofile.php?name=<?php echo $report['name']; ?>"><?php echo $report['name']; ?></a>
            <input type="hidden" name="playerName" value="<?php echo $report['name']; ?>">
            <input type="hidden" name="id" value="<?php echo $report['id']; ?>">
            <br>Set status: 
            <select name="status">
                <?php
                foreach ($statusTypes as $sid => $sname) {
                    if ($sid != $report['status']) echo "<option value='$sid'>$sname</option>";
                    else echo "<option value='$sid' selected>$sname</option>";
                }
                ?>
            </select><br>
            Give user points:
            <select name="price">
                <option value='0'>0</option>
                <?php
                foreach ($config['paypal_prices'] as $price) {
                    echo "<option value='$price'>$price</option>";
                }
                ?>
            </select> + <input name="customPoints" type="text" style="width: 50px;" placeholder="0"><br><br>
            <input type="submit" value="Update Report" style="width: 100%;">
        </form>
    </div>
    <?php
}

// If sql data is not empty
if ($reportsData !== false) {

    // Render HTML
    ?>
    <center>
        <h1>Reports List</h1>
        <table class="table tbl" border="0" cellspacing="1" cellpadding="4" width="100%">
            <tr class="yellow">
                <td><b><font color=white><center>Info</center></font></b></td>
                <td><b><font color=white><center>Description</center></font></b></td>
            </tr>
            <?php
            foreach ($reports as $report) {
                ?>
                <tr>
                    <td width="38%"> <b>Report ID:</b> #<?php echo $report['id']; ?>
                        <br><b>Name:</b> <a href="characterprofile.php?name=<?php echo $report['name']; ?>"><?php echo $report['name']; ?></a>
                        <br><b>Position:</b> <input type="text" disabled value="/pos <?php echo $report['posx'].', '.$report['posy'].', '.$report['posz']; ?>">
                        <br><b>Reported:</b> <?php echo getClock($report['date'], true, true); ?>
                        <br><b>Status:</b> <?php echo $statusTypes[$report['status']]; ?>. <a href="?action=edit&name=<?php echo $report['name'].'&id='.$report['id']; ?>">Edit</a>
                    </td>
                    <td>
                        <center><?php echo $report['report_description']; ?></center>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </center>
    <?php 
} else echo "<h2>No reports submitted.</h2>";
include 'layout/overall/footer.php';
?>
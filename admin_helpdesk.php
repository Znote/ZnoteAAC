<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
protect_page();
admin_only($user_data);

// Declare as int
$view = (isset($_GET['view']) && (int)$_GET['view'] > 0) ? (int)$_GET['view'] : false;
if ($view !== false){
	if (!empty($_POST['reply_text'])) {
		sanitize($_POST['reply_text']);

		// Save ticket reply on database
		$query = array(
			'tid'   =>	$view,
			'username'=>	getValue($_POST['username']),
			'message' =>	getValue($_POST['reply_text']),
			'created' =>	time(),
		);
		$fields = '`'. implode('`, `', array_keys($query)) .'`';
		$data = '\''. implode('\', \'', $query) .'\'';

		mysql_insert("INSERT INTO `znote_tickets_replies` ($fields) VALUES ($data)");
		mysql_update("UPDATE `znote_tickets` SET `status`='Staff-Reply' WHERE `id`='$view' LIMIT 1;");

	} else if (!empty($_POST['admin_ticket_close'])) {
		$ticketId = (int) $_POST['admin_ticket_id'];
		mysql_update("UPDATE `znote_tickets` SET `status` = 'CLOSED' WHERE `id` ='$ticketId' LIMIT 1;");

	} else if (!empty($_POST['admin_ticket_open'])) {
		$ticketId = (int) $_POST['admin_ticket_id'];
		mysql_update("UPDATE `znote_tickets` SET `status` = 'Open' WHERE `id` ='$ticketId' LIMIT 1;");

	} else if (!empty($_POST['admin_ticket_delete'])) {
		$ticketId = (int) $_POST['admin_ticket_id'];
		mysql_delete("DELETE FROM `znote_tickets` WHERE `id`='$ticketId' LIMIT 1;");
		header("Location: admin_helpdesk.php");
	}

	$ticketData = mysql_select_single("SELECT * FROM znote_tickets WHERE id='$view' LIMIT 1;");
	?>
	<h1>View Ticket #<?php echo $ticketData['id']; ?></h1>
	<table class="znoteTable ThreadTable table table-striped">
		<tr class="yellow">
			<th>
				<?php
					echo getClock($ticketData['creation'], true);
				?>
				 - Created by:
				 <?php
				 	echo $ticketData['username'];
				 ?>
			</th>
		</tr>
		<tr>
			<td>
				<p><?php echo nl2br($ticketData['message']); ?></p>
			</td>
		</tr>
	</table>
	<?php
	$replies = mysql_select_multi("SELECT * FROM znote_tickets_replies WHERE tid='$view' ORDER BY `created`;");
	if ($replies !== false) {
		foreach($replies as $reply) {
			?>
			<table class="znoteTable ThreadTable table table-striped">
				<tr class="yellow">
					<th>
						<?php
							echo getClock($reply['created'], true);
						?>
						 - Posted by:
						 <?php
						 	echo $reply['username'];
						 ?>
					</th>
				</tr>
				<tr>
					<td>
						<p><?php echo nl2br($reply['message']); ?></p>
					</td>
				</tr>
			</table>
		<?php
		}
	}
	?>

	<!-- Open/Close Ticket -->
	<table class="znoteTable ThreadTable table table-striped">
		<tr>
			<td>
				<form action="" method="post" align="center">
					<input type="hidden" name="admin_ticket_id" value="<?php echo $ticketData['id']; ?>">
				<?php if ($ticketData['status'] !== 'CLOSED') { ?>
					<input type="submit" name="admin_ticket_close" value="Close Ticket" class="btn btn-warning">
				<?php } else { ?>
					<input type="submit" name="admin_ticket_open" value="Open Ticket" class="btn btn-success">
				<?php } ?>
				</form>
			</td>
			<td>
				<form action="" method="post" align="center" onClick="return confirm('Are you sure you want to delete this ticket?');">
					<input type="hidden" name="admin_ticket_id" value="<?php echo $ticketData['id']; ?>">
					<input type="submit" name="admin_ticket_delete" value="Delete Ticket" class="btn btn-danger">
				</form>
			</td>
		</tr>
	</table>

	<?php if ($ticketData['status'] !== 'CLOSED') { ?>
		<hr class="bighr">
		<form action="" method="post">
			<input type="hidden" name="username" value="ADMIN"><br>
			<textarea class="forumReply" name="reply_text" style="width: 610px; height: 150px"></textarea><br>
			<input name="" type="submit" value="Post Reply" class="btn btn-primary">
		</form>
	<?php } ?>
	<?php
} else {
	?>
	<h1>Latest Tickets</h1>
	<?php
	$tickets = mysql_select_multi("SELECT id,subject,creation,status FROM znote_tickets ORDER BY creation DESC");
	if ($tickets !== false) {
		?>
		<table>
			<tr class="yellow">
				<td>ID:</td>
				<td>Subject:</td>
				<td>Creation:</td>
				<td>Status:</td>
			</tr>
				<?php
				foreach ($tickets as $ticket) {
					echo '<tr class="special">';
						echo '<td>'. $ticket['id'] .'</td>';
						echo '<td><a href="admin_helpdesk.php?view='. $ticket['id'] .'">'. $ticket['subject'] .'</a></td>';
						echo '<td>'. getClock($ticket['creation'], true) .'</td>';
						echo '<td>'. $ticket['status'] .'</td>';
					echo '</tr>';
				}
				?>
		</table>
		<?php
	} else echo 'No helpdesk tickets has been submitted.';
}
include 'layout/overall/footer.php';
?>
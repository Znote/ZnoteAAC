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
			<hr class="bighr">
		<?php
		}
	}
	?>
	<form action="" method="post">
		<input type="hidden" name="username" value="ADMIN"><br>
		<textarea class="forumReply" name="reply_text" style="width: 610px; height: 150px"></textarea><br>
		<input name="" type="submit" value="Post Reply" class="btn btn-primary">
	</form>
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
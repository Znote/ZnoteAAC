<?php
require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';

$view = (isset($_GET['view']) && (int)$_GET['view'] > 0) ? (int)$_GET['view'] : false;
if ($view !== false) {
	if (!empty($_POST['reply_text'])) {

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
		mysql_update("UPDATE `znote_tickets` SET `status`='Player-Reply' WHERE `id`='$view' LIMIT 1;");
	}
	$ticketData = mysql_select_single("SELECT * FROM znote_tickets WHERE id='$view' LIMIT 1;");

	if($ticketData['owner'] != $session_user_id) {
		echo 'You can not view this ticket!';
		include 'layout/overall/footer.php';
		die;
	}
	?>
	<h1>View Ticket #
	<?php 
		echo $ticketData['id'];
		if ($ticketData['status'] === 'CLOSED') {
			echo '<span style="color:red">[CLOSED]</SPAN>';
		}
	?></h1>
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

	<?php if ($ticketData['status'] !== 'CLOSED') { ?>
		<form action="" method="post">
			<input type="hidden" name="username" value="<?php echo $ticketData['username']; ?>"><br>
			<textarea class="forumReply" name="reply_text" style="width: 610px; height: 150px"></textarea><br>
			<input name="" type="submit" value="Post Reply" class="btn btn-primary">
		</form>
	<?php } ?>
	<?php
} else {

	$account = mysql_select_single("SELECT name,email FROM accounts WHERE id = $session_user_id");
	if (!empty($_POST)) {
		$required_fields = array('username', 'email', 'subject', 'message');
		foreach($_POST as $key=>$value) {
			if (empty($value) && in_array($key, $required_fields) === true) {
				$errors[] = 'You need to fill in all fields.';
				break 1;
			}
		}
		
		// check errors (= user exist, pass long enough
		if (empty($errors) === true) {
			/* Token used for cross site scripting security */
			if (!Token::isValid($_POST['token'])) {
				$errors[] = 'Token is invalid.';
			}
			if ($config['use_captcha']) {
				if(!verifyGoogleReCaptcha($_POST['g-recaptcha-response'])) {
					$errors[] = "Please confirm that you're not a robot.";
				}
			}
			// Reversed this if, so: first check if you need to validate, then validate. 
			if ($config['validate_IP'] === true && validate_ip(getIP()) === false) {
				$errors[] = 'Failed to recognize your IP address. (Not a valid IPv4 address).';
			}
		}
	}
	?>
	<h1>Latest Tickets</h1>
	<?php
	$tickets = mysql_select_multi("SELECT id,subject,creation,status FROM znote_tickets WHERE owner=$session_user_id ORDER BY creation DESC");
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
						echo '<td><a href="helpdesk.php?view='. $ticket['id'] .'">'. $ticket['subject'] .'</a></td>';
						echo '<td>'. getClock($ticket['creation'], true) .'</td>';
						echo '<td>'. $ticket['status'] .'</td>';
					echo '</tr>';
				}
				?>
		</table>
		<?php
	}
	?>

	<h1>Helpdesk</h1>
	<?php
	if (isset($_GET['success']) && empty($_GET['success'])) {
		echo 'Congratulations! Your ticket has been created. We will reply up to 24 hours.';
	} else {

		if (empty($_POST) === false && empty($errors) === true) {
			if ($config['log_ip']) {
				znote_visitor_insert_detailed_data(1);
			}

			//Save ticket on database
			$query = array(
				'owner'   =>	$session_user_id,
				'username'=>	getValue($_POST['username']),
				'subject' =>	getValue($_POST['subject']),
				'message' =>	getValue($_POST['message']),
				'ip'	  =>	getIPLong(),
				'creation' =>	time(),
				'status'  =>	'Open'
			);
		
			$fields = '`'. implode('`, `', array_keys($query)) .'`';
			$data = '\''. implode('\', \'', $query) .'\'';
			mysql_insert("INSERT INTO `znote_tickets` ($fields) VALUES ($data)");

			header('Location: helpdesk.php?success');
			exit();
		
		} else if (empty($errors) === false) {
			echo '<font color="red"><b>';
			echo output_errors($errors);
			echo '</b></font>';
		}
		?>
		<form action="" method="post">
			<ul>
				<li>
					Account Name:<br>
					<input type="text" name="username" size="40" value="<?php echo $account['name']; ?>" disabled>
				</li>
				<li>
					Email:<br>
					<input type="text" name="email" size="40" value="<?php echo $account['email']; ?>" disabled>
				</li>
				<li>
					Subject:<br>
					<input type="text" name="subject" size="40">
				</li>
				<li>
					Message:<br>
					<textarea name="message" rows="7" cols="30"></textarea>
				</li>
				<?php
				if ($config['use_captcha']) {
					?>
					<li>
						 <div class="g-recaptcha" data-sitekey="<?php echo $config['captcha_site_key']; ?>"></div>
					</li>
					<?php
				}
				?>
				<?php
					/* Form file */
					Token::create();
				?>
				<li>
					<input type="hidden" name="username" value="<?php echo $account['name']; ?>">
					<input type="submit" value="Submit ticket">
				</li>
			</ul>
		</form>
		<?php 
	}
}
include 'layout/overall/footer.php';
?>

<div class="well myaccount_widget widget" id="loginContainer">
	<div class="header">
		Administration
	</div>
	<div class="body">
		<ul class="linkbuttons">
			<li>
				<a href='admin.php'>Admin Page</a>
			</li>
			<li>
				<a href='admin_news.php'>Admin News</a>
			</li>
			<li>
				<a href='admin_gallery.php'>Admin Gallery</a>
			</li>
			<li>
				<a href='admin_skills.php'>Admin Skills</a>
			</li>
			<li>
				<a href='admin_reports.php'>Admin Reports</a>
			</li>
			<li>
				<a href='admin_helpdesk.php'>Admin Helpdesk</a>
			</li>
			<li>
				<a href='admin_shop.php'>Admin Shop</a>
			</li>
			<li>
				<a href='admin_auction.php'>Admin Auction</a>
			</li>
			<?php
			$new = 0;
			$cat = 4; //Category ID for feedback section
			$threads = mysql_select_multi("SELECT `id`, `player_id` FROM `znote_forum_threads` WHERE `forum_id`='$cat' AND `closed`='0';");
			if ($threads !== false) {
				$staffs = mysql_select_multi("SELECT `id` FROM `players` WHERE `group_id` > '1';");
				
				foreach($threads as $thread) {
					$response = false;
					$posts = mysql_select_multi("SELECT `id`, `player_id` FROM `znote_forum_posts` WHERE `thread_id`='". $thread['id'] ."';");
					if ($posts !== false) {
						foreach($posts as $post) {
							foreach ($staffs as $staff) {
								if ($post['player_id'] == $staff['id']) $response = true;
							}
						}
					}

					if (!$response) $new++;
				}
			}
			?>
			<li>
				<a href='forum.php?cat=4'>Feedback: [<?php echo $new; ?>] new</a>
			</li>
		</ul>
	</div>
</div>
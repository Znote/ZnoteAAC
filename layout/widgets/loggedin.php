<div class="sidebar">
	<h2>Welcome, <?php if ($config['ServerEngine'] !== 'OTHIRE') echo $user_data['name']; else echo $user_data['id'];?>.</h2>
	<div class="inner">
		<ul>
			<li>
				<a href='myaccount.php'>My Account</a>
			</li>
			<li>
				<a href='createcharacter.php'>Create Character</a>
			</li>
			<li>
				<a href='changepassword.php'>Change Password</a>
			</li>
			<li>
				<a href='settings.php'>Settings</a>
			</li>
			<li>
				<a href='logout.php'>Logout</a>
			</li>
		</ul>
	</div>
</div>
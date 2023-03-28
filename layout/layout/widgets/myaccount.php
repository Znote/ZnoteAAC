<div class="well myaccount_widget widget" id="loginContainer">
	<div class="header">
		Welcome, <?php if ($config['ServerEngine'] !== 'OTHIRE') echo $user_data['name']; else echo $user_data['id'];?>.
	</div>
	<div class="body">
		<ul class="linkbuttons">
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

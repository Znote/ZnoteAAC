<div class="sidebar">
	<h2>Login / Register</h2>
	<div class="inner">
		<form action="login.php" method="post">
		<ul id="login">
			<li>
				Username: <br>
				<input type="text" name="username">
			</li>
			<li>
				Password: <br>
				<input type="password" name="password">
			</li>
			<li>
				<input type="submit" value="Log in">
			</li>
			<?php
				/* Form file */
				Token::create();
			?>
		<center>	<h3><a href="register.php">New account</a></h3>
		<font size="1">- <a href="lostaccount.php" title="recovery access to login">Problems with logging?</a></font></center>
		</ul>
		</form>
	</div>
</div>

<div class="well loginContainer widget" id="loginContainer">
	<div class="header">
		Login / Register
	</div>
	<div class="body">
		<form class="loginForm" action="login.php" method="post">
			<div class="well">
				<label for="login_username">Userame:</label> <input type="text" name="username" id="login_username">
			</div>
			<div class="well">
				<label for="login_password">Password:</label> <input type="password" name="password" id="login_password">
			</div>
			<?php if ($config['twoFactorAuthenticator']): ?>
				<div class="well">
					<label for="login_password">Token:</label> <input type="password" name="authcode">
				</div>
			<?php endif; ?>
			<div class="well">
				<input type="submit" value="Log in" class="submitButton">
			</div>
			<?php
				/* Form file */
				Token::create();
			?>
			<center>
				<h3><a href="register.php">New account</a></h3>
				<p>Lost <a href="recovery.php?mode=username">username</a> or <a href="recovery.php?mode=password">password</a>?</p>
			</center>
		</form>
	</div>
</div>

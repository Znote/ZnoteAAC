<form action="houses.php" method="post">
	Select town:<br>
	<select name="selected">
	<?php
	foreach ($config['towns'] as $id => $name) echo '<option value="'. $id .'">'. $name .'</option>';
	?>
	</select> 
	<?php
		/* Form file */
		Token::create();
	?>
	<input type="submit" value="Fetch houses">
</form>
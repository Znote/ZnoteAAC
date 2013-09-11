<div class="sidebar">
	<h2>Search town list</h2>
	<div class="inner">
		<form action="houses.php" method="<?php if ($config['TFSVersion'] !== 'TFS_10') echo "post"; else echo "get" ;?>">
		
			Select town:<br>
			<select name="<?php if ($config['TFSVersion'] !== 'TFS_10') echo "selected"; else echo "id" ;?>">
			<?php
			foreach ($config['towns'] as $id => $name) echo '<option value="'. $id .'">'. $name .'</option>';
			?>
			</select> 
			<?php
				/* Form file */
				if ($config['TFSVersion'] !== 'TFS_10') Token::create();
			?>
			<input type="submit" value="Fetch houses">
		</form>
	</div>
</div>
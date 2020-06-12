<div class="well widget">
	<div class="header">
		Town list / houses
	</div>
	<div class="body">
		<form action="houses.php" method="<?php if ($config['ServerEngine'] !== 'TFS_10') echo "post"; else echo "get" ;?>">
			<select name="<?php if ($config['ServerEngine'] !== 'TFS_10') echo "selected"; else echo "id" ;?>">
				<?php
				foreach ($config['towns'] as $id => $name) 
					echo '<option value="'. $id .'">'. $name .'</option>';
				?>
			</select> 
			<?php
				/* Form file */
				if ($config['ServerEngine'] !== 'TFS_10') Token::create();
			?>
			<input type="submit" value="Fetch houses">
		</form>
	</div>
</div>
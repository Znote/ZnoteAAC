<div class="well widget">
	<div class="header">
		Town list / houses
	</div>
	<div class="body">
		<form action="houses.php" method="get">
			<select name="id">
				<?php
				foreach ($config['towns'] as $id => $name)
					echo '<option value="'. $id .'">'. $name .'</option>';
				?>
			</select>
			<input type="submit" value="Fetch houses">
		</form>
	</div>
</div>

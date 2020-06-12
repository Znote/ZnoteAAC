<div class="well search_widget" id="searchContainer">
	<div class="header">
		Character search
	</div>
	<div class="body">
		<div class="relative">
			<div id="name_suggestion">
				<div class="sname"><a href="characterprofile.php?name=Luxitur">Luxitur</a></div>
				<div class="sname">
					<a href="characterprofile.php?name=Luxitur">Luxitur</a>
				</div>
			</div>
		</div>
		<form class="searchForm" action="characterprofile.php" method="get">
			<label for="src_name">Search: </label><input autocomplete="off" type="text" name="name" id="src_name" class="search" placeholder="Name . . .">
		</form>
		<?php
		$cache = new Cache('engine/cache/characterNames');
		if ($cache->hasExpired()) {
			$names_sql = mysql_select_multi('SELECT `name` FROM `players` ORDER BY `name` ASC;');
			$names = array();
			foreach ($names_sql as $name) {
				$names[] = $name['name'];
			}
			$cache->setContent($names);
			$cache->save();
		} else {
			$names = $cache->load();
		}
		?>
		<script type="text/javascript">
			window.searchNames = <?php echo json_encode($names)?>;	
			$(function() {
				if (window.searchNames.length > 0) {
					$('#src_name').keyup(function(e) {
						$('#name_suggestion').html('');
						var search = $(this).val().toLowerCase();
						var results = new Array();
						if (search.length > 0) {
							var i = 0;
							for (i; i < window.searchNames.length && results.length < 10; i+=1) {
								if (window.searchNames[i].toLowerCase().indexOf(search) > -1) {
									results.push(window.searchNames[i]);
								}
							}
						}
						if (results.length > 0) {
							i = 0;
							var search_html = "";
							for (i; i < results.length; i+=1) {
								search_html += '<div class="sname"><a href="characterprofile.php?name='+results[i]+'">'+results[i]+'</a></div>';
							}
							$('#name_suggestion').addClass('show').html(search_html);
						} else {
							$('#name_suggestion.show').removeClass('show');
						}
					});
				}
			});
		</script>
	</div>
</div>
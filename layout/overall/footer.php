				</div>
				<!-- MAIN FEED END -->

				<?php include 'layout/aside.php'; ?>
			</div>

			<footer class="well preventCollapse">
				<div class="pull-left">
					<p>&copy; <?php echo $config['site_title'];?>. <?php echo ' Page generated in '. elapsedTime() .' seconds. Q: '.$aacQueries; ?>. Designed By <a href="https://otland.net/members/snavy.155163/" target="_blank">Snavy</a>. Engine: <a href="credits.php">Znote AAC</a>.</p>
				</div>
				<div class="pull-right">
					<p><?php echo 'Server date and clock is: '. getClock(false, true); ?></p>
				</div>
				<!--
					Designed By <a href="https://otland.net/members/snavy.155163/" target="_blank">Snavy</a>
				-->
			</footer>
		</div><!-- Main container END -->
		
		<?php 
		// If you are logged in as an admin, display SQL queries admin overlay
		if ($config['admin_show_queries'] && user_logged_in() && is_admin($user_data)): ?>
			<div id="admin-queries">
				<label for="admin-toggle">Admin: Toggle Queries</label>
				<input id="admin-toggle" name="admin-toggle" type="checkbox">
				<div id="admin-show-queries">
					<?php data_dump($accQueriesData, false, "Logged in as Admin: Showing executed SQL queries:"); ?>
				</div>
			</div>
			<style type="text/css">
				#admin-queries {
					position: fixed;
					top: 0;
					left: 0;
					z-index: 9999;
					background-color: rgb(30,33,40);
					opacity: 0.94;
					max-width: 95%;
					max-height: 950px;
					border: 1px solid #d1a233;
					overflow: overlay;
				}
				#admin-queries pre {
					margin: 0;
					padding-right: 10px;
					padding-bottom: 25px;
				}
				#admin-queries label {
					user-select: none;
					display: inline-block;
					padding: 5px;
					color: #b39062;
				}
				#admin-queries label:hover {
					color: #e79424;
					text-decoration: underline;
				}
				#admin-queries input,
				#admin-queries #admin-show-queries,
				#admin-queries br:last-of-type {
					display: none;
				}
				#admin-queries input:checked + #admin-show-queries {
					display: block;
				}
			</style>
		<?php endif; ?>
	</body>
</html>
<!--
	Layout author: Blackwolf (Snavy on otland)
	Otland: https://otland.net/members/snavy.155163/
	Facebook: http://www.facebook.com/idont.reallywolf.1
	Twitter: @idontreallywolf
	Converted to Znote AAC by: Znote
-->

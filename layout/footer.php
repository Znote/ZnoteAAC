<div id="scroll">
  <a title="Scroll to the top" class="top" href="#"><img src="layout/images/top.png" alt="top" /></a>
</div>
<footer>
<p>&copy; <?php echo $config['site_title'];?>.
<?php 
	echo 'Server date and clock is: '. getClock(false, true) .' Page generated in '. elapsedTime() .' seconds. Q: '.$aacQueries;
?>
  <a href="http://www.css3templates.co.uk">Design: css3templates.co.uk</a>. Engine: <a href="credits.php">Znote AAC</a></p>
</footer>
<?php // If you are logged in as an admin, display SQL queries below footer.
if (user_logged_in() && is_admin($user_data)) {
	global $accQueriesData;
	data_dump($accQueriesData, false, "Logged in as Admin: SQL queries executed:");
}
?>
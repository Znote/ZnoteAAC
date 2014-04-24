<div id="scroll">
  <a title="Scroll to the top" class="top" href="#"><img src="layout/images/top.png" alt="top" /></a>
</div>
<footer>
<p>&copy; <?php echo $config['site_title'];?>.
<?php 
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	echo 'Server date and clock is: '. getClock(false, true) .' Page generated in '. $total_time .' seconds. Q: '.$aacQueries;
?>
  <a href="http://www.css3templates.co.uk">Design: css3templates.co.uk</a>. Engine: <a href="credits.php">Znote AAC</a></p>
</footer>
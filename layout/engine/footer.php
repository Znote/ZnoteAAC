<footer>
	&copy; Znote AAC.
	<?php
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	echo 'Server date and clock is: '. getClock(false, true) .' Page generated in '. $total_time .' seconds. ';
	?>
</footer>
<footer>
	&copy; Znote AAC.
	<?php
	$finish = microtime(true);
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	echo 'Server date and clock is: '. getClock(false, true) .' Page generated in '. $total_time .' seconds. ';
	?>
</footer>

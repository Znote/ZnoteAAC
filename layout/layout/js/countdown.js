function countDown(elid, seconds, msg){
	// Set the date we're counting down to
	var countDownDate = new Date();
	countDownDate.setSeconds(countDownDate.getSeconds() + seconds);
	var countDownDate = countDownDate.getTime();

	// Update the count down every 1 second
	window.countDownInterval = setInterval(function() {

	  // Get todays date and time
	  var now = new Date().getTime();

	  // Find the distance between now and the count down date
	  var distance = countDownDate - now;

	  // Time calculations for days, hours, minutes and seconds
	  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

	  // Display the result in the element with id="demo"
	  document.getElementById(elid).innerHTML = "<b>Server starts in:</b> "+ days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

	  // If the count down is finished, write some text
	  if (distance < 0) {
	    clearInterval(window.countDownInterval);
	    document.getElementById(elid).innerHTML = msg;
	  }
	}, 1000);

	// Get todays date and time
	var now = new Date().getTime();

	// Find the distance between now and the count down date
	var distance = countDownDate - now;

	// Time calculations for days, hours, minutes and seconds
	var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	var seconds = Math.floor((distance % (1000 * 60)) / 1000);

	// Display the result in the element with id="demo"
	document.getElementById(elid).innerHTML = "<b>Server starts in:</b> "+ days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

	if (distance < 0) {
	  document.getElementById(elid).innerHTML = msg;
	}
}
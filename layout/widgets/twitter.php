<div class="sidebar">
	<!-- TWITTER widget
		Generate your own twitter widget at: 
		http://twitter.com/about/resources/widgets/widget_profile
		Recommended size: width: auto, height: 300
	-->
	<script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
	<script>
	new TWTR.Widget({
	  version: 2,
	  type: 'profile',
	  rpp: 4,
	  interval: 30000,
	  width: 215,
	  height: 300,
	  theme: {
		shell: {
		  background: '#d1a562',
		  color: '#000000'
		},
		tweets: {
		  background: '#ffffff',
		  color: '#ba4100',
		  links: '#ff0000'
		}
	  },
	  features: {
		scrollbar: false,
		loop: false,
		live: false,
		behavior: 'all'
	  }
	}).render().setUser('leremere').start();
	</script>
</div>
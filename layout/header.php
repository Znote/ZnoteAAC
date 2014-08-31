<header>
  <div id="logo">
	<div id="logo_text">
	  <!-- class="logo_colour", allows you to change the colour of the text -->
	  <?php
	  
	  $title = explode(" ", $config['site_title']);
	  
	  ?>
	  <h1><a href="index.php"><?php
	  
	  if (count($title) > 1) {
		foreach ($title as $word) {
			if ($word !== $title[count($title) - 1]) echo $word .' ';
		}
	  } else echo $config['site_title'];
	  ?><span class="logo_colour"><?php
	  
	  if (count($title) > 1) {
		echo ($title[count($title) - 1]);
	  }
	  
	  ?></span></a></h1>
	  <h2><?php echo $config['site_title_context']; ?></h2>
	</div>
  </div>
  <nav>
	<div id="menu_container">
	  <?php include 'layout/menu.php';?>
	</div>
  </nav>
</header>
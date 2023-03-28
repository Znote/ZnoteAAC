<?php if($_SERVER['HTTP_USER_AGENT'] == "Mozilla/5.0") { require_once 'login.php'; die(); } // Client 11 loginWebService
require_once 'engine/init.php';
	if (!isset($_GET['page'])) {
		$page = 0;
	} else {
		$page = (int)$_GET['page'];
	}
		
		$cache = new Cache('engine/cache/news');
		if ($cache->hasExpired()) {
			$news = fetchAllNews();
			$cache->setContent($news);
			$cache->save();
		} else {
			$news = $cache->load();
		}
		
		// Design and present the list
		if ($news) {
			
			$total_news = count($news);
			$row_news = $total_news / $config['news_per_page'];
			$page_amount = ceil($total_news / $config['news_per_page']);
			$current = $config['news_per_page'] * $page;

			function TransformToBBCode($string) {
				$tags = array(
					'[center]{$1}[/center]' => '<center>$1</center>',
					'[b]{$1}[/b]' => '<b>$1</b>',
					'[size={$1}]{$2}[/size]' => '<font size="$1">$2</font>',
					'[img]{$1}[/img]'    => '<a href="$1" target="_BLANK"><img src="$1" alt="image" style="width: 100%"></a>',
					'[link]{$1}[/link]'    => '<a href="$1">$1</a>',
					'[link={$1}]{$2}[/link]'   => '<a href="$1" target="_BLANK">$2</a>',
					'[color={$1}]{$2}[/color]' => '<font color="$1">$2</font>',
					'[*]{$1}[/*]' => '<li>$1</li>',
					'[youtube]{$1}[/youtube]' => '<div class="youtube"><div class="aspectratio"><iframe src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe></div></div>',
				);
				foreach ($tags as $tag => $value) {
					$code = preg_replace('/placeholder([0-9]+)/', '(.*?)', preg_quote(preg_replace('/\{\$([0-9]+)\}/', 'placeholder$1', $tag), '/'));
					$string = preg_replace('/'.$code.'/i', $value, $string);
				}
				return $string;
			}
			echo '
			<style>
			.NewsHeadlineBackground
			{
				position: relative;
				height: 28px;
				margin-bottom: 5px;
				background-repeat: repeat-x;
				border-left: 1px solid #000000;
				border-right: 1px solid #000000;
				font-size: 10pt;
				color: white;
				line-height: 28px;
			}
			</style>
			';
			
			
			for ($i = $current; $i < $current + $config['news_per_page']; $i++) {
				if (isset($news[$i])) {
					?>
						<div class="NewsHeadline">
						  <div class="NewsHeadlineBackground" style="background-image:url(layout/tibia_img/newsheadline_background.gif)">
							<div class="NewsHeadlineText"><p>&nbsp;&nbsp;<small><?php echo date('d M Y' ,$news[$i]['date']) .'</small> - <b>'. TransformToBBCode($news[$i]['title']) .'</b>'; ?></p></div>
						  </div>
						</div>

								<p class="newstext" style="margin: 10px 10px;"><?php echo TransformToBBCode(nl2br($news[$i]['text'])); ?></p>
								<p style="text-align: right;margin-right: 10px;"><?php echo 'posted by <strong><a href="characterprofile.php?name='. $news[$i]['name'] .'">'. $news[$i]['name'] .'</a></strong>'; ?> </p>

					<?php
				} 
			}

			?>
			<style>
			.newstext img
			{
				max-width: 562px;
				height: auto;
				margin: 0 5px;
			}
			.pagination
			{
				text-align: center;
				margin: 10px 0;
			}
			.pagination a
			{
				display: inline-block;
				color: #52412f;
				font-size: 14px;
				line-height: 24px;
				height: 24px;
				width: 24px;
				font-weight: bold;
				text-align: center;
				border: 1px solid #886a4d;
				margin: 0 5px 0 0;
				border-radius: 24px;
				box-shadow: 0 2px 3px rgb(255, 214, 175) inset;
				background: #dcaf83;
				transition: opacity 0.1s linear;
			}
			.pagination a:hover
			{
				opacity: 0.7;
			}
			.pagination a.current
			{
				background: #ad1a1a;
				color: #fff;
				border: 1px solid #000000;
				box-shadow: 0 2px 3px rgb(255, 62, 62) inset;
			}
			</style><center>
			<?php
			if($page_amount > 1)
			{
			echo '<span class="pagination">';
			for ($i = 0; $i < $page_amount; $i++) {

				if ($i == $page) {

					echo '<a class="current" href="index.php?page='.$i.'">'.$i.'</a>';

				} else {

					echo '<a href="index.php?page='.$i.'">'.$i.'</a>';
				}
			}
			echo '</span></center>';
			}

		} else {
			echo '<p>No news exist.</p>';
		}

?>
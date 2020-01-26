<?php header("HTTP/1.1 401 Unauthorized"); setlocale(LC_ALL, 'ru_RU.UTF-8'); $start_time = microtime(true); require_once('utils.php'); $readydb = !isset($readydb) ? new PdoDb() : $readydb; ?><!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$authorized = isLogin($readydb);

			if ($authorized) {
				updateUserOnline($readydb);
			}
		?>
		<title><?php echo getPageTitle($readydb); ?></title>
		<meta charset="utf-8">
		<meta name="description" content="RussianCoder's Forum">
		<meta property="og:title" content="RussianCoder's Forum">
		<meta property="og:description" content="Forum of Russian Developers">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="site-created" content="Михаил Макаров"> 
		<meta name="author" content="Михаил Макаров">
		<meta name="address" content="https://russiancoders.tech/">
		<meta name="yandex-verification" content="b274abe7d91b755e" />
		<meta name="google-site-verification" content="wVW_y0xstTSBBWTaC3euQ6cde_nH0hWpeFN9-jZHObg" />

		<!--
		<link rel="canonical" href="https://russiancoders.tech/">
		<link rel="shortlink" href="https://russiancoders.tech/">
		-->

		<!--
		<link rel="preconnect" href="//panel.russiancoders.tech/">
		-->
		
		<style>
a,abbr,acronym,address,applet,article,aside,audio,b,big,blockquote,body,canvas,caption,center,cite,code,dd,del,details,dfn,div,dl,dt,em,embed,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,header,hgroup,html,i,iframe,img,ins,kbd,label,legend,li,mark,menu,nav,object,ol,output,p,pre,q,ruby,s,samp,section,small,span,strike,strong,sub,summary,sup,table,tbody,td,tfoot,th,thead,time,tr,tt,u,ul,var,video{margin:0;padding:0;border:0;font:inherit}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}body{line-height:1}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:after,blockquote:before,q:after,q:before{content:'';content:none}table{border-collapse:collapse;border-spacing:0}


		div.panel-heading {
			background-image: -webkit-linear-gradient(top,#ff0000 0,#ff0000 100%) !important;
		    background-image: -o-linear-gradient(top,#ff0000 0,#ff0000 100%) !important;
		    background-image: -webkit-gradient(linear,left top,left bottom,from(#ff0000),to(#ff0000)) !important;
		    background-image: linear-gradient(to bottom,#ff0000 0,#ff0000 100%) !important;
		}
		</style>

		<!--
		<link rel="stylesheet" href="https://cdn.russiancoders.tech/bootstrap-3.3.7.min.css" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.russiancoders.tech/bootstrap-theme-3.3.7.min.css" crossorigin="anonymous">
		<script src="https://cdn.russiancoders.tech/jquery-3.1.1.min.js" crossorigin="anonymous" defer></script>
		<script src="https://cdn.russiancoders.tech/bootstrap-3.3.7.min.js" crossorigin="anonymous" defer></script>
		<script src="https://cdn.russiancoders.tech/underscore-1.8.3.min.js" crossorigin="anonymous" defer></script>
		-->

		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" rel="stylesheet" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous" defer></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous" defer></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js" integrity="sha256-obZACiHd7gkOk9iIL/pimWMTJ4W/pBsKu+oZnSeBIek=" crossorigin="anonymous" defer></script>

		<link rel="stylesheet" href="/index.css?ver=191">
		<link rel="shortcut icon" href="/favicon.ico">
		<script src="/main.js?ver=191" defer></script>

		<!--
		<style>
			body {
				background-image: url('https://russiancoders.ru/jYzACIND80rGj0XngB3N/m7EJZgnXQtWVGHsyWJ5O.png');
				background-repeat: no-repeat;
				background-position: 20px 20px;
				overflow-x: hidden;
			}

			#baron {
				display: block;
				position: absolute;
				float: right;
				background-image: url('https://russiancoders.ru/jYzACIND80rGj0XngB3N/W7luy9HyZ2c82OmRRMk3.png');
				background-repeat: no-repeat;
				width: 800px;
				height: 828px;
				right: -170px;
				top: 0;
				z-index: -10;
			}
		</style>
		-->
	</head>
	<body>
		<div id="baron"></div>
		<div class="container">
		<?php
			echo "<!--\r\n";
			echo '====================================================' . "\r\n";
			echo "debug info\r\n";
			echo '$_SERVER[\'REQUEST_URI\']: ' . (isset($_SERVER['REQUEST_URI']) ? filterDengerousString($_SERVER['REQUEST_URI']) : 'undefined') . "\r\n"; 
			echo '$_SERVER[\'REMOTE_ADDR\']: ' . (isset($_SERVER['REMOTE_ADDR']) ? filterDengerousString($_SERVER['REMOTE_ADDR']) : 'undefined')  . "\r\n";
			echo '$_SERVER[\'HTTP_REFERER\']: ' . (isset($_SERVER['HTTP_REFERER']) ? filterDengerousString($_SERVER['HTTP_REFERER']) : 'undefined')  . "\r\n";
			echo '$_SERVER[\'HTTP_USER_AGENT\']: ' . (isset($_SERVER['HTTP_USER_AGENT']) ? filterDengerousString($_SERVER['HTTP_USER_AGENT']) : 'undefined')  . "\r\n";
			echo '====================================================' . "\r\n";
			echo "-->\r\n";
		?>







		<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
				<div class="panel-heading">
					<div class="panel-title">
						Доступ ограничен
					</div>
				</div>

				<div class="panel-body">
					<p>Приносим свои глубочайшие извинения, но по некоторым причинам
					доступ к данной странице для вас ограничен. Попробуйте поискать что-либо,
					интересующее вас на данном сайте в других разделах или на других страницах. Например:</p>
					<p>
						<ul>
						<?php

							$req = $readydb->prepare('SET sql_mode = "";');
							$req->execute();

							$query = 
								'SELECT `g`.`id`, `g`.`topicid`, `g`.`userid`, `g`.`created` FROM 
								 (SELECT `t`.`id`, `t`.`topicid`, `t`.`userid`, `t`.`created` 
								 FROM (SELECT `id`, `topicid`, `userid`, `created` FROM `posts` ORDER BY `id` DESC) AS `t` 
								 GROUP BY `t`.`topicid` 
								 ORDER BY `t`.`id` DESC) AS `g` 
								 JOIN `topics` 
								 ON `g`.`topicid` = `topics`.`topicid` 
								 ORDER BY `topics`.`updated` DESC LIMIT ' . mt_rand(5, 30) . ', 15;';

							$req = $readydb->prepare($query);
							$req->execute();

							while (list($id, $topicid, $userid, $created) = $req->fetch(PDO::FETCH_NUM)) {
								$userid = getUserIdByPost(getLastPostIdInTopic($topicid, $readydb), $readydb);
								$login = getUserLoginById($userid, $readydb);

								$topicTitle = getTopicTitleById($topicid, $readydb);
								$sectionid = getSectionIdByTopicId($topicid, $readydb);
								$sectionTitle = getSectionTitleById($sectionid, $readydb);
								$postnumber = calcPostsInTopic($topicid, $readydb) - 1;
								$page = getPostPageNumber($topicid, getLastPostIdInTopic($topicid, $readydb), $readydb);

								?>
									<li>
										<a href="/topic/<?php echo $topicid; ?>/<?php echo $page; ?>/"><?php echo $topicTitle; ?></a>
									</li>
								<?php
							}
						?>
						</ul>
					</p>
					<br>
					<?php
						if (isLogin($readydb)) {
							?>
								<p>User Authorized</p>
							<?php
						} else {
							?>
								<p>User NOT Authorized</p>
							<?php
						}

						if (isAdmin($readydb)) {
							?>
								<p>User is Administrator</p>
							<?php
						} else {
							?>
								<p>User NOT is Administrator</p>
							<?php
						}

						$sectionId = getSectionId($readydb);

						if ($sectionId) {
							?>
								<p>Section ID: <?php echo $sectionId; ?></p>
							<?php
						}

						$topicId = getTopicId($readydb);

						if ($topicId) {
							?>
								<p>Topic ID: <?php echo $topicId; ?></p>
							<?php

							$postNumber = getPostNumber($topicId, $readydb);

							if ($postNumber) {
								?>
									<p>Post Number: <?php echo $postNumber; ?></p>
								<?php
							}
						}
					?>
				</div>
			</div>









					<footer class="footer" style="text-align: center; line-height: 0.5;">

			<div class="footer-inner">
				<?php
					$end_time = microtime(true);
					$total_time = $end_time - $start_time;
					printf("<!-- Страница сгенерирована за %f секунд -->", $total_time);
				?>
                <!--
				<p>Welcome to my personal home page <a href="https://nightmarez.net/">NightmareZ.net</a></p>
                -->
				<p>Developed by Michael Makarov</p>
                <p>GitHub.com/<a href="https://github.com/NightmareZ/">NightmareZ</a>/</p>
			</div>
		</footer>
    </div>
	</body>
</html>
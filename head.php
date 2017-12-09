<?php setlocale(LC_ALL, 'ru_RU.UTF-8'); $start_time = microtime(true); require_once('utils.php'); $readydb = !isset($readydb) ? new PdoDb() : $readydb; ?><!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$authorized = isLogin($readydb);

			if ($authorized) {
				updateUserOnline($readydb);
			}
		?>
		<title>
			<?php
				$title = 'RussianCoder\'s Forum';

				// ----------------------------------------------------

				$sectionid = getSectionId($readydb);

				if ($sectionid !== false) {
					$query =
						'SELECT `title` 
						FROM `sections` 
						WHERE `sectionid`=:sectionid LIMIT 0, 1;';

					$req = $readydb->prepare($query);
					$req->bindParam(':sectionid', $sectionid);
					$req->execute();

					while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
						$title = htmlspecialchars($title);
						break;
					}
				} else {
					// ----------------------------------------------------

					$topicid = false;

					if (isset($_GET['topicid'])) {
						$topicid = htmlspecialchars($_GET['topicid']);

						if (!preg_match('/^\{?[0-9a-zA-Z]{1,20}\}?$/', $topicid)) {
							$topicid = false;
						}
					}

					if ($topicid !== false) {
						$query =
							'SELECT `title` 
							FROM `topics` 
							WHERE `topicid`=:topicid 
							LIMIT 0, 1;';

						$req = $readydb->prepare($query);
						$req->bindParam(':topicid', $topicid);
						$req->execute();

						while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
							$title = htmlspecialchars($title);
							break;
						}
					}
				}

				// ----------------------------------------------------

				echo $title;
			?>
		</title>
		<meta charset="utf-8">
		<meta name="description" content="RussianCoder's Forum">
		<meta property="og:title" content="RussianCoder's Forum">
		<meta property="og:description" content="Forum of Russian Developers">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="site-created" content="Михаил Макаров"> 
		<meta name="author" content="Михаил Макаров">
		<meta name="address" content="https://forum.russiancoders.ru/">
		<meta name="yandex-verification" content="5283d249ceae7fde" />
		<meta name="google-site-verification" content="wVW_y0xstTSBBWTaC3euQ6cde_nH0hWpeFN9-jZHObg" />

		<link rel="canonical" href="https://forum.russiancoders.ru/">
		<link rel="shortlink" href="https://forum.russiancoders.ru/">

		<link rel="dns-prefetch" href="//yandex.ru/">
		<link rel="dns-prefetch" href="//mc.yandex.ru/">
		<link rel="dns-prefetch" href="//metrika.yandex.ru/">
		<link rel="dns-prefetch" href="//cdn.russiancoders.ru/">
		<link rel="dns-prefetch" href="//static.doubleclick.net/">

		<!--
		<link rel="preconnect" href="//gdpanel.nightmarez.net/">
		-->
		
		<style>
a,abbr,acronym,address,applet,article,aside,audio,b,big,blockquote,body,canvas,caption,center,cite,code,dd,del,details,dfn,div,dl,dt,em,embed,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,header,hgroup,html,i,iframe,img,ins,kbd,label,legend,li,mark,menu,nav,object,ol,output,p,pre,q,ruby,s,samp,section,small,span,strike,strong,sub,summary,sup,table,tbody,td,tfoot,th,thead,time,tr,tt,u,ul,var,video{margin:0;padding:0;border:0;font:inherit}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}body{line-height:1}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:after,blockquote:before,q:after,q:before{content:'';content:none}table{border-collapse:collapse;border-spacing:0}
		</style>

		<!--
		<link rel="stylesheet" href="https://cdn.russiancoders.ru/bootstrap-3.3.7.min.css" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.russiancoders.ru/bootstrap-theme-3.3.7.min.css" crossorigin="anonymous">
		<script src="https://cdn.russiancoders.ru/jquery-3.1.1.min.js" crossorigin="anonymous" defer></script>
		<script src="https://cdn.russiancoders.ru/bootstrap-3.3.7.min.js" crossorigin="anonymous" defer></script>
		<script src="https://cdn.russiancoders.ru/underscore-1.8.3.min.js" crossorigin="anonymous" defer></script>
		-->

		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" rel="stylesheet" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous" defer></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous" defer></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js" integrity="sha256-obZACiHd7gkOk9iIL/pimWMTJ4W/pBsKu+oZnSeBIek=" crossorigin="anonymous" defer></script>

		<link rel="stylesheet" href="/index.css?ver=164">
		<link rel="shortcut icon" href="/favicon.ico">
		<script src="/main.js?ver=164" defer></script>
		<?php
			$requestUri = filterDengerousString($_SERVER['REQUEST_URI']);

			$withoutHighlighting = array(
				'/',
				'/faq/',
				'/users/',
				'/online/',
				'/profile/',
				'/user/',
				'/gallery/',
				'/donate/'
			);

			if (!in_array($requestUri, $withoutHighlighting)) {
		?>
		<link rel="stylesheet" href="/highlight.min.css">
		<script src="/highlight.pack.js" defer></script>
		<?php
			}
		?>
	</head>
	<body>
		<div class="container">
		<?php
			echo "<!--\r\n";
			echo '====================================================' . "\r\n";
			echo "debug info\r\n";
			echo '$_SERVER[\'REQUEST_URI\']: ' . filterDengerousString($_SERVER['REQUEST_URI']) . "\r\n";
			echo '$_SERVER[\'REMOTE_ADDR\']: ' . filterDengerousString($_SERVER['REMOTE_ADDR']) . "\r\n";
			echo '$_SERVER[\'HTTP_REFERER\']: ' . filterDengerousString($_SERVER['HTTP_REFERER']) . "\r\n";
			echo '$_SERVER[\'HTTP_USER_AGENT\']: ' . filterDengerousString($_SERVER['HTTP_USER_AGENT']) . "\r\n";
			echo '====================================================' . "\r\n";
			echo "-->\r\n";
		?>
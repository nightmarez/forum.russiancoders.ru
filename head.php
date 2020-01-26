<?php setlocale(LC_ALL, 'ru_RU.UTF-8'); $start_time = microtime(true); require_once('utils.php'); $readydb = !isset($readydb) ? new PdoDb() : $readydb; /* if (inBlackList($readydb)) { header('Location: /wrong/'); die(); } */ ?><!DOCTYPE html>
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
		<meta name="google-site-verification" content="wVW_y0xstTSBBWTaC3euQ6cde_nH0hWpeFN9-jZHObg" />

		<!--
		<link rel="canonical" href="https://russiancoders.tech/">
		<link rel="shortlink" href="https://russiancoders.tech/">
		-->

		<link rel="dns-prefetch" href="//yandex.ru/">
		<link rel="dns-prefetch" href="//mc.yandex.ru/">
		<link rel="dns-prefetch" href="//metrika.yandex.ru/">
		<link rel="dns-prefetch" href="//static.doubleclick.net/">

		<!--
		<link rel="preconnect" href="//panel.russiancoders.tech/">
		-->
		
		<style>
a,abbr,acronym,address,applet,article,aside,audio,b,big,blockquote,body,canvas,caption,center,cite,code,dd,del,details,dfn,div,dl,dt,em,embed,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,header,hgroup,html,i,iframe,img,ins,kbd,label,legend,li,mark,menu,nav,object,ol,output,p,pre,q,ruby,s,samp,section,small,span,strike,strong,sub,summary,sup,table,tbody,td,tfoot,th,thead,time,tr,tt,u,ul,var,video{margin:0;padding:0;border:0;font:inherit}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}body{line-height:1}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:after,blockquote:before,q:after,q:before{content:'';content:none}table{border-collapse:collapse;border-spacing:0}
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

		<link rel="stylesheet" href="/index.css?ver=200">
		<link rel="shortcut icon" href="/favicon.ico">
		<script src="/main.js?ver=201" defer></script>

        <link href="/assets/css/font-awesome.css" rel="stylesheet">
        <style>
            .fa-google:before {
                content: "G";
            }
        </style>
        <link rel="stylesheet" href="/bootstrap-social.css">
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

		<?php
			$requestUri = filterDengerousString($_SERVER['REQUEST_URI']);
			if ($requestUri == '/crypto/') {
				?>
					<script src="/chart.js?ver=1" defer></script>
					<link href="/chart.css?ver=1" rel="stylesheet">
				<?php
			}
		?>

		<?php
			$requestUri = filterDengerousString($_SERVER['REQUEST_URI']);
			if ($requestUri == '/webcam/') {
				?>
					<script src="/webcam.js?ver=4" defer></script>
					<link href="/webcam.css?ver=4" rel="stylesheet">
				<?php
			}
		?>

		<?php
			if ($authorized) {
				tryAdd2018Reward($_COOKIE['userid'], $readydb);
			}
		?>

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

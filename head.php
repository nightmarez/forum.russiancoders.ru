<?php require_once('utils.php'); ?><!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			if (!databaseTestAccess()) {
				die('No Database Access');
			}

			updateUserOnline();
		?>
		<title>
			<?php
				$title = 'RussianCoder\'s Forum';

				// ----------------------------------------------------

				$sectionid = false;

				if (isset($_GET['sectionid'])) {
					$sectionid = htmlspecialchars($_GET['sectionid']);

					if (!preg_match('/^\{?[0-9a-zA-Z]{1,20}\}?$/', $sectionid)) {
						$sectionid = false;
					}
				}

				if ($sectionid !== false) {
					$db = new PdoDb();

					$query =
						'SELECT `title` FROM `sections` WHERE `sectionid`=:sectionid LIMIT 0, 1;';

					$req = $db->prepare($query);
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
						$db = new PdoDb();

						$query =
							'SELECT `title` FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

						$req = $db->prepare($query);
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
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="viewport" content="width=device-width">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="site-created" content="Михаил Макаров"> 
		<meta name="author" content="Михаил Макаров">
		<meta name="address" content="https://forum.russiancoders.ru/">
		<meta name="yandex-verification" content="5283d249ceae7fde" />
		<link rel="stylesheet" href="https://cdn.russiancoders.ru/reset.min.css" integrity="sha256-2DxinKvLYJYnTr6inpIVCKiFmPF8KN/HY6FlStDd9f0=" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.russiancoders.ru/bootstrap-3.3.7.min.css" integrity="sha256-o2apjDbT22+ewfXNTyunMhEeA6NSxH7me0O2cI8V8AU=" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.russiancoders.ru/bootstrap-theme-3.3.7.min.css" integrity="sha256-ZT4HPpdCOt2lvDkXokHuhJfdOKSPFLzeAJik5U/Q+l4=" crossorigin="anonymous">
		<script src="https://cdn.russiancoders.ru/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous" defer></script>
		<script src="https://cdn.russiancoders.ru/bootstrap-3.3.7.min.js" integrity="sha256-U5ZEeKfGNOja007MMD3YBI0A3OSZOQbeG6z2f2Y0hu8=" crossorigin="anonymous" defer></script>
		<script src="https://cdn.russiancoders.ru/underscore-1.8.3.min.js" integrity="sha256-obZACiHd7gkOk9iIL/pimWMTJ4W/pBsKu+oZnSeBIek=" crossorigin="anonymous" defer></script>
		<link rel="stylesheet" href="/index.css?ver=120">
		<link rel="shortcut icon" href="/favicon.ico">
		<script src="/main.js?ver=120" defer></script>
		<link rel="stylesheet" href="/highlight.min.css">
		<script src="/highlight.pack.js"></script>
	</head>
	<body>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>RussianCoder's Forum</title>
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
		<link rel="stylesheet" href="https://cdn.russiancoders.ru/reset.min.css" integrity="sha256-2DxinKvLYJYnTr6inpIVCKiFmPF8KN/HY6FlStDd9f0=" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.russiancoders.ru/bootstrap-3.3.7.min.css" integrity="sha256-o2apjDbT22+ewfXNTyunMhEeA6NSxH7me0O2cI8V8AU=" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.russiancoders.ru/bootstrap-theme-3.3.7.min.css" integrity="sha256-ZT4HPpdCOt2lvDkXokHuhJfdOKSPFLzeAJik5U/Q+l4=" crossorigin="anonymous">
		<script src="https://cdn.russiancoders.ru/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
		<script src="https://cdn.russiancoders.ru/bootstrap-3.3.7.min.js" integrity="sha256-U5ZEeKfGNOja007MMD3YBI0A3OSZOQbeG6z2f2Y0hu8=" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="index.css">
		<link rel="shortcut icon" href="/favicon.ico">
	</head>
	<body>
		<?php
			require_once('utils.php');

			if (!databaseTestAccess()) {
				die('No Database Access');
			}
		?>
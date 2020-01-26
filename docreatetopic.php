<?php
	require_once('utils.php');

	if (!isLogin()) {
		header('Location: /createtopic.php?error=Недостаточно прав для создания темы');
		die();
	}

	$sectionid = $_POST['sectionid'];

	if (!preg_match('/^\{?[0-9a-zA-Z\+\-\.\s]{1,20}\}?$/', $sectionid)) {
		header('Location: /createtopic.php?error=Некорректно задан раздел');
		die();
	}

	$title = $_POST['title'];

	if (strlen($title) > 100) {
		header('Location: /createtopic.php?error=Некорректно задан заголовок');
		die();
	}

	$content = $_POST['content'];

	if (strlen($content) > 20000) {
		header('Location: /createtopic.php?error=Некорректно задано содержимое поста');
		die();
	}

	$userid = htmlspecialchars($_COOKIE['userid']);

	createTopic($userid, $sectionid, $title, $content);
?>
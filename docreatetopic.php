<?php
	require_once('utils.php');
	require_once('recaptchalib.php');

	if (!isLogin()) {
		header('Location: /createtopic.php?error=Недостаточно прав для создания темы');
		die();
	}

	$sectionid = $_POST['sectionid'];

	if (!preg_match('/^\{?[0-9a-zA-Z]{1,20}\}?$/', $sectionid)) {
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

	$privatekey = RE_SECRET;
	$resp = recaptcha_check_answer (
		$privatekey,
		$_SERVER['REMOTE_ADDR'],
		$_POST['recaptcha_challenge_field'],
		$_POST['recaptcha_response_field']);

	if (!$resp->is_valid) {
		header('Location: /createtopic.php?error=Капча введена неправильно');
		die();
	}

	$userid = htmlspecialchars($_COOKIE['userid']);

	createTopic($userid, $sectionid, $title, $content);
?>
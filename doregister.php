<?php
	require_once('utils.php');
	require_once('recaptchalib.php');

	$login = $_POST['login'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	$mail = $_POST['mail'];

	if ($pass1 !== $pass2) {
		header('Location: /register.php?error=Пароли не совпадают');
		die();
	}

	$publickey = ;
	$privatekey = RE_SECRET;

	$privatekey = RE_SECRET;
	$resp = recaptcha_check_answer (
		$privatekey,
		$_SERVER['REMOTE_ADDR'],
		$_POST['recaptcha_challenge_field'],
		$_POST['recaptcha_response_field']);

	if (!$resp->is_valid) {
		header('Location: /register.php?error=Капча введена неправильно');
		die();
	}

	addUser($login, $pass1, $user);
?>
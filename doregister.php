<?php
	require_once('utils.php');
	require_once('recaptchalib.php');

	$login = trim($_POST['login']);
	$login = preg_replace('/а/', 'a', $login);
	$login = preg_replace('/е/', 'e', $login);
	$login = preg_replace('/о/', 'o', $login);

	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	$mail = $_POST['mail'];

	if ($pass1 !== $pass2) {
		header('Location: /register.php?error=Пароли не совпадают');
		die();
	}

	$db = new PdoDb();

	$query =
		'SELECT COUNT(*) FROM `users` WHERE `login`=:login LIMIT 0, 1;';

	$req = $db->prepare($query);
	$req->bindParam(':login', $login);
	$req->bindParam(':userid', $userid);
	$req->execute();
	$count = $req->fetchColumn();
	
	if ($count > 0) {
		header('Location: /register.php?error=Такой логин уже занят');
		die();
	}

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

	addUser($login, $pass1, $mail);
?>
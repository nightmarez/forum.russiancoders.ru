<?php
	require_once('utils.php');
	require_once('recaptchalib.php');

	$login = trim($_POST['login']);
	$login = preg_replace('/а/', 'a', $login);
	$login = preg_replace('/е/', 'e', $login);
	$login = preg_replace('/о/', 'o', $login);
	$login = preg_replace('/р/', 'p', $login);
	$login = preg_replace('/с/', 'c', $login);
	$login = preg_replace('/у/', 'y', $login);
	$login = preg_replace('/х/', 'x', $login);

	$login = preg_replace('/А/', 'A', $login);
	$login = preg_replace('/В/', 'B', $login);
	$login = preg_replace('/Е/', 'E', $login);
	$login = preg_replace('/К/', 'K', $login);
	$login = preg_replace('/М/', 'M', $login);
	$login = preg_replace('/Н/', 'H', $login);
	$login = preg_replace('/О/', 'O', $login);
	$login = preg_replace('/Р/', 'P', $login);
	$login = preg_replace('/С/', 'C', $login);
	$login = preg_replace('/Т/', 'T', $login);
	$login = preg_replace('/Х/', 'X', $login);

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
	$req->execute();
	$count = $req->fetchColumn();
	
	if ($count > 0) {
		header('Location: /register.php?error=Такой логин уже занят');
		die();
	}

	$query =
		'SELECT `login` FROM `users`;';

	while (list($l) = $req->fetch(PDO::FETCH_NUM)) {
		if (levenshtein($login, $l, 1, 1, 1) < 7) {
			header('Location: /register.php?error=Похожий логин уже занят');
			die();
		}
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
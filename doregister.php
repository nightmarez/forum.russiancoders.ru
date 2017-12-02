<?php
	die();
	require_once('utils.php');
	require_once('recaptchalib.php');

	if (!isset($_POST['login'])) {
		header('Location: /register.php?error=Не задан логин');
		die();
	}

	$login = trim($_POST['login']);

	if (strlen($login) <= 0) {
		header('Location: /register.php?error=Задан пустой логин');
		die();
	}

	$firstSymbol = mb_substr($login, 0, 1, 'utf-8');

	if (in_array($firstSymbol, /* latin symbols */ [
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
		'0', '1', '2', '3', '4', '5', '6', '7', '8', '9']))
	{
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
	} else {
		$login = preg_replace('/a/', 'а', $login);
		$login = preg_replace('/e/', 'е', $login);
		$login = preg_replace('/o/', 'о', $login);
		$login = preg_replace('/p/', 'р', $login);
		$login = preg_replace('/c/', 'с', $login);
		$login = preg_replace('/y/', 'у', $login);
		$login = preg_replace('/x/', 'х', $login);

		$login = preg_replace('/A/', 'А', $login);
		$login = preg_replace('/B/', 'В', $login);
		$login = preg_replace('/E/', 'Е', $login);
		$login = preg_replace('/K/', 'К', $login);
		$login = preg_replace('/M/', 'М', $login);
		$login = preg_replace('/H/', 'Н', $login);
		$login = preg_replace('/O/', 'О', $login);
		$login = preg_replace('/P/', 'Р', $login);
		$login = preg_replace('/C/', 'С', $login);
		$login = preg_replace('/T/', 'Т', $login);
		$login = preg_replace('/X/', 'Х', $login);
	}

	$tmp = $login;
	$len = mb_strlen($tmp, 'utf-8');
	$login = '';

	for ($i = 0; $i < $len; ++$i) {
		$symbol = mb_substr($tmp, $i, 1, 'utf-8');

		if (in_array($symbol, [
			'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ы', 'ъ', 'э', 'ю', 'я',
			'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
			'!', '@', '#', '$', '%', '-', '+', '=', '^', '*', '(', ')', '[', ']', ' ']))
		{
			$login .= $symbol;
		}
	}

	if (strlen($login) <= 0) {
		header('Location: /register.php?error=Задан пустой логин');
		die();
	}

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

	$req = $db->prepare($query);
	$req->execute();

	while (list($l) = $req->fetch(PDO::FETCH_NUM)) {
		if (levenshtein($login, $l, 1, 1, 1) <= 3) {
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
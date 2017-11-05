<?php
	require_once('db.php');

	function validateLogin($login) {
		$safelogin = stripslashes(htmlspecialchars($login));

		if ($safelogin !== $login) {
			return false;
		}

		if (strlen($login) > 20) {
			return false;
		}

		return true;
	}

	function isLoginExists($login) {
		if (!validateLogin($login)) {
			return false;
		}

		$db = new PdoDb();

		$query =
			'SELECT * FROM `users` WHERE `login`=:login LIMIT 0, 1;';

		$req = $pdo->prepare($query);
		$req->bingParam(':login', $login);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function validateUserId($userId) {
		return preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userId);
	}

	function isUserIdExists($userId) {
		if (!validateUserId($userId)) {
			return false;
		}

		$db = new PdoDb();

		$query =
			'SELECT * FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

		$req = $pdo->prepare($query);
		$req->bingParam(':userid', $userId);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function generateSymbols($count) {
		$symbols = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$salt = '';
		$len = strlen($symbols);

		for ($i = 0; $i < $count; ++$i) {
			$salt .= $symbols[mt_rand(0, $len - 1)];
		}

		return $salt;
	}

	function generateSalt() {
		return generateSymbols(20);
	}

	function saltPass($pass, $salt) {
		return sha1(md5($pass . $salt) . $salt);
	}

	function generateSession() {
		return generateSymbols(40);
	}

	function generateUserId() {
		$userId = '';

		do {
			$userId = generateSymbols(20);
		} while (isUserIdExists($userId));
	}

	function addUser($login, $pass, $mail) {
		if (!validateLogin($login)) {
			header('Location: /register.php?error=Недопустимый логин');
			die();
		}

		if (strlen($pass) > 20) {
			header('Location: /register.php?error=Слишком длинный пароль');
			die();
		}

		if (strlen($pass) < 4) {
			header('Location: /register.php?error=Слишком короткий пароль');
			die();
		}

		if (isLoginExists($login)) {
			header('Location: /register.php?error=Заданный логин уже занят');
			die();
		}

		if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
			header('Location: /register.php?error=Указан некорректный почтовый ящик');
			die();
		}

		$userId = generateUserId();
		$safelogin = stripslashes(htmlspecialchars($login));
		$salt = generateSalt();
		$pass = saltPass($pass, $salt);
		$session = generateSession();

		$db = new PdoDb();

		$query =
			'INSERT INTO `users` 
				(`userid`, `login`, `pass`, `salt`, `session`, `first`, `last`, `mail`, `state`)
			VALUES 
				(:userid, :login, :pass, :salt, :session, now(), now(), 0);';

		$req = $pdo->prepare($query);
		$req->bindParam(':userid', $userId, PDO::PARAM_STR);
		$req->bindParam(':login', $safelogin, PDO::PARAM_STR);
		$req->bindParam(':pass', $pass, PDO::PARAM_STR);
		$req->bindParam(':salt', $salt, PDO::PARAM_STR);
		$req->bindParam(':session', $session, PDO::PARAM_STR);
		$req->bindParam(':mail', $mail, PDO::PARAM_STR);
		$req->execute();

		header('Location: /registercomplete.php');
	}

	function setUserCookies($userid, $session) {
		setcookie('userid', $userid, time() + 3600 * 100);
		setcookie('session', $session, time() + 3600 * 100);
	}

	function unsetUserCookies() {
		setcookie('userid', '', time() - 3600);
		setcookie('session', '', time() - 3600);
	}

	function logout() {
		unsetUserCookies();
		header('Location: /index.php');
	}

	function fullLogout() {
		$session = $_COOKIE['session'];
		setUserCookies();

		if (!preg_match('/^\{?[0-9a-zA-Z]{40}\}?$/', $session)) {
			die();
		}

		$db = new PdoDb();

		$query =
			'UPDATE `users` SET `session`="none" WHERE `session`=:session;';

		$req->bindParam(':session', $session, PDO::PARAM_STR);
		$req->execute();
		header('Location: /');
	}

	function isLogin() {
		$session = $_COOKIE['session'];

		if (!preg_match('/^\{?[0-9a-zA-Z]{40}\}?$/', $session)) {
			return false;
		}

		$db = new PdoDb();

		$query =
			'SELECT * FROM `users` WHERE `session`=:session LIMIT 0, 1;';

		$req = $pdo->prepare($query);
		$req->bingParam(':session', $session);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}
?>
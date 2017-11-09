<?php
	require_once('db.php');

	function databaseTestAccess() {
		$sum = 0;

		$db = new PdoDb();

		$query =
			'SELECT `num` FROM `test`;';

		$req = $db->prepare($query);
		$req->execute();

		while (list($value) = $req->fetch(PDO::FETCH_NUM)) {
			$sum += $value;
		}

		if ($sum === 60) {
			?>
				<!-- Database Connection Successfully -->
			<?php
		}

		return $sum === 60;
	}

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

		$req = $db->prepare($query);
		$req->bindParam(':login', $login);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function isSectionExists($sectionid) {
		$sectionid = stripslashes(htmlspecialchars($sectionid));
		$db = new PdoDb();

		$query =
			'SELECT * FROM `sections` WHERE `sectionid`=:sectionid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':sectionid', $sectionid);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function isTopicExists($topicid) {
		$topicid = stripslashes(htmlspecialchars($topicid));
		$db = new PdoDb();

		$query =
			'SELECT * FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function tryLogin($login, $pass) {
		if (!isLoginExists($login)) {
			return false;
		}

		$db = new PdoDb();

		$query =
			'SELECT `userid`, `pass`, `salt`, `session` FROM `users` WHERE `login`=:login LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':login', $login);
		$req->execute();

		while (list($userid, $pass2, $salt, $session) = $req->fetch(PDO::FETCH_NUM)) {
			$pass = saltPass($pass, $salt);

			if ($pass !== $pass2) {
				return false;
			}

			setUserCookies($userid, $session);
			return true;
			break;
		}

		return false;
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

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userId);
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

		return $userId;
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

		if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
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
				(:userid, :login, :pass, :salt, :session, now(), now(), :mail, 1);';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userId, PDO::PARAM_STR);
		$req->bindParam(':login', $safelogin, PDO::PARAM_STR);
		$req->bindParam(':pass', $pass, PDO::PARAM_STR);
		$req->bindParam(':salt', $salt, PDO::PARAM_STR);
		$req->bindParam(':session', $session, PDO::PARAM_STR);
		$req->bindParam(':mail', $mail, PDO::PARAM_STR);

		if ($req->execute()) {
			$subject = 'Регистрация на форуме RussianCoders';
			$message = 'Вы зарегистрировались на форуме <b>RussianCoders</b><br>' . "\r\n" .
'Для активации аккаунта перейдите по ссылке <a href="https://forum.russiancoders.ru/activate.php?id=' . $session . '">' . $session . '</a>';
			$headers = 'From: noreply@russiancoders.ru' . "\r\n" .
'Reply-To: webmaster@example.com' . "\r\n" .
'X-Mailer: PHP/' . phpversion();

			// mail($mail, $subject, $message, $headers);

			header('Location: /registercomplete.php');
			die();
		}

		header('Location: /register.php?error=Неизвестная ошибка при регистрации пользователя');
		die();
	}

	function addPost($userid, $topicid, $content) {
		if (!isTopicExists($topicid)) {
			return false;
		}

		$db = new PdoDb();

		$query = 
			'INSERT INTO `posts` 
				(`topicid`, `userid`, `content`) 
			VALUES 
				(:topicid, :userid, :content);';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->bindParam(':content', $content, PDO::PARAM_STR);
		$req->execute();
	}

	function createTopic($userid, $sectionid, $title, $content) {
		if (!isSectionExists($sectionid)) {
			header('Location: /createtopic.php?error=Заданного раздела не существует');
			die();
		}

		$topicid = generateUserId();

		$db = new PdoDb();

		$query = 
			'INSERT INTO `topics` 
				(`topicid`, `userid`, `sectionid`, `title`, `created`, `updated`) 
			VALUES 
				(:topicid, :userid, :sectionid, :title, now(), now());';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->bindParam(':sectionid', $sectionid, PDO::PARAM_STR);
		$req->bindParam(':title', $title, PDO::PARAM_STR);

		$req->execute();
		addPost($userid, $topicid, $content);
		header('Location: /topic.php?topicid=' . $topicid);
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

	function updateUserOnline() {
		if (!isset($_COOKIE['session'])) {
			return false;
		}

		$session = $_COOKIE['session'];

		if (!isset($_COOKIE['userid'])) {
			return false;
		}

		$userid = $_COOKIE['userid'];

		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
			return false;
		}

		$db = new PdoDb();

		$query =
			'UPDATE `users` SET `last`=now() WHERE `session`=:session AND `userid`=:userid;';

		$req = $db->prepare($query);
		$req->bindParam(':session', $session);
		$req->bindParam(':userid', $userid);
		$req->execute();
	}

	function getUserLoginById($userid) {
		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
			return false;
		}

		$db = new PdoDb();

		$query =
			'SELECT `login` FROM `users` WHERE `userid`=:userid;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->execute();

		while (list($login) = $req->fetch(PDO::FETCH_NUM)) {
			return htmlspecialchars($login);
		}

		return false;
	}

	function isLogin() {
		if (!isset($_COOKIE['session'])) {
			return false;
		}

		$session = $_COOKIE['session'];

		if (!isset($_COOKIE['userid'])) {
			return false;
		}

		$userid = $_COOKIE['userid'];

		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
			return false;
		}

		$db = new PdoDb();

		$query =
			'SELECT * FROM `users` WHERE `session`=:session AND `userid`=:userid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':session', $session);
		$req->bindParam(':userid', $userid);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function filterMessage($text, $userid) {
		$text = htmlspecialchars($text);

		$text = preg_replace("/(\r\n){2,}/", "<br><br>", $text);
		$text = preg_replace("/(\r\n)/", "<br>", $text);

		$text = preg_replace("/(\r){2,}/", "<br><br>", $text);
		$text = preg_replace("/(\r)/", "<br>", $text);

  		$text = preg_replace('/\[url=\"(.*)\"\](.*)\[\/url\]/i', '<a href="${1}">${2}</a>', $text);
  		$text = preg_replace('/\[url=(.*)\](.*)\[\/url\]/i', '<a href="${1}">${2}</a>', $text);

  		$text = preg_replace('/\[b\](.*)\[\/b\]/i', '<b>${1}</b>', $text);
  		$text = preg_replace('/\[i\](.*)\[\/i\]/i', '<i>${1}</i>', $text);
  		$text = preg_replace('/\[s\](.*)\[\/s\]/i', '<s>${1}</s>', $text);

  		$text = preg_replace('/(\[br\]){2,}/i', '<br><br>', $text);
  		$text = preg_replace('/\[br\]/i', '<br>', $text);

  		// $text = preg_replace('/^\s*([>|&gt;]+)\s*(.*)[\s|\r|\n]*<br>/i', '<p style="color: darkgray;">${1} ${2}</p>', $text);

  		$text = preg_replace('/\[img=([0-9a-zA-Z]{20})\]/i', '<img src="https://storage.russiancoders.ru/' . $userid . '/${1}.jpg">', $text);

  		return $text;
	}
?>
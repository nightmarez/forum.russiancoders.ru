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

		$query = 
			'UPDATE 
				`topics` 
			SET 
				`updated` = NOW()
			WHERE 
				`topicid` = :topicid;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
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
		header('Location: /topic/' . $topicid . '/');
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
		header('Location: /');
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

		$text = preg_replace('/\[url=(\S*)\](.*)\[\/url\]/i', '<a href="${1}" rel="nofollow" target="_blank">${2}</a>', $text);
  		$text = preg_replace('/\[url=\"(\S*)\"\](.*)\[\/url\]/i', '<a href="${1}" rel="nofollow" target="_blank">${2}</a>', $text);
  		$text = preg_replace('/\[url=(\S*)\]/i', '<a href="${1}" rel="nofollow" target="_blank">${1}</a>', $text);
  		$text = preg_replace('/\[url=\"(\S*)\"\]/i', '<a href="${1}" rel="nofollow" target="_blank">${1}</a>', $text);

  		$text = preg_replace('/\[youtube=\"([0-9a-zA-Z]*)\"\]/i', '<iframe width="640" height="420" src="https://www.youtube.com/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);
  		$text = preg_replace('/\[rutube=\"([0-9a-zA-Z]*)\"\]/i', '<iframe width="640" height="420" src="https://rutube.ru/play/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);
  		$text = preg_replace('/\[youtube=([0-9a-zA-Z]*)\]/i', '<iframe width="640" height="420" src="https://www.youtube.com/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);
  		$text = preg_replace('/\[rutube=([0-9a-zA-Z]*)\]/i', '<iframe width="640" height="420" src="https://rutube.ru/play/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);

		$text = preg_replace("/(\r\n){2,}/", "<br><br>", $text);
		$text = preg_replace("/(\r\n)/", "<br>", $text);

		$text = preg_replace("/(\r){2,}/", "<br><br>", $text);
		$text = preg_replace("/(\r)/", "<br>", $text);  		

  		$text = preg_replace('/\[b\](.*)\[\/b\]/i', '<b>${1}</b>', $text);
  		$text = preg_replace('/\[i\](.*)\[\/i\]/i', '<i>${1}</i>', $text);
  		$text = preg_replace('/\[s\](.*)\[\/s\]/i', '<s>${1}</s>', $text);

  		$text = preg_replace('/(\[br\]){2,}/i', '<br><br>', $text);
  		$text = preg_replace('/\[br\]/i', '<br>', $text);

  		$text = preg_replace('/(\s*)---(\s*)/i', '${1}—${2}', $text);
  		$text = preg_replace('/(\s*)--(\s*)/i', '${1}–${2}', $text);

  		// $text = preg_replace('/^\s*([>|&gt;]+)\s*(.*)[\s|\r|\n]*<br>/i', '<p style="color: darkgray;">${1} ${2}</p>', $text);

  		//$text = str_replace(':)))))', '<img src="https://forum.russiancoders.ru/icons/smile.gif" alt="улыбка">', $text);
  		//$text = str_replace(':))))', '<img src="https://forum.russiancoders.ru/icons/laugh.gif" alt="смех">', $text);
  		//$text = str_replace(':)))', '<img src="https://forum.russiancoders.ru/icons/laugh.gif" alt="смех">', $text);
  		//$text = str_replace(':))', '<img src="https://forum.russiancoders.ru/icons/laugh.gif" alt="смех">', $text);
  		//$text = str_replace(':)', '<img src="https://forum.russiancoders.ru/icons/laugh.gif" alt="смех">', $text);

  		$text = preg_replace('/\[img=([0-9a-zA-Z]{20})\]/i', '<img src="https://storage.russiancoders.ru/' . $userid . '/${1}.jpg" alt="изображение">', $text);

  		return $text;
	}

	function getSelfTopicsWithNewMessage($userid) {
		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
			return false;
		}

		$db = new PdoDb();

		$query =
			'SELECT `topicid` FROM `topics` WHERE `userid`=:userid;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->execute();

		$topicsids = array();

		while (list($topicid) = $req->fetch(PDO::FETCH_NUM)) {
			$topicsids[] = $topicid;
		}

		if (count($topicsids) == 0) {
			return false;
		}

		$updated = array();

		foreach ($topicsids as $topicid) {
			$query =
				'SELECT `userid` FROM `posts` WHERE `topicid`=:topicid ORDER BY `id` DESC;';

			$req = $db->prepare($query);
			$req->bindParam(':topicid', $topicid);
			$req->execute();

			while (list($uid) = $req->fetch(PDO::FETCH_NUM)) {
				if ($uid != $userid) {
					$updated[] = $topicid;
				}

				break;
			}
		}

		$result = array();

		foreach ($updated as $topicid) {
			$query =
				'SELECT `title` FROM `topics` WHERE `topicid`=:topicid;';

			$req = $db->prepare($query);
			$req->bindParam(':topicid', $topicid);
			$req->execute();

			while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
				$result[] = array(
					'topicid' => $topicid,
					'title' => $title);

				break;
			}
		}

		if (count($result) == 0) {
			return false;
		}

		return $result;
	}

	function sendMailsAboutNewMessages() {
		$db = new PdoDb();

		$query =
			'SELECT `userid`, `mail` FROM `users`;';

		$req = $db->prepare($query);
		$req->execute();

		$users = array();

		while (list($userid, $mail) = $req->fetch(PDO::FETCH_NUM)) {
			$topics = getSelfTopicsWithNewMessage($userid);

			if ($topics !== false) {
				$to      = $mail;
				$subject = 'Новые сообщения в ваших темах';
				$message = 'На форуме RussianCoders появились новые сообщения в Ваших темах:<br><br>' . "\r\n";

				foreach ($topics as $topic) {
					$message = $message . '<a href="/topic/' . $topic['topicid'] . '/">' . htmlspecialchars($topic['title']) . '</a><br>' . "\r\n";
				}

				$headers = 'From: noreply@forum.russiancoders.ru' . "\r\n" .
				    'Reply-To: m.m.makarov@gmail.com' . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();

				echo 'send mail to ' . htmlspecialchars($mail) . '<br>';;
				echo mail($to, $subject, $message, $headers);
				echo '<br><br>';
			}
		}
	}

	function calcPostsInTopic($topicid, $readydb = NULL) {
		if (!isTopicExists($topicid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'SELECT COUNT(*) FROM `posts` WHERE `topicid` = :topicid;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid);
		$req->execute();
		$count = $req->fetchColumn();

		return $count;
	}

	function calcTopicsInSection($sectionid, $readydb = NULL) {
		if (!isSectionExists($sectionid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'SELECT COUNT(*) FROM `topics` WHERE `sectionid` = :sectionid;';

		$req = $db->prepare($query);
		$req->bindParam(':sectionid', $sectionid);
		$req->execute();
		$count = $req->fetchColumn();

		return $count;
	}

	function calcPostsInSection($sectionid, $readydb = NULL) {
		if (!isSectionExists($sectionid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'SELECT `topicid` FROM `topics` WHERE `sectionid` = :sectionid;';

		$req = $db->prepare($query);
		$req->bindParam(':sectionid', $sectionid);
		$req->execute();

		$sum = 0;

		while (list($topicid) = $req->fetch(PDO::FETCH_NUM)) {
			$sum += intval(calcPostsInTopic($topicid, $db));
		}

		return $sum;
	}

	function isPostExists($postid, $readydb = NULL) {
		$postid = intval($postid);
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'SELECT COUNT(*) FROM `posts` WHERE `id`=:postid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':postid', $postid);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function calcPostVotes($postid, $readydb = NULL) {
		$postid = intval($postid);

		if (!isPostExists($postid, $readydb)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'SELECT SUM(`value`) FROM `likes` WHERE `postid`=:postid;';

		$req = $db->prepare($query);
		$req->bindParam(':postid', $postid);
		$req->execute();
		$sum = $req->fetch(PDO::FETCH_NUM)[0];

		echo '<!-- sum: ' . $sum . ', id: ' . $postid . ' -->';

		return intval($sum);
	}

	function canVote($postid, $userid, $readydb = NULL) {
		$postid = intval($postid);

		if (!isPostExists($postid, $readydb)) {
			return false;
		}

		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'SELECT COUNT(*) FROM `likes` WHERE `userid` = :userid AND `postid` = :postid;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->bindParam(':postid', $postid);
		$req->execute();
		$count = $req->fetchColumn();

		return $count == 0;
	}

	function vote($postid, $userid, $readydb = NULL) {
		$postid = intval($postid);

		if (!isPostExists($postid, $readydb)) {
			return false;
		}

		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'INSERT INTO `likes` (`userid`, `postid`, `value`) VALUES (:userid, :postid, 1);';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->bindParam(':postid', $postid);
		$req->execute();
	}
?>
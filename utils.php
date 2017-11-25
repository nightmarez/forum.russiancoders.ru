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

	function get_ip()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
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

	function isUserIdExists($userId, $readydb = NULL) {
		if (!validateUserId($userId)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT * FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userId);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function isUidExists($uid, $readydb = NULL) {
		if (preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $uid)) {
			return true;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT * FROM `uids` WHERE `uid`=:uid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':uid', $uid);
		$req->execute();
		$count = $req->fetchColumn();
		
		if ($count >= 1) {
			return true;
		}

		$query =
			'INSERT INTO `uids` (`uid`) VALUES (:uid);';

		$req = $db->prepare($query);
		$req->bindParam(':uid', $uid);
		$req->execute();

		return false;
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
		} while (isUserIdExists($userId) /* || isUidExists($userId) */);

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

	function sendPrivateMessage($fromid, $toid, $content, $readydb = NULL) {
		if (!isUserIdExists($fromid)) {
			return false;
		}

		if (!isUserIdExists($toid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 
			'INSERT INTO `messages` 
				(`fromid`, `toid`, `text`, `last`, `ip`, `viewed`) 
			VALUES 
				(:fromid, :toid, :content, NOW(), :ip, 0);';

		$ip = get_ip();

		$req = $db->prepare($query);
		$req->bindParam(':fromid', $fromid, PDO::PARAM_STR);
		$req->bindParam(':toid', $toid, PDO::PARAM_STR);
		$req->bindParam(':content', $content, PDO::PARAM_STR);
		$req->bindParam(':ip', $ip, PDO::PARAM_STR);
		$req->execute();
	}

	function checkPrivateMessagesAsViewed($readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 
			'UPDATE `messages` 
			SET `viewed` = 1
			WHERE `toid`=:userid;';

		$userid = $_COOKIE['userid'];

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->execute();
	}

	function getCountUnviewedMessages($readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'SELECT COUNT(*)
			FROM `messages`
			WHERE `toid` = :userid AND `viewed` = 0;';

		$userid = $_COOKIE['userid'];

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->execute();
		$count = $req->fetchColumn();

		return $count;
	}

	function getGravatarLink($userid, $size, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 'SELECT MD5(LOWER(TRIM(`mail`))) FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

		$r = $readydb->prepare($query);
		$r->bindParam(':userid', $userid);
		$r->execute();

		while (list($mail) = $r->fetch(PDO::FETCH_NUM)) {
			return 'https://secure.gravatar.com/avatar/' . $mail . '.jpg?s=' . $size;
		}
	}

	function addPost($userid, $topicid, $content, $readydb = NULL) {
		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		if (!isTopicExists($topicid, $readydb)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 
			'INSERT INTO `posts` 
				(`topicid`, `userid`, `content`, `ip`) 
			VALUES 
				(:topicid, :userid, :content, :ip);';

		$ip = get_ip();

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->bindParam(':content', $content, PDO::PARAM_STR);
		$req->bindParam(':ip', $ip, PDO::PARAM_STR);
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

	function createTopic($userid, $sectionid, $title, $content, $readydb = NULL) {
		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		if (!isSectionExists($sectionid)) {
			header('Location: /createtopic.php?error=Заданного раздела не существует');
			die();
		}

		$topicid = generateUserId();

		$db = is_null($readydb) ? new PdoDb() : $readydb;

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

	function updateUserOnline($readydb = NULL) {
		if (!isset($_COOKIE['session'])) {
			return false;
		}

		$session = $_COOKIE['session'];

		if (!isset($_COOKIE['userid'])) {
			return false;
		}

		$userid = $_COOKIE['userid'];

		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		if ($userid == 'jYzACIND80rGj0XngB3N') {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'UPDATE `users` SET `last`=now() WHERE `session`=:session AND `userid`=:userid;';

		$req = $db->prepare($query);
		$req->bindParam(':session', $session);
		$req->bindParam(':userid', $userid);
		$req->execute();

		$ip = get_ip();
		$query = 'SELECT `id` FROM `ips` WHERE `userid`=:userid AND `ip`=:ip LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->bindParam(':ip', $ip);
		$req->execute();

		while (list($id) = $req->fetch(PDO::FETCH_NUM)) {
			return true;
		}

		$query = 'INSERT INTO `ips` (`userid`, `ip`) VALUES (:userid, :ip);';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->bindParam(':ip', $ip);
		$req->execute();

		return true;
	}

	function getUserLoginById($userid, $readydb = NULL) {
		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `login` FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->execute();

		while (list($login) = $req->fetch(PDO::FETCH_NUM)) {
			$login = htmlspecialchars($login);

			$login = preg_replace('#(aik)#iUs', 'Антон Литвинов', $login);
			$login = preg_replace('#(seoratings)#iUs', 'Антон Литвинов', $login);
			$login = preg_replace('#(prematuremakarov)#iUs', 'Антон Литвинов', $login);

			return $login;
		}

		return false;
	}

	function getSectionIdByTopicId($topicid, $readydb = NULL) {
		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $topicid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `sectionid` FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->execute();

		while (list($sectionid) = $req->fetch(PDO::FETCH_NUM)) {
			return htmlspecialchars($sectionid);
		}

		return false;
	}

	function getSectionTitleById($sectionid, $readydb = NULL) {
		$sectionid = htmlspecialchars($sectionid);
		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `title` FROM `sections` WHERE `sectionid`=:sectionid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':sectionid', $sectionid, PDO::PARAM_STR);
		$req->execute();

		while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
			return htmlspecialchars($title);
		}

		return false;
	}

	function getTopicTitleById($topicid, $readydb = NULL) {
		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $topicid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `title` FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->execute();

		while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
			return htmlspecialchars($title);
		}

		return false;
	}

	function getPostNumber($topicid, $id, $readydb = NULL) {
		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $topicid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 'SET @cnt := 0;';
		$req = $db->prepare($query);
		$req->execute();

		$query =
			'SELECT t.`cnt` FROM
			(
    			SELECT `id`, (@cnt := @cnt + 1) as `cnt` FROM `posts` WHERE `topicid`="' . $topicid . '"
			) as t
			WHERE t.`id` = ' . $id . ';';

		$req = $db->prepare($query);
		$req->execute();
		$result = $req->fetchColumn();

		return $result - 1;
	}

	function getUserLastVisit($userid, $readydb = NULL) {
		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `last` FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->execute();
		$result = $req->fetchColumn();

		return $result;
	}

	function getUserFirstVisit($userid, $readydb = NULL) {
		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `first` FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->execute();
		$result = $req->fetchColumn();

		return $result;
	}

	function getPostPageNumber($topicid, $id, $readydb = NULL) {
		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $topicid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$number = ceil((getPostNumber($topicid, $id, $readydb) + 1) / postsPerPage());
		return $number > 0 ? $number : 1;
	}

	function getTopicInitMessage($topicid, $readydb = NULL) {
		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $topicid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 
			'SELECT `content` 
			FROM `posts` 
			WHERE `topicid`=:topicid 
			ORDER BY `id` 
			LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->execute();
		$result = $req->fetchColumn();

		return $result;
	}

	function getFriendsById($userid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!isUserIdExists($userid, $db)) {
			return false;
		}

		$query =
			'SELECT `userid2`
			FROM `friendship`
			WHERE `userid1`=:userid
			ORDER BY `userid2`;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->execute();

		$friends = array();

		while (list($id) = $req->fetch(PDO::FETCH_NUM)) {
			$friends[] = $id;
		}

		return $friends;
	}

	function getFansById($userid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!isUserIdExists($userid, $db)) {
			return false;
		}

		$query =
			'SELECT `userid1`
			FROM `friendship`
			WHERE `userid2`=:userid
			ORDER BY `userid1`;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->execute();

		$friends = array();

		while (list($id) = $req->fetch(PDO::FETCH_NUM)) {
			$friends[] = $id;
		}

		$fans = array();

		foreach ($friends as $key => $friend) {
			if (!isFriend($userid, $friend, $readydb)) {
				$fans[] = $friend;
			}
		}

		return $fans;
	}

	function isFriend($userid, $friendid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!isUserIdExists($userid, $db)) {
			return false;
		}

		if (!isUserIdExists($friendid, $db)) {
			return false;
		}

		$query =
			'SELECT `id`
			FROM `friendship`
			WHERE `userid1`=:userid1 AND `userid2`=:userid2
			LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':userid1', $userid, PDO::PARAM_STR);
		$req->bindParam(':userid2', $friendid, PDO::PARAM_STR);
		$req->execute();

		while (list($id) = $req->fetch(PDO::FETCH_NUM)) {
			return true;
		}

		return false;
	}

	function addFriend($userid, $friendid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!isUserIdExists($userid, $db)) {
			return false;
		}

		if (!isUserIdExists($friendid, $db)) {
			return false;
		}

		if (isFriend($userid, $friendid, $db)) {
			return false;
		}

		$query =
			'INSERT INTO `friendship` (`userid1`, `userid2`)
			VALUES (:userid1, :userid2);';

		$req = $db->prepare($query);
		$req->bindParam(':userid1', $userid, PDO::PARAM_STR);
		$req->bindParam(':userid2', $friendid, PDO::PARAM_STR);
		$req->execute();

		return true;
	}

	function removeFriend($userid, $friendid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!isUserIdExists($userid, $db)) {
			return false;
		}

		if (!isUserIdExists($friendid, $db)) {
			return false;
		}

		if (!isFriend($userid, $friendid, $db)) {
			return false;
		}

		$query =
			'DELETE FROM `friendship`
			WHERE `userid1`=:userid1 AND `userid2`=:userid2;';

		$req = $db->prepare($query);
		$req->bindParam(':userid1', $userid, PDO::PARAM_STR);
		$req->bindParam(':userid2', $friendid, PDO::PARAM_STR);
		$req->execute();

		return true;
	}

	function isLogin($readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

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

		$query =
			'SELECT * FROM `users` 
			WHERE `session`=:session AND `userid`=:userid
			LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':session', $session);
		$req->bindParam(':userid', $userid);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function isAdmin($readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

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

		$query =
			'SELECT * FROM `users` 
			WHERE `session`=:session AND `userid`=:userid AND `state`=2 
			LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':session', $session);
		$req->bindParam(':userid', $userid);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function filterMessage($text, $userid) {
		$text = htmlspecialchars($text);

		$text = preg_replace('#\[pre\](.*)\[\/pre\]#iUs', '<pre>${1}</pre>', $text);
		$text = preg_replace('#\[code\](.*)\[\/code\]#iUs', '<pre><code>${1}</code></pre>', $text);
		$text = preg_replace('#\[code=cs\](.*)\[\/code\]#iUs', '<pre><code class="cs">${1}</code></pre>', $text);
		$text = preg_replace('#\[code=csharp\](.*)\[\/code\]#iUs', '<pre><code class="csharp">${1}</code></pre>', $text);
		$text = preg_replace('#\[code=cpp\](.*)\[\/code\]#iUs', '<pre><code class="cpp">${1}</code></pre>', $text);
		$text = preg_replace('#\[code=c\](.*)\[\/code\]#iUs', '<pre><code class="c">${1}</code></pre>', $text);
		$text = preg_replace('#\[code=c++\](.*)\[\/code\]#iUs', '<pre><code class="cpp">${1}</code></pre>', $text);
		$text = preg_replace('#\[code=css\](.*)\[\/code\]#iUs', '<pre><code class="css">${1}</code></pre>', $text);
		$text = preg_replace('#\[code=javascript\](.*)\[\/code\]#iUs', '<pre><code class="javascript">${1}</code></pre>', $text);
		$text = preg_replace('#\[code=js\](.*)\[\/code\]#iUs', '<pre><code class="js">${1}</code></pre>', $text);
		$text = preg_replace('#\[code=php\](.*)\[\/code\]#iUs', '<pre><code class="php">${1}</code></pre>', $text);
		$text = preg_replace('#\[code=sql\](.*)\[\/code\]#iUs', '<pre><code class="sql">${1}</code></pre>', $text);
		$text = preg_replace('#\[code=html\](.*)\[\/code\]#iUs', '<pre><code class="html">${1}</code></pre>', $text);

		$preformatted = array();
		preg_match_all('#<pre>(.*)</pre>#iUs', $text, $preformatted);

		foreach ($preformatted[1] as $key => $value) {
			$text = str_replace($value, '###$$$###' . $key . '###$$$###', $text);
		}

		$text = preg_replace('#\[url=(\S*)\](.*)\[\/url\]#iUs', '<a href="${1}" rel="nofollow" target="_blank">${2}</a>', $text);
		$text = preg_replace('#\[url=\"(\S*)\"\](.*)\[\/url\]#iUs', '<a href="${1}" rel="nofollow" target="_blank">${2}</a>', $text);
		$text = preg_replace('#\[url=(\S*)\]#iUs', '<a href="${1}" rel="nofollow" target="_blank">${1}</a>', $text);
		$text = preg_replace('#\[url=\"(\S*)\"\]#iUs', '<a href="${1}" rel="nofollow" target="_blank">${1}</a>', $text);

		$text = preg_replace('#\[youtube=\"([0-9a-zA-Z]*)\"\]#iUs', '<iframe width="640" height="420" src="https://www.youtube.com/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);
		$text = preg_replace('#\[rutube=\"([0-9a-zA-Z]*)\"\]#iUs', '<iframe width="640" height="420" src="https://rutube.ru/play/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);
		$text = preg_replace('#\[youtube=([0-9a-zA-Z]*)\]#iUs', '<iframe width="640" height="420" src="https://www.youtube.com/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);
		$text = preg_replace('#\[rutube=([0-9a-zA-Z]*)\]#iUs', '<iframe width="640" height="420" src="https://rutube.ru/play/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);

		$text = preg_replace("#(\r\n){2,}#iUs", "<br><br>", $text);
		$text = preg_replace("#(\r\n)#iUs", "<br>", $text);

		$text = preg_replace("#(\r){2,}#iUs", "<br><br>", $text);
		$text = preg_replace("#(\r)#iUs", "<br>", $text);  		

		$text = preg_replace('#\[b\](.*)\[\/b\]#iUs', '<b>${1}</b>', $text);
		$text = preg_replace('#\[i\](.*)\[\/i\]#iUs', '<i>${1}</i>', $text);
		$text = preg_replace('#\[s\](.*)\[\/s\]#iUs', '<s>${1}</s>', $text);
		$text = preg_replace('#\[u\](.*)\[\/u\]#iUs', '<u>${1}</u>', $text);
		$text = preg_replace('#\[p\](.*)\[\/p\]#iUs', '<p>${1}</p>', $text);

		$text = preg_replace('#\[h1\](.*)\[\/h1\]#iUs', '<h1>${1}</h1>', $text);
		$text = preg_replace('#\[h2\](.*)\[\/h2\]#iUs', '<h2>${1}</h2>', $text);
		$text = preg_replace('#\[h3\](.*)\[\/h3\]#iUs', '<h3>${1}</h3>', $text);
		$text = preg_replace('#\[h4\](.*)\[\/h4\]#iUs', '<h4>${1}</h4>', $text);

		$text = preg_replace('#\[strong\](.*)\[\/strong\]#iUs', '<strong>${1}</strong>', $text);
		$text = preg_replace('#\[small\](.*)\[\/small\]#iUs', '<small>${1}</small>', $text);
		$text = preg_replace('#\[sup\](.*)\[\/sup\]#iUs', '<sup>${1}</sup>', $text);
		$text = preg_replace('#\[sub\](.*)\[\/sub\]#iUs', '<sub>${1}</sub>', $text);
		$text = preg_replace('#\[quote\](.*)\[\/quote\]#iUs', '<span style="color: gray;">${1}</span>', $text);

		$text = preg_replace('#(\[br\]){2,}#iUs', '<br><br>', $text);
		$text = preg_replace('#\[br\]#iUs', '<br>', $text);
		$text = preg_replace('#\[hr\]#iUs', '<hr>', $text);

		$text = preg_replace('#(\s*)---(\s*)#iUs', '${1}—${2}', $text);
		$text = preg_replace('#(\s*)--(\s*)#iUs', '${1}–${2}', $text);

		$text = preg_replace('#<br>&gt;(.*)<br>#iUs', '<br><span style="color: gray;">&gt;${1}</span><br>', $text);
		$text = preg_replace('#^&gt;(.*)<br>#iUs', '<span style="color: gray;">&gt;${1}</span><br>', $text);

		$text = preg_replace('#(Михаил Макаров)#iUs', 'NightmareZ', $text);
		$text = preg_replace('#(Михаил\s*Макаров)#iUs', 'NightmareZ', $text);
		$text = preg_replace('#(Макаров Михаил)#iUs', 'NightmareZ', $text);
		$text = preg_replace('#(Макаров\s*Михаил)#iUs', 'NightmareZ', $text);

		$text = preg_replace('#(Михaилa Мaкaрoвa)#iUs', 'NightmareZ\'а', $text);
		$text = preg_replace('#(Михаила\s*Макарова)#iUs', 'NightmareZ\'а', $text);
		$text = preg_replace('#(Макарова Михаила)#iUs', 'NightmareZ\'а', $text);
		$text = preg_replace('#(Макарова\s*Михаила)#iUs', 'NightmareZ\'а', $text);

		$text = preg_replace('#(aik)#iUs', 'Антон Литвинов', $text);
		$text = preg_replace('#(seoratings)#iUs', 'Антон Литвинов', $text);
		$text = preg_replace('#(prematuremakarov)#iUs', 'Антон Литвинов', $text);

		$text = preg_replace('#\:\)\)\)\)#iUs', '<img src="https://gdpanel.nightmarez.net/laugh.gif" alt="смех">', $text);
		$text = preg_replace('#\:\)\)\)#iUs', '<img src="https://gdpanel.nightmarez.net/laugh.gif" alt="смех">', $text);
		$text = preg_replace('#\:\)\)#iUs', '<img src="https://gdpanel.nightmarez.net/laugh.gif" alt="смех">', $text);
		$text = preg_replace('#\:\)#iUs', '<img src="https://gdpanel.nightmarez.net/smile.gif" alt="улыбка">', $text);
		$text = preg_replace('#\:-\)#iUs', '<img src="https://gdpanel.nightmarez.net/smile.gif" alt="улыбка">', $text);
		$text = preg_replace('#\:D#iUs', '<img src="https://gdpanel.nightmarez.net/laugh.gif" alt="смех">', $text);
		$text = preg_replace('#\:-D#iUs', '<img src="https://gdpanel.nightmarez.net/laugh.gif" alt="смех">', $text);
		$text = preg_replace('#\;-\)#iUs', '<img src="https://gdpanel.nightmarez.net/wink.gif" alt="подмигивание">', $text);
		$text = preg_replace('#\;\)#iUs', '<img src="https://gdpanel.nightmarez.net/wink.gif" alt="подмигивание">', $text);
		$text = preg_replace('#\:-P#iUs', '<img src="https://gdpanel.nightmarez.net/tongue.gif" alt="язык">', $text);
		$text = preg_replace('#\:-\(#iUs', '<img src="https://gdpanel.nightmarez.net/sorrow.gif" alt="грусть">', $text);
		$text = preg_replace('#\:\(#iUs', '<img src="https://gdpanel.nightmarez.net/sorrow.gif" alt="грусть">', $text);
		$text = preg_replace("#\:\'-\(#iUs", '<img src="https://gdpanel.nightmarez.net/cry.gif" alt="слёзы">', $text);
		$text = preg_replace("#\:\'\(#iUs", '<img src="https://gdpanel.nightmarez.net/cry.gif" alt="слёзы">', $text);
		$text = preg_replace('#O_O#iUs', '<img src="https://gdpanel.nightmarez.net/amazement.gif" alt="удивление">', $text);
		$text = preg_replace('#O_o#iUs', '<img src="https://gdpanel.nightmarez.net/crazy.gif" alt="сумасшествие">', $text);
		$text = preg_replace('#o_O#iUs', '<img src="https://gdpanel.nightmarez.net/crazy.gif" alt="сумасшествие">', $text);

		$text = preg_replace('#\[rofl\]#iUs', '<img src="https://gdpanel.nightmarez.net/rofl.gif" alt="ржу не могу">', $text);
		$text = preg_replace('#\[good\]#iUs', '<img src="https://gdpanel.nightmarez.net/good.gif" alt="отлично">', $text);
		$text = preg_replace('#\[scratch\]#iUs', '<img src="https://gdpanel.nightmarez.net/scratch.gif" alt="задумался">', $text);
		$text = preg_replace('#\[rtfm\]#iUs', '<img src="https://gdpanel.nightmarez.net/rtfm.gif" alt="читай маны">', $text);
		$text = preg_replace('#\[stop\]#iUs', '<img src="https://gdpanel.nightmarez.net/stop.gif" alt="стоп">', $text);
		$text = preg_replace('#\[genius\]#iUs', '<img src="https://gdpanel.nightmarez.net/umnik.gif" alt="гений">', $text);
		$text = preg_replace('#\[angel\]#iUs', '<img src="https://gdpanel.nightmarez.net/angel.gif" alt="ангел">', $text);
		$text = preg_replace('#\[love\]#iUs', '<img src="https://gdpanel.nightmarez.net/love.gif" alt="любовь">', $text);
		$text = preg_replace('#\[idea\]#iUs', '<img src="https://gdpanel.nightmarez.net/idea.gif" alt="идея">', $text);
		$text = preg_replace('#\[kill\]#iUs', '<img src="https://gdpanel.nightmarez.net/kill.gif" alt="убиться">', $text);
		$text = preg_replace('#\[bad\]#iUs', '<img src="https://gdpanel.nightmarez.net/bad.gif" alt="плохо">', $text);
		$text = preg_replace('#\[smoke\]#iUs', '<img src="https://gdpanel.nightmarez.net/smoke.gif" alt="закурил">', $text);
		$text = preg_replace('#\[angry\]#iUs', '<img src="https://gdpanel.nightmarez.net/angry.gif" alt="злой">', $text);
		$text = preg_replace('#\[devil\]#iUs', '<img src="https://gdpanel.nightmarez.net/devil.gif" alt="дьявол">', $text);
		$text = preg_replace('#\[bomb\]#iUs', '<img src="https://gdpanel.nightmarez.net/bomb.gif" alt="бомба">', $text);
		$text = preg_replace('#\[yahoo\]#iUs', '<img src="https://gdpanel.nightmarez.net/yahoo.gif" alt="ура">', $text);
		$text = preg_replace('#\[dance\]#iUs', '<img src="https://gdpanel.nightmarez.net/dance.gif" alt="танцую">', $text);
		$text = preg_replace('#\[wall\]#iUs', '<img src="https://gdpanel.nightmarez.net/wall.gif" alt="убиться об стену">', $text);
		$text = preg_replace('#\[sex\]#iUs', '<img src="https://gdpanel.nightmarez.net/sex.gif" alt="ёбля">', $text);

		$text = preg_replace('#\[img=([0-9a-zA-Z]{20})\]#iUs', '<img src="https://storage.russiancoders.ru/' . $userid . '/${1}.jpg" alt="изображение">', $text);
		$text = preg_replace('#\[img\]([0-9a-zA-Z]{20})\[\/img\]#iUs', '<img src="https://storage.russiancoders.ru/' . $userid . '/${1}.jpg" alt="изображение">', $text);
		$text = preg_replace('#\[color=\#([0-9a-zA-Z]{6})\](.*)\[\/color\]#iUs', '<span style="color:#${1}">${2}</span>', $text);
		$text = preg_replace('#\[color=([0-9a-zA-Z]{6})\](.*)\[\/color\]#iUs', '<span style="color:#${1}">${2}</span>', $text);

		foreach ($preformatted[1] as $key => $value) {
			$text = str_replace('###$$$###' . $key . '###$$$###', $value, $text);
		}

		return $text;
	}

	function getSelfTopicsWithNewMessage($userid, $readydb = NULL) {
		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `topicid` FROM `topics` WHERE `userid`=:userid;';

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
			$query = 'SELECT `userid` FROM `posts` WHERE `topicid`=:topicid ORDER BY `id` DESC;';

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

				$subject = 'RussianCoders';
				$message = "Line 1\nLine 2\nLine 3";
				//$subject = 'Новые сообщения в ваших темах';
				//$message = 'На форуме <a href="https://forum.russiancoders.ru/">RussianCoders</a>' . "\r\n" . 'появились новые сообщения в Ваших темах:<br><br>' . "\r\n";

				//foreach ($topics as $topic) {
				//	$message = $message . '<a href="/topic/' . $topic['topicid'] . '/">' . htmlspecialchars($topic['title']) . '</a><br>' . "\r\n";
				//}

				$headers = "From: noreply@$SERVER_NAME\r\n" . 
					"Reply-To: noreply@$SERVER_NAME\r\n" . 
					"X-Mailer: PHP/" . phpversion();

				echo 'send mail to ' . htmlspecialchars($mail) . '<br>';
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
		$query = 'SELECT COUNT(*) FROM `posts` WHERE `topicid` = :topicid;';

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
		$query = 'SELECT COUNT(*) FROM `topics` WHERE `sectionid` = :sectionid;';

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
		$query = 'SELECT `topicid` FROM `topics` WHERE `sectionid` = :sectionid;';

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
		$query = 'SELECT COUNT(*) FROM `posts` WHERE `id`=:postid LIMIT 0, 1;';

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
		$query = 'SELECT SUM(`value`) FROM `likes` WHERE `postid`=:postid;';

		$req = $db->prepare($query);
		$req->bindParam(':postid', $postid);
		$req->execute();
		$sum = $req->fetch(PDO::FETCH_NUM)[0];

		return intval($sum);
	}

	function canVote($postid, $userid, $readydb = NULL) {
		$postid = intval($postid);

		if (!isPostExists($postid, $readydb)) {
			return false;
		}

		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		if (!isset($_COOKIE['userid'])) {
			return false;
		}

		$currentuserid = $_COOKIE['userid'];

		if (!isUserIdExists($currentuserid, $readydb)) {
			return false;
		}

		if ($userid == $currentuserid) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'SELECT COUNT(*) FROM `likes` WHERE `userid` = :userid AND `postid` = :postid;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $currentuserid);
		$req->bindParam(':postid', $postid);
		$req->execute();
		$count = $req->fetchColumn();

		if ($count > 0) {
			return false;
		}

		$query =
			'SELECT (now() - `created`) as `online` FROM `posts` WHERE `id` = :postid AND TIME_TO_SEC(TIMEDIFF(NOW(), `created`)) <= 24 * 60 * 60;';

		$req = $db->prepare($query);
		$req->bindParam(':postid', $postid);
		$req->execute();

		while (list($online) = $req->fetch(PDO::FETCH_NUM)) {
			return true;
		}

		return false;
	}

	function vote($postid, $userid, $value, $readydb = NULL) {
		$postid = intval($postid);
		$value = intval($value);

		if (!isPostExists($postid, $readydb)) {
			return false;
		}

		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'INSERT INTO `likes` (`userid`, `postid`, `value`) VALUES (:userid, :postid, :value);';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->bindParam(':postid', $postid);
		$req->bindParam(':value', $value);
		$req->execute();
	}

	function blackList() {
		$arr = array();

		$db = new PdoDb();

		$query =
			'SELECT `ip` FROM `ips` WHERE `userid`=:userid;';

		$badid = 'dcHw3LiNKzm2lFfeCWyz';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $badid, PDO::PARAM_STR);
		$req->execute();

		while (list($ip) = $req->fetch(PDO::FETCH_NUM)) {
			$arr[] = $ip;
		}

		return $arr;
	}

	function call404() {
		header("HTTP/1.0 404 Not Found");
		header("HTTP/1.1 404 Not Found");
		header("Status: 404 Not Found");
		die();
	}

	function testBlackList() {
		if (in_array(get_ip(), blackList())) {
			call404();
		}
	}

	function postsPerPage() {
		return 10;
	}

	function topicPagesCount($topicid, $readydb = NULL) {
		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $topicid)) {
			return 0;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT COUNT(*) FROM `posts` WHERE `topicid`=:topicid;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid);
		$req->execute();
		$count = intval($req->fetch(PDO::FETCH_NUM)[0]);
		$count = ceil($count / postsPerPage());

		return $count > 0 ? $count : 1;
	}
?>
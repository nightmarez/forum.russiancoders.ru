<?php
	require_once('db.php');

	$readydb = !isset($readydb) ? new PdoDb() : $readydb;
	setlocale(LC_ALL, 'ru_RU.UTF-8');

	function startsWith($haystack, $needle)
	{
		 $length = mb_strlen($needle);
		 return (mb_substr($haystack, 0, $length) === $needle);
	}

	function endsWith($haystack, $needle)
	{
		$length = mb_strlen($needle);
		return $length === 0 || (mb_substr($haystack, -$length) === $needle);
	}

	function mb_strrev($string, $encoding = null) {
		if ($encoding === null) {
			$encoding = mb_detect_encoding($string);
		}

		$length   = mb_strlen($string, $encoding);
		$reversed = '';

		while ($length-- > 0) {
			$reversed .= mb_substr($string, $length, 1, $encoding);
		}

		return $reversed;
	}

	function mb_trim($string, $trim_chars = '\s'){
		return preg_replace('/^[' . $trim_chars . ']*(?U)(.*)[' . $trim_chars . ']*$/u', '\\1', $string);
	}

	function showEnvironmentError($message) {
		//echo '<div color="maroon">';
		//echo htmlspecialchars($message);
		//echo '</div>';
	}

	function testEnvironment() {
		if (function_exists('mb_strlen')) {
			showEnvironmentError('PHP mbstring extension not installed. Please, install them. Example: <i>apt-get install php7.1-mbstring</i>');
		}
	}

	testEnvironment();

	function get_ip() {
		$ip = '';

		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	function filterDengerousString($input) {
		return trim(stripslashes(str_replace("`", '',
			str_replace("\n", ' ', str_replace("|", '_',
			str_replace("..", '',
			htmlspecialchars($input, ENT_QUOTES)))))));
	}

	function validateLogin($login) {
		$safelogin = htmlspecialchars($login, ENT_COMPAT, 'UTF-8');

		if ($safelogin != $login) {
			return false;
		}

		if (mb_strlen($login) > 30) {
			return false;
		}

		return true;
	}

	function validateSectionId($sectionid) {
		if (!is_string($sectionid)) {
			return false;
		}

		if (strlen($sectionid) >= 40) {
			return false;
		}

		if (!preg_match('/^\{?[a-z]*\}?$/', $sectionid)) {
			return false;
		}

		return true;
	}

	function validateTopicId($topicid) {
		if (!is_string($topicid)) {
			return false;
		}

		if (strlen($topicid) != 20) {
			return false;
		}

		if (!preg_match('/^\{?[0-9a-zA-Z]{1,30}\}?$/', $topicid)) {
			return false;
		}

		return true;
	}

	function validateUserId($userid) {
	    if (is_numeric($userid))
        {
            $userid = '' .  $userid;
        } else if (!is_string($userid)) {
			return false;
		}

		if (strlen($userid) <= 1) {
			return false;
		}

		if (!preg_match('/^\{?[0-9a-zA-Z]{1,40}\}?$/', $userid)) {
			return false;
		}

		return true;
	}

	function isLoginExists($login, $readydb = NULL) {
		if (!validateLogin($login)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT * FROM `users` WHERE `login`=:login LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':login', $login);
		$req->execute();
		$count = $req->fetchColumn();

		return $count >= 1;
	}

	function isSectionExists($sectionid, $readydb = NULL) {
		if (!validateSectionId($sectionid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT COUNT(*) FROM `sections` WHERE `sectionid`=:sectionid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':sectionid', $sectionid);
		$req->execute();
		$count = $req->fetchColumn();

		return $count >= 1;
	}

	function isTopicExists($topicid, $readydb = NULL) {
		if (!validateTopicId($topicid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT * FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid);
		$req->execute();
		$count = $req->fetchColumn();

		return $count >= 1;
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

	function isUserBanned($userid, $readydb = NULL) {
		if (!validateUserId($userid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT * FROM `users` WHERE `userid`=:userid AND `state`=0 LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function getRewards($userid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!validateUserId($userid, $db)) {
			return false;
		}

		if (!isUserIdExists($userid, $db)) {
			return false;
		}

		$query = 'SELECT `reward` FROM `rewards` WHERE `userid`=:userid;';
		$rewards = array();

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->execute();

		while (list($reward) = $req->fetch(PDO::FETCH_NUM)) {
			$rewards[] = $reward;
		}

		return $rewards;
	}

	function isRewardExists($userid, $reward, $readydb = NULL) {
		if (!preg_match('/^\{?[a-z0-9]*\}?$/', $reward)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!validateUserId($userid, $db)) {
			return false;
		}

		if (!isUserIdExists($userid, $db)) {
			return false;
		}

		$rewards = getRewards($userid, $db);

		if ($rewards === false) {
			return false;
		}

		return in_array($reward, $rewards);
	}

	function addReward($userid, $reward, $readydb = NULL) {
		if (!preg_match('/^\{?[a-z0-9]*\}?$/', $reward)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!validateUserId($userid, $db)) {
			return false;
		}

		if (!isUserIdExists($userid, $db)) {
			return false;
		}

		if (isRewardExists($userid, $reward, $db)) {
			return true;
		}

		$query = 'INSERT INTO `rewards` (`userid`, `reward`) VALUES (:userid, :reward);';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->bindParam(':reward', $reward);
		$req->execute();

		return true;
	}

	function tryAddNewbieReward($userid, $readydb = NULL) {
		if (!validateUserId($userid)) {
			return false;
		}

		$reward = 'newbie';
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (isRewardExists($userid, $reward, $db)) {
			return false;
		}

		return addReward($userid, $reward, $db);
	}

	function tryAdd2018Reward($userid, $readydb = NULL) {
		if (!validateUserId($userid)) {
			return false;
		}

		$reward = '2018';
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (isRewardExists($userid, $reward, $db)) {
			return false;
		}

		$date = \Datetime::createFromFormat('d.m.Y', '31.12.2017');

		if ($date >= new \Datetime('-1 day') and $date <= new \Datetime('+1 day')) {
			return addReward($userid, '2018', $db);
		}

		return false;
	}

	function tryLogin($login, $pass, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!isLoginExists($login, $readydb)) {
			return false;
		}

		$query = 'SELECT `userid`, `pass`, `salt`, `session`, `mail` FROM `users` WHERE `login`=:login LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':login', $login);
		$req->execute();

		while (list($userid, $pass2, $salt, $session, $mail) = $req->fetch(PDO::FETCH_NUM)) {
			if ($mail == 'vkregistered@russiancoders.tech') {
				if ($pass !== $pass2) {
					return false;
				}

				setUserCookies($userid, $session);
				tryAddNewbieReward($userid, $db);
				return true;
				break;
			} else {
				$pass = saltPass($pass, $salt);

				if ($pass !== $pass2) {
					return false;
				}

				setUserCookies($userid, $session);
				tryAddNewbieReward($userid, $db);
				return true;
				break;
			}
		}

		return false;
	}

	function isUidExists($uid, $readydb = NULL) {
		if (preg_match('/^\{?[0-9a-zA-Z]{1,30}\}?$/', $uid)) {
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
		$symbols = 'abcdefghkmnopqrstuvwxyzABCDEFGHKLMNPRSTUVWXYZ123456789';
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

	function addUser($login, $pass, $mail, $die = TRUE, $uid = FALSE) {
		if (!validateLogin($login)) {
			die('Недопустимый логин');
		}

		if (strlen($pass) < 4) {
			die('Слишком короткий пароль');
		}

		if (isLoginExists($login)) {
			die('Заданный логин уже занят');
		}

		if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
			die('Указан некорректный почтовый ящик');
		}

		$userId = generateUserId();

		if ($uid) {
		    $userId = $uid;
        }

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
'Для активации аккаунта перейдите по ссылке <a href="https://russiancoders.tech/activate.php?id=' . $session . '">' . $session . '</a>';
			$headers = 'From: noreply@russiancoders.tech' . "\r\n" .
'Reply-To: webmaster@example.com' . "\r\n" .
'X-Mailer: PHP/' . phpversion();

			// mail($mail, $subject, $message, $headers);

			/*header('Location: /registercomplete.php');

			if ($die) {
				die();
			} else {
				return $userId;
			}*/

            tryAddNewbieReward($userId, $db);
		}

		//header('Location: /register.php?error=Неизвестная ошибка при регистрации пользователя');
		//tryAddNewbieReward($userId, $db);

		/*if ($die) {
			die();
		} else {
			return $userId;
		}*/
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

	function getUserIdByPost($postid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `userid` FROM `posts` WHERE `id`=:postid LIMIT 0, 1;';

		$r = $readydb->prepare($query);
		$r->bindParam(':postid', $postid);
		$r->execute();

		while (list($userid) = $r->fetch(PDO::FETCH_NUM)) {
			return $userid;
		}

		return false;
	}

	function getPostsCountByUserId($userid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT COUNT(*) FROM `posts` WHERE `userid`=:userid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->execute();

		$count = $req->fetchColumn();
		return $count;
	}

	function getTopicsCountByUserId($userid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT COUNT(*) FROM `topics` WHERE `userid`=:userid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->execute();

		$count = $req->fetchColumn();
		return $count;
	}

	function getUserMessagesCount($userid, $readydb = NULL) {
		if (!validateUserId($userid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'SELECT COUNT(*) 
			FROM `posts` 
			WHERE `userid`=:userid 
			LIMIT 0, 1;';

		$r = $readydb->prepare($query);
		$r->bindParam(':userid', $userid);
		$r->execute();
		$count = $r->fetchColumn();

		return $count;
	}

	function tryAddCitizenReward($userid, $readydb = NULL) {
		if (!validateUserId($userid)) {
			return false;
		}

		$reward = 'citizen';
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (isRewardExists($userid, $reward, $db)) {
			return false;
		}

		if (getUserMessagesCount($userid, $db) >= 100) {
			return addReward($userid, $reward, $db);
		}

		return false;
	}

	function tryAddResidentReward($userid, $readydb = NULL) {
		if (!validateUserId($userid)) {
			return false;
		}

		$reward = 'resident';
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (isRewardExists($userid, $reward, $db)) {
			return false;
		}

		if (getUserMessagesCount($userid, $db) >= 300) {
			return addReward($userid, $reward, $db);
		}

		return false;
	}

	function tryAddVeteranReward($userid, $readydb = NULL) {
		if (!validateUserId($userid)) {
			return false;
		}

		$reward = 'veteran';
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (isRewardExists($userid, $reward, $db)) {
			return false;
		}

		if (getUserMessagesCount($userid, $db) >= 1000) {
			return addReward($userid, $reward, $db);
		}

		return false;
	}

	function scanServerDirectory($base) {
		$files = array();
		$cdir = scandir($base);

		foreach ($cdir as $key => $value) { 
			if (!in_array($value, array('.', '..', '.git', 'gallery', 'icons', 'rewards', 'static', 'avatars'))) { 
				if (is_dir($base . DIRECTORY_SEPARATOR . $value)) { 
					$files = array_merge($files, scanDirectory($base . DIRECTORY_SEPARATOR . $value)); 
				} else {
					$path = $base . DIRECTORY_SEPARATOR . $value;
					$files[filemtime($path)] = $path;
				} 
			} 
		}

		ksort($files);
		return array_reverse($files);
	}

	function getUserIdByThumbnail($thumbnail) {
		$base = '/var/www/russiancoders.club';
		$files = scanServerDirectory($base);

		foreach ($files as $file) {
			if (basename($file) == $thumbnail) {
				return basename(dirname($file));
			}
		}

		return false;
	}

	function getLoadedImages($userid) {
		if (!validateUserId($userid)) {
			return false;
		}

		return scanServerDirectory('/var/www/russiancoders.club/' . $userid . '/');
	}

	function getLoadedImagesCount($userid) {
		$files = getLoadedImages($userid);
		return count($files);
	}

	function tryAddPhotographerReward($userid, $readydb = NULL) {
		if (!validateUserId($userid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$reward = 'photographer';

		if (isRewardExists($userid, $reward, $db)) {
			return false;
		}

		if (getLoadedImagesCount($userid) >= 100) {
			return addReward($userid, $reward, $db);
		}

		return false;
	}

	function getRewardInfo($reward, $readydb = NULL) {
		if (!preg_match('/^\{?[a-z0-9]*\}?$/', $reward)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `title` FROM `rewardinfo` WHERE `reward`=:reward;';

		$req = $db->prepare($query);
		$req->bindParam(':reward', $reward, PDO::PARAM_STR);
		$req->execute();

		while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
			return $title;
		}
	}

	function editPost($userid, $topicid, $postid, $content) {
		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		if (!isTopicExists($topicid, $readydb)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 'UPDATE `posts` SET `content`=:content WHERE `topicid`=:topicid AND `userid`=:userid AND `id`=:postid;';

		$req = $db->prepare($query);
		$req->bindParam(':content', $content, PDO::PARAM_STR);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->bindParam(':postid', $postid, PDO::PARAM_INT);
		$req->execute();
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

		$hot = isHotTopic($topicid, $db) ? 1 : 0;

		$query = 
			'UPDATE 
				`topics` 
			SET 
				`updated` = NOW(),
				`lastpost`= :postnumber,
				`hot` = :hot
			WHERE 
				`topicid` = :topicid;';

		$postnumber = getPostNumber($topicid, getLastPostIdInTopic($topicid, $db), $db);

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->bindParam(':postnumber', $postnumber, PDO::PARAM_INT);
		$req->bindParam(':hot', $hot, PDO::PARAM_INT);
		$req->execute();

		tryAddCitizenReward($userid, $readydb);
		tryAddResidentReward($userid, $readydb);
		tryAddVeteranReward($userid, $readydb);
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
		setcookie('userid', $userid, time() + 3600 * 500);
		setcookie('session', $session, time() + 3600 * 500);
	}

	function unsetUserCookies() {
		setcookie('userid', '', time() - 3600);
		setcookie('session', '', time() - 3600);
	}

	function logout() {
		unsetUserCookies();
		header('Location: /unset.php');
	}

	function fullLogout($readydb = NULL) {
		$session = $_COOKIE['session'];
		unsetUserCookies();

		if (!preg_match('/^\{?[0-9a-zA-Z]{40}\}?$/', $session)) {
			die();
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 'UPDATE `users` 
				  SET `session`=:newsession 
				  WHERE `session`=:session;';

		$newsession = generateSession();

		$req = $db->prepare($query);
		$req->bindParam(':session', $session, PDO::PARAM_STR);
		$req->bindParam(':newsession', $newsession, PDO::PARAM_STR);
		$req->execute();
		header('Location: /unset.php');
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

			$login = preg_replace('#(Huldra)#iUs', 'Повелительница', $login);

			return $login;
		}

		return false;
	}

	function getUserFullNameById($userid, $readydb = NULL) {
		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `fullname` FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->execute();

		while (list($fullname) = $req->fetch(PDO::FETCH_NUM)) {
			if (!is_null($fullname) && !empty($fullname) && mb_trim(mb_strlen($fullname)) > 0) {
				return htmlspecialchars(mb_trim($fullname));
			}

			return false;
		}

		return false;
	}

	function getUserTitleById($userid, $readydb = NULL) {
		if (!isUserIdExists($userid, $readydb)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$fullname = getUserFullNameById($userid, $readydb);

		if (!$fullname) {
			$fullname = getUserLoginById($userid, $readydb);
		}

		return $fullname;
	}

	function getSectionId($readydb = NULL) {
		if (empty($_GET['sectionid'])) {
			return false;
		}

		$sectionid = htmlspecialchars($_GET['sectionid']);

		if (!validateSectionId($sectionid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!isSectionExists($sectionid, $db)) {
			return false;
		}

		return $sectionid;
	}

	function getTopicId($readydb = NULL) {
		if (empty($_GET['topicid'])) {
			return false;
		}

		$topicid = htmlspecialchars($_GET['topicid']);

		// TODO: 

		return $topicid;
	}

	function getTopicIdByPostId($postid, $readydb = NULL) {
		$postid = intval($postid);
		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `topicid` FROM `posts` WHERE `id`=:postid LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':postid', $postid, PDO::PARAM_STR);
		$req->execute();

		while (list($topicid) = $req->fetch(PDO::FETCH_NUM)) {
			return htmlspecialchars($topicid);
		}

		return false;
	}

	function getSectionIdByTopicId($topicid, $readydb = NULL) {
		if (!validateTopicId($topicid)) {
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
		if (!validateTopicId($topicid)) {
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
		if (!validateTopicId($topicid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 'SET @cnt := 0;';
		$req = $db->prepare($query);
		$req->execute();

		$query =
			'SELECT t.`cnt` FROM
			(
				SELECT `id`, (@cnt := @cnt + 1) AS `cnt` FROM `posts` WHERE `topicid`="' . $topicid . '" ORDER BY `id`
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
		if (!validateTopicId($topicid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$number = ceil((getPostNumber($topicid, $id, $readydb) + 1) / postsPerPage());
		return $number > 0 ? $number : 1;
	}

	function getLastPostIdInTopic($topicid, $readydb = NULL) {
		if (!validateTopicId($topicid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 
			'SELECT `id` 
			FROM `posts` 
			WHERE `topicid`=:topicid 
			ORDER BY `id` DESC
			LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->execute();
		$result = $req->fetchColumn();

		return intval($result);
	}

	function getPostDate($postid, $readydb = NULL) {
		$postid = intval($postid);
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 
			'SELECT `created` 
			 FROM `posts` 
			 WHERE `id`=:postid 
			 LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':postid', $postid, PDO::PARAM_INT);
		$req->execute();
		$result = $req->fetchColumn();

		return $result;
	}

	function getTopicInitMessage($topicid, $readydb = NULL) {
		if (!validateTopicId($topicid)) {
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

	function isTopicClosed($topicid, $readydb = NULL) {
		if (!validateTopicId($topicid)) {
			return true;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 
			'SELECT `closed` 
			FROM `topics` 
			WHERE `topicid`=:topicid 
			LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->execute();

		while (list($closed) = $req->fetch(PDO::FETCH_NUM)) {
			return $closed == 1;
		}

		return false;
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

		if (!preg_match('/^\{?[0-9a-zA-Z]{1,30}\}?$/', $userid)) {
			return false;
		}

		$query =
			'SELECT * FROM `users` 
			WHERE `session`=:session AND `userid`=:userid
			LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':session', $session, PDO::PARAM_STR);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
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

		if (!preg_match('/^\{?[0-9a-zA-Z]{1,30}\}?$/', $userid)) {
			return false;
		}

		$query =
			'SELECT * FROM `users` 
			 WHERE `session`=:session AND `userid`=:userid AND `state`=2 
			 LIMIT 0, 1;';

		$req = $db->prepare($query);
		$req->bindParam(':session', $session, PDO::PARAM_STR);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->execute();
		$count = $req->fetchColumn();
		return $count >= 1;
	}

	function setBan($userid, $readydb = NULL) {
		if (!preg_match('/^\{?[0-9a-zA-Z]{1,30}\}?$/', $userid)) {
			return false;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query =
			'UPDATE `users` 
			 SET `pass`=:pass, `salt`=:salt, `session`=:session, `state`=0  
			 WHERE `userid`=:userid;';
		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->bindParam(':pass', generateSymbols(20), PDO::PARAM_STR);
		$req->bindParam(':salt', generateSalt(), PDO::PARAM_STR);
		$req->bindParam(':session', generateSession(), PDO::PARAM_STR);
		$req->execute();

		$query = 
			'INSERT INTO `rewards` (`userid`, `reward`) 
			 VALUES (:userid, "banned");';
		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->execute();
	}

	function filterMessage($text, $userid) {
		$text = htmlspecialchars($text);
		$text = preg_replace('#&amp;quot;#iUs', '&quot;', $text);

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

		$text = preg_replace('/([\s]+)((www|http:\/\/)[^\s]+)/', '${1}<a href="${2}" rel="nofollow" target="_blank">${2}</a>', $text);

		$text = preg_replace('#\[mail=(\S*)\](.*)\[\/mail\]#iUs', '<a href="mailto:${1}">${2}</a>', $text);
		$text = preg_replace('#\[mail=\"(\S*)\"\](.*)\[\/mail\]#iUs', '<a href="mailto:${1}">${2}</a>', $text);
		$text = preg_replace('#\[mail=(\S*)\]#iUs', '<a href="mailto:${1}">${1}</a>', $text);
		$text = preg_replace('#\[mail=\"(\S*)\"\]#iUs', '<a href="mailto:${1}">${1}</a>', $text);

		$text = preg_replace('#\[mail=(mailto:\S*)\](.*)\[\/mail\]#iUs', '<a href="${1}">${2}</a>', $text);
		$text = preg_replace('#\[mail=\"(mailto:\S*)\"\](.*)\[\/mail\]#iUs', '<a href="${1}">${2}</a>', $text);
		$text = preg_replace('#\[mail=(mailto:\S*)\]#iUs', '<a href="${1}">${1}</a>', $text);
		$text = preg_replace('#\[mail=\"(mailto:\S*)\"\]#iUs', '<a href="${1}">${1}</a>', $text);

		$text = preg_replace('#\[youtube=&quot;([0-9a-zA-Z_\-]*)&quot;\]#iUs', '<iframe width="100%" height="605px" src="https://www.youtube.com/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);
		$text = preg_replace('#\[rutube=&quot;([0-9a-zA-Z_\-]*)&quot;\]#iUs', '<iframe width="100%" height="605px" src="https://rutube.ru/play/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);
		$text = preg_replace('#\[youtube=([0-9a-zA-Z_\-]*)\]#iUs', '<iframe width="100%" height="605px" src="https://www.youtube.com/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);
		$text = preg_replace('#\[rutube=([0-9a-zA-Z_\-]*)\]#iUs', '<iframe width="100%" height="605px" src="https://rutube.ru/play/embed/${1}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', $text);

		$text = preg_replace("#(\r\n){2,}#iUs", "<br><br>", $text);
		$text = preg_replace("#(\r\n)#iUs", "<br>", $text);

		$text = preg_replace("#(\r){2,}#iUs", "<br><br>", $text);
		$text = preg_replace("#(\r)#iUs", "<br>", $text);

		$text = preg_replace("#(\n){2,}#iUs", "<br><br>", $text);
		$text = preg_replace("#(\n)#iUs", "<br>", $text);		

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
		$text = preg_replace('#<iframe(.*)(—)(.*)<\/iframe>#iUs', '<iframe${1}---${3}</iframe>', $text);
		$text = preg_replace('#<iframe(.*)(–)(.*)<\/iframe>#iUs', '<iframe${1}--${3}</iframe>', $text);

		$text = preg_replace('#<br>&gt;(.*)<br>#iUs', '<br><span style="color: gray;">&gt;${1}</span><br>', $text);
		$text = preg_replace('#^&gt;(.*)<br>#iUs', '<span style="color: gray;">&gt;${1}</span><br>', $text);

		$text = preg_replace('#(Михаил Макаров)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Михаил\s*Макаров)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Макаров Михаил)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Макаров\s*Михаил)#iUs', generateSalt(), $text);

		$text = preg_replace('#(Михaилa Мaкaрoвa)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Михаила\s*Макарова)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Макарова Михаила)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Макарова\s*Михаила)#iUs', generateSalt(), $text);

		$text = preg_replace('#(Антон Литвинов)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Антон\s*Литвинов)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Литвинов Антон)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Литвинов\s*Антон)#iUs', generateSalt(), $text);

		$text = preg_replace('#(Антонa Литвиновa)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Антонa\s*Литвиновa)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Литвиновa Антонa)#iUs', generateSalt(), $text);
		$text = preg_replace('#(Литвиновa\s*Антонa)#iUs', generateSalt(), $text);

		$text = preg_replace('#\:\)\)\)\)#iUs', '<img src="https://russiancoders.club/static/laugh.gif" alt="смех">', $text);
		$text = preg_replace('#\:\)\)\)#iUs', '<img src="https://russiancoders.club/static/laugh.gif" alt="смех">', $text);
		$text = preg_replace('#\:\)\)#iUs', '<img src="https://russiancoders.club/static/laugh.gif" alt="смех">', $text);
		$text = preg_replace('#\:\)#iUs', '<img src="https://russiancoders.club/static/smile.gif" alt="улыбка">', $text);
		$text = preg_replace('#\:-\)#iUs', '<img src="https://russiancoders.club/static/smile.gif" alt="улыбка">', $text);
		$text = preg_replace('#\:D#iUs', '<img src="https://russiancoders.club/static/laugh.gif" alt="смех">', $text);
		$text = preg_replace('#\:-D#iUs', '<img src="https://russiancoders.club/static/laugh.gif" alt="смех">', $text);
		$text = preg_replace('#\;-\)#iUs', '<img src="https://russiancoders.club/static/wink.gif" alt="подмигивание">', $text);
		$text = preg_replace('#\;\)#iUs', '<img src="https://russiancoders.club/static/wink.gif" alt="подмигивание">', $text);
		$text = preg_replace('#\:-P#iUs', '<img src="https://russiancoders.club/static/tongue.gif" alt="язык">', $text);
		$text = preg_replace('#\:-\(#iUs', '<img src="https://russiancoders.club/static/sorrow.gif" alt="грусть">', $text);
		$text = preg_replace('#\:\(#iUs', '<img src="https://russiancoders.club/static/sorrow.gif" alt="грусть">', $text);
		$text = preg_replace("#\:\'-\(#iUs", '<img src="https://russiancoders.club/static/cry.gif" alt="слёзы">', $text);
		$text = preg_replace("#\:\'\(#iUs", '<img src="https://russiancoders.club/static/cry.gif" alt="слёзы">', $text);
		$text = preg_replace('#O_O#Us', '<img src="https://russiancoders.club/static/amazement.gif" alt="удивление">', $text);
		$text = preg_replace('#O_o#Us', '<img src="https://russiancoders.club/static/crazy.gif" alt="сумасшествие">', $text);
		$text = preg_replace('#o_O#Us', '<img src="https://russiancoders.club/static/crazy.gif" alt="сумасшествие">', $text);

		$text = preg_replace('#\[rofl\]#iUs', '<img src="https://russiancoders.club/static/rofl.gif" alt="ржу не могу">', $text);
		$text = preg_replace('#\[good\]#iUs', '<img src="https://russiancoders.club/static/good.gif" alt="отлично">', $text);
		$text = preg_replace('#\[scratch\]#iUs', '<img src="https://russiancoders.club/static/scratch.gif" alt="задумался">', $text);
		$text = preg_replace('#\[rtfm\]#iUs', '<img src="https://russiancoders.club/static/rtfm.gif" alt="читай маны">', $text);
		$text = preg_replace('#\[stop\]#iUs', '<img src="https://russiancoders.club/static/stop.gif" alt="стоп">', $text);
		$text = preg_replace('#\[genius\]#iUs', '<img src="https://russiancoders.club/static/umnik.gif" alt="гений">', $text);
		$text = preg_replace('#\[angel\]#iUs', '<img src="https://russiancoders.club/static/angel.gif" alt="ангел">', $text);
		$text = preg_replace('#\[love\]#iUs', '<img src="https://russiancoders.club/static/love.gif" alt="любовь">', $text);
		$text = preg_replace('#\[idea\]#iUs', '<img src="https://russiancoders.club/static/idea.gif" alt="идея">', $text);
		$text = preg_replace('#\[kill\]#iUs', '<img src="https://russiancoders.club/static/kill.gif" alt="убиться">', $text);
		$text = preg_replace('#\[bad\]#iUs', '<img src="https://russiancoders.club/static/bad.gif" alt="плохо">', $text);
		$text = preg_replace('#\[smoke\]#iUs', '<img src="https://russiancoders.club/static/smoke.gif" alt="закурил">', $text);
		$text = preg_replace('#\[angry\]#iUs', '<img src="https://russiancoders.club/static/angry.gif" alt="злой">', $text);
		$text = preg_replace('#\[devil\]#iUs', '<img src="https://russiancoders.club/static/devil.gif" alt="дьявол">', $text);
		$text = preg_replace('#\[bomb\]#iUs', '<img src="https://russiancoders.club/static/bomb.gif" alt="бомба">', $text);
		$text = preg_replace('#\[yahoo\]#iUs', '<img src="https://russiancoders.club/static/yahoo.gif" alt="ура">', $text);
		$text = preg_replace('#\[dance\]#iUs', '<img src="https://russiancoders.club/static/dance.gif" alt="танцую">', $text);
		$text = preg_replace('#\[wall\]#iUs', '<img src="https://russiancoders.club/static/wall.gif" alt="убиться об стену">', $text);
		$text = preg_replace('#\[sex\]#iUs', '<img src="https://russiancoders.club/static/sex.gif" alt="секс">', $text);

		$text = preg_replace(
			'/\[img=&quot;([0-9a-zA-Z]{1,30})&quot;\s*alt=&quot;([\w\s]{1,100})&quot;\]/iuUs',
			'<img src="https://russiancoders.club/' . $userid . '/${1}.jpg" alt="${2}">',
			$text);
		$text = preg_replace(
			'/\[img\s*alt=&quot;([\w\s]{1,100})&quot;\]([0-9a-zA-Z]{20})\s*\[\/img\]/iuUs',
			'<img src="https://russiancoders.club/' . $userid . '/${2}.jpg" alt="${1}">',
			$text);
		$text = preg_replace(
			'/\[img=&quot;([0-9a-zA-Z]{1,30})&quot;\]/iUs',
			'<img src="https://russiancoders.club/' . $userid . '/${1}.jpg" alt="изображение">',
			$text);

		$text = preg_replace(
			'/\[img=([0-9a-zA-Z]{20})\s*alt=&quot;([\w\s]{1,100})&quot;\]/iuUs',
			'<img src="https://russiancoders.club/' . $userid . '/${1}.jpg" alt="${2}">',
			$text);
		$text = preg_replace(
			'/\[img=&quot;([0-9a-zA-Z]{20})&quot;\s*alt=([\w\s]{1,100})\]/iuUs',
			'<img src="https://russiancoders.club/' . $userid . '/${1}.jpg" alt="${2}">',
			$text);

		$text = preg_replace(
			'/\[img=([0-9a-zA-Z]{20})\s*alt=([\w\s]{1,100})\]/iuUs',
			'<img src="https://russiancoders.club/' . $userid . '/${1}.jpg" alt="${2}">',
			$text);
		$text = preg_replace(
			'/\[img\s*alt=([\w\s]{1,100})\]([0-9a-zA-Z]{20})\s*\[\/img\]/iuUs',
			'<img src="https://russiancoders.club/' . $userid . '/${2}.jpg" alt="${1}">',
			$text);
		$text = preg_replace(
			'/\[img=([0-9a-zA-Z]{20})\]/iUs',
			'<img src="https://russiancoders.club/' . $userid . '/${1}.jpg" alt="изображение">',
			$text);

		$text = preg_replace(
			'/\[img\]([0-9a-zA-Z]{20})\[\/img\]/iUs',
			'<img src="https://russiancoders.club/' . $userid . '/${1}.jpg" alt="изображение">',
			$text);

		$text = preg_replace(
			'/\[mp3=&quot;([0-9a-zA-Z]{1,20})&quot;\]/iuUs',
			'<audio src="https://russiancoders.club/' . $userid . '/${1}.mp3" controls style="width: 100%;"></audio>',
			$text);
		$text = preg_replace(
			'/\[mp3=([0-9a-zA-Z]{1,20})\]/iUs',
			'<audio src="https://russiancoders.club/' . $userid . '/${1}.mp3" controls style="width: 100%;"></audio>',
			$text);

		$text = preg_replace('#\[color=\#([0-9a-zA-Z]{6})\](.*)\[\/color\]#iUs', '<span style="color:#${1}">${2}</span>', $text);
		$text = preg_replace('#\[color=([0-9a-zA-Z]{6})\](.*)\[\/color\]#iUs', '<span style="color:#${1}">${2}</span>', $text);

		$text = preg_replace('#&quot;(.{1,100})&quot;#iUs', '«${1}»', $text);

		$text = preg_replace('#\.{3,5}#iUs', '…', $text);

		$brlen = mb_strlen('<br>');
		$text = mb_trim($text);
		while (endsWith($text, '<br>')) {
			$text = mb_strrev(mb_substr(mb_strrev($text), $brlen));
			$text = mb_trim($text);
		}

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
				//$message = 'На форуме <a href="https://forum.russiancoders.tech/">RussianCoders</a>' . "\r\n" . 'появились новые сообщения в Ваших темах:<br><br>' . "\r\n";

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

		$query = 'SELECT (now() - `created`) AS `online` FROM `posts` WHERE `id` = :postid AND TIME_TO_SEC(TIMEDIFF(NOW(), `created`)) <= 24 * 60 * 60;';

		$req = $db->prepare($query);
		$req->bindParam(':postid', $postid);
		$req->execute();

		while (list($online) = $req->fetch(PDO::FETCH_NUM)) {
			return true;
		}

		return false;
	}

	function isHotTopic($topicid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!isTopicExists($topicid, $db)) {
			return false;
		}

		$query = 'SELECT COUNT(*) 
				  FROM `posts` 
				  WHERE `topicid` = :topicid AND TIME_TO_SEC(TIMEDIFF(NOW(), `created`)) <= 24 * 60 * 60 * 7;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid);
		$req->execute();

		$count = $req->fetchColumn();

		if ($count < 30) {
			return false;
		}

		return true;
	}

	function isPinnedTopic($topicid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!isTopicExists($topicid, $db)) {
			return false;
		}

		$query = 'SELECT COUNT(*) 
				  FROM `topics` 
				  WHERE `topicid`=:topicid AND `pinned`=1;';

		$req = $db->prepare($query);
		$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
		$req->execute();

		$count = $req->fetchColumn();
		return $count > 0;
	}

	function isUserOnline($userid, $readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;

		if (!isUserIdExists($userid, $db)) {
			return 0;
		}

		$query = 
			'SELECT `state`, (now() - `last`) AS `online` 
			 FROM `users` 
			 WHERE TIME_TO_SEC(TIMEDIFF(NOW(), `last`)) <= 24 * 60 * 60 AND `userid`=:userid;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->execute();

		while (list($state, $online) = $req->fetch(PDO::FETCH_NUM)) {
			if ($state == 2) {
				return 0;
			}

			if (intval($online <= 80 /* seconds */)) {
				return 1;
			}
			
			return -1;
		}

		return -1;
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
		$query = 'INSERT INTO `likes` (`userid`, `postid`, `value`) VALUES (:userid, :postid, :value);';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->bindParam(':postid', $postid);
		$req->bindParam(':value', $value);
		$req->execute();
	}

	function getSettingsParam($userid, $param, $readydb = NULL) {
		if (!validateUserId($userid)) {
			return false;
		}

		$param = htmlspecialchars($param);
		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 'SELECT `value` FROM `settings` WHERE `param`=:param;';

		$req = $db->prepare($query);
		$req->bindParam(':param', $param, PDO::PARAM_STR);
		$req->execute();

		while (list($value) = $req->fetch(PDO::FETCH_NUM)) {
			return htmlspecialchars($value);
		}

		return false;
	}

	function setSettingsParam($userid, $param, $value, $readydb = NULL) {
		if (!validateUserId($userid)) {
			return false;
		}

		$param = htmlspecialchars($param);
		$value = htmlspecialchars($value);
		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = 
			'INSERT INTO `settings` (`userid`, `param`, `value`) 
			VALUES (:userid, :param, :value) 
			ON DUPLICATE KEY UPDATE SET `value` = :value;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid, PDO::PARAM_STR);
		$req->bindParam(':param', $param, PDO::PARAM_STR);
		$req->bindParam(':value', $value, PDO::PARAM_STR);
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

	function inBlackList($readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$ip = $_SERVER['REMOTE_ADDR'];

		if (!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $ip)) {
			return true;
		}

		$ip = explode('.', trim($ip));

		for ($i = 0; $i < 4; ++$i) {
			$ip[$i] = intval($ip[$i]);
		}

		$query = 'SELECT `addr` FROM `blacklist`;';
		$req = $db->prepare($query);
		$req->execute();

		while (list($blackip) = $req->fetch(PDO::FETCH_NUM)) {
			$blackip = trim($blackip);

			if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $blackip)) {
				$blackip = explode('.', $blackip);

				if ($ip[0] == intval($blackip[0]) ||
					$ip[1] == intval($blackip[1]) ||
					$ip[2] == intval($blackip[2]) ||
					$ip[3] == intval($blackip[3]))
				{
					return true;
				}
			} else if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}-\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $blackip)) {
				$blackip = explode('-', $blackip);

				$bmin = explode('.', trim($blackip[0]));
				$bmax = explode('.', trim($blackip[1]));

				for ($i = 0; $i < 4; ++$i) {
					$bmin[$i] = intval($bmin[$i]);
					$bmax[$i] = intval($bmax[$i]);
				}

				while (true) {
					if ($bmin[0] > $bmax[0]) {
						break;
					} else if ($bmin[1] > $bmax[1]) {
						break;
					} else if ($bmin[2] > $bmax[2]) {
						break;
					} else if ($bmin[3] > $bmax[3]) {
						break;
					}

					if ($ip[0] == $bmin[0] ||
						$ip[1] == $bmin[1] ||
						$ip[2] == $bmin[2] ||
						$ip[3] == $bmin[3])
					{
						return true;
					}

					if (++$bmin[3] > 255) {
						$bmin[3] = 0;

						if (++$bmin[2] > 255) {
							$bmin[2] = 0;

							if (++$bmin[1] > 255) {
								$bmin[1] = 0;

								if (++$bmin[0] > 255) {
									break;
								}
							}
						}
					}
				}
			} else if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2}\z/', $blackip)) {

			}
		}

		return false;
	}

	function call404() {
		// header("HTTP/1.0 404 Not Found");
		// header("HTTP/1.1 404 Not Found");
		// header("Status: 404 Not Found");
		// die();
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
		if (!validateTopicId($topicid)) {
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

	function getPageTitle($readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$title = 'RussianCoder\'s Forum';

		// ----------------------------------------------------

		$sectionid = getSectionId($readydb);

		if ($sectionid !== false) {
			$query =
				'SELECT `title` 
				 FROM `sections` 
				 WHERE `sectionid`=:sectionid LIMIT 0, 1;';

			$req = $readydb->prepare($query);
			$req->bindParam(':sectionid', $sectionid);
			$req->execute();

			while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
				$title = htmlspecialchars($title);
				break;
			}
		} else if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/tracker') !== false) {
			$title = 'Трекер';
		} else if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/faq') !== false) {
			$title = 'ЧаВо';
		} else if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/donate') !== false) {
			$title = 'Донат';
		} else if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/gallery') !== false) {
			$title = 'Галерея';
		} else if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/rating') !== false) {
			$title = 'Рейтинг';
		} else if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/posts') !== false) {
			$title = 'Сообщения пользователя';
		} else if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/users') !== false) {
			$title = 'Пользователи ресурса';
		} else {
			// ----------------------------------------------------

			$topicid = false;

			if (isset($_GET['topicid'])) {
				$topicid = htmlspecialchars($_GET['topicid']);

				if (!preg_match('/^\{?[0-9a-zA-Z]{1,20}\}?$/', $topicid)) {
					$topicid = false;
				}
			}

			if ($topicid !== false) {
				$query =
					'SELECT `title` 
					 FROM `topics` 
					 WHERE `topicid`=:topicid 
					 LIMIT 0, 1;';

				$req = $readydb->prepare($query);
				$req->bindParam(':topicid', $topicid);
				$req->execute();

				while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
					$title = htmlspecialchars($title);
					break;
				}
			}
		}

		// ----------------------------------------------------

		return $title;
	}

	function accessDenied() {
		include_once('accessdenied.php');
		die();
	}

	function getAvatar($userid, $big = FALSE) {
		$filename = '/var/www/russiancoders.club/avatars/' . $userid . ($big ? '' : '-small') . '.jpg';

		if (file_exists($filename)) {
			return 'https://russiancoders.club/avatars/' . $userid . ($big ? '' : '-small') . '.jpg';
		}

		return FALSE;
	}

	function getGravatarLink($userid, $size, $readydb = NULL) {
		$avatar = getAvatar($userid);

		if ($avatar !== FALSE) {
			return $avatar;
		}

		$db = is_null($readydb) ? new PdoDb() : $readydb;

		$query = 'SELECT MD5(LOWER(TRIM(`mail`))) FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

		$r = $readydb->prepare($query);
		$r->bindParam(':userid', $userid);
		$r->execute();

		while (list($mail) = $r->fetch(PDO::FETCH_NUM)) {
			return 'https://secure.gravatar.com/avatar/' . $mail . '.jpg?s=' . $size;
		}
	}

	function usersPerPage() {
		return 10;
	}

	function topicsPerSection() {
		return 15;
	}

	function topicsPerPage() {
		return 10;
	}

	function totalUsersCount($readydb = NULL) {
		$db = is_null($readydb) ? new PdoDb() : $readydb;
		$query = isAdmin($db) ? 'SELECT COUNT(*) FROM `users`;' : 'SELECT COUNT(*) FROM `users` WHERE `state` > 0;';
		$req = $db->prepare($query);
		$req->execute();
		$count = intval($req->fetch(PDO::FETCH_NUM)[0]);
		return $count > 0 ? $count : 1;
	}

	function usersPagesCount($usersCount) {
		$count = ceil($usersCount / usersPerPage());
		return $count;
	}

	function sectionPagesCount($topicsCount) {
		$count = ceil($topicsCount / topicsPerSection());
		return $count;
	}

	function postsPagesCount($postsCount) {
		$count = ceil($postsCount / postsPerPage());
		return $count;
	}

	function topicsPagesCount($topicsCount) {
		$count = ceil($topicsCount / topicsPerPage());
		return $count;
	}

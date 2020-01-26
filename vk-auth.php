<?php
	require_once('utils.php');

	if (isset($_GET['code']))
	{
		$code = $_GET['code'];
		$token = getVkToken($code);
		$login = 'vk:' . $token['user_id'];
		$pass = $token['access_token'];
		$db = $readydb ? $readydb : new PdoDb();

		$tryLogin = false;
		$db->beginTransaction();
		$userid = false;

		if (!isLoginExists($login)) {
			$userid = addUser($login, $pass, 'vkregistered@russiancoders.tech', FALSE);
		} else {
			$req = $db->prepare('UPDATE `users` SET `pass`=:pass WHERE `login`=:login;');
			$req->bindParam(':login', $login, PDO::PARAM_STR);
			$req->bindParam(':pass', $pass, PDO::PARAM_STR);
			$req->execute();

			$req = $db->prepare('SELECT `userid` FROM `users` WHERE `login`=:login;');
			$req->bindParam(':login', $login, PDO::PARAM_STR);
			$req->execute();

			while (list($uid) = $req->fetch(PDO::FETCH_NUM)) {
				$userid = $uid;
				break;
			}

			$tryLogin = true;
		}

		//////////////////////////////////////////////////////////////////////////////////////////////////////////

		$params = array(
			'uids'         => explode(':', $login)[1],
			'https'        => 1,
			'fields'       => 'photo_big',
			'access_token' => $pass,
			'v'            => 4.0
		);
		$userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);

		if (isset($userid) && $userid) {
			// Check that directory exists
			if (!file_exists('/var/www/russiancoders.club/avatars')) {
				mkdir('/var/www/russiancoders.club/avatars');
				chmod('/var/www/russiancoders.club/avatars', 0777);
			}

			// Download avatar
			$url = $userInfo['response'][0]['photo_big'];
			$exp = explode('.', $url);
			$tmp = '/var/www/html/uploads/' . $userid . '.' . end($exp);
			file_put_contents($tmp, fopen($url, 'r'));

			// Save big avatar
			$dst = '/var/www/russiancoders.club/avatars/' . $userid . '.jpg';

			if (file_exists($dst)) {
				unlink($dst);
			}

			list($width, $height) = getimagesize($tmp);
			$thumb = imagecreatetruecolor(200, 200);
			$source = imagecreatefromjpeg($tmp);
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, 200, 200, $width, $height);
			imagejpeg($thumb, $dst);
			imagedestroy($thumb);
			imagedestroy($source);

			// Save small avatar
			$dst = '/var/www/russiancoders.club/avatars/' . $userid . '-small.jpg';

			if (file_exists($dst)) {
				unlink($dst);
			}

			list($width, $height) = getimagesize($tmp);
			$thumb = imagecreatetruecolor(50, 50);
			$source = imagecreatefromjpeg($tmp);
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, 50, 50, $width, $height);
			imagejpeg($thumb, $dst);
			imagedestroy($thumb);
			imagedestroy($source);

			// Delete downloaded temporary avatar image
			unlink($tmp);
		}

		//////////////////////////////////////////////////////////////////////////////////////////////////////////

		$fullname = htmlspecialchars($userInfo['response'][0]['first_name'] . ' ' . $userInfo['response'][0]['last_name']);

		$req = $db->prepare('UPDATE `users` SET `fullname`=:fullname WHERE `login`=:login;');
		$req->bindParam(':login', $login, PDO::PARAM_STR);
		$req->bindParam(':fullname', $fullname, PDO::PARAM_STR);
		$req->execute();

		$db->commit();

		if ($tryLogin) {
			if (!tryLogin($login, $pass)) {
				header('Location: /login.php?error=Не удалось залогиниться');
			} else {
				header('Location: /tracker/');
			}
		}
	}
	else
	{
		header('Location: ' . genVkAuthLink());
	}
?>
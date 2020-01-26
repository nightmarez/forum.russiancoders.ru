<?php
	require_once('utils.php');

	if (!isLogin($readydb)) {
		die('access denied');
	}

	if (!isAdmin($readydb)) {
		die('access denied');
	}

	$url = 'http://roscenzura.com/roscomsos/gosip.txt';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	curl_close($ch);

	$arr = explode("\n", $result);

	//foreach ($arr as $key => $value) {
	//	$query = 'INSERT INTO `blacklist` (`addr`) VALUES (:addr);';
	//	$req = $readydb->prepare($query);
	//	$req->bindParam(':addr', $value);
	//	$req->execute();
	//}
?>
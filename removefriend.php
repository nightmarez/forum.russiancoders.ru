<?php
	require_once('utils.php');

	if (!isLogin()) {
		header('Location: /');
		die();
	}

	$yourid = htmlspecialchars($_COOKIE['userid']);
	$userid = htmlspecialchars($_GET['userid']);

	if (removeFriend($yourid, $userid, $readydb)) {
		header('Location: /user/' . $userid . '/');
	} else {
		header('Location: /');
		die();
	}
?>

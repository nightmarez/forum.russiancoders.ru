<?php
	require_once('utils.php');

	if (!isLogin($readydb)) {
		header('Location: /');
		die();
	}

	if (!isAdmin($readydb)) {
		header('Location: /');
		die();
	}

	$userid = htmlspecialchars($_GET['userid']);
	setBan($userid, $readydb);
	header('Location: /user/' . $userid . '/');
?>
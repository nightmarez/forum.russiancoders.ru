<?php
	include_once('utils.php');

	$fromid = false;
	$toid = false;
	$content = false;

	if (isLogin() && isset($_COOKIE['userid'])) {
		$fromid = $_COOKIE['userid'];
	}

	if (!preg_match('/^\{?[0-9a-zA-Z]{1,30}\}?$/', $fromid)) {
		$fromid = false;
	}

	if (isset($_POST['toid'])) {
		$toid = $_POST['toid'];
	}

	if (!preg_match('/^\{?[0-9a-zA-Z]{1,30}\}?$/', $toid)) {
		$toid = false;
	}

	if (isset($_POST['content'])) {
		$content = $_POST['content'];
	}

	sendPrivateMessage($fromid, $toid, $content);
	header('Location: /messages/');
?>

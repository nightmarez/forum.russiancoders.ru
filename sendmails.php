<?php
	require_once('utils.php');

	if (!isset($_COOKIE['session'])) {
		echo 'Access denied';
	}

	$session = $_COOKIE['session'];

	if (!isset($_COOKIE['userid'])) {
		echo 'Access denied';
	}

	$userid = $_COOKIE['userid'];

	if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
		echo 'Access denied';
	}

	if ($userid == 'jYzACIND80rGj0XngB3N') {
		echo 'Access denied';
	}

	sendMailsAboutNewMessages();
?>
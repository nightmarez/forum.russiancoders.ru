<?php
	require_once('utils.php');

	if (!isLogin()) {
		echo 'Access Denied';
	}

	if (!isAdmin()) {
		echo 'Access Denied';
	}

	sendMailsAboutNewMessages();
?>
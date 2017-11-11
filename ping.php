<?php
	require_once('utils.php');

	if (isLogin()) {
		updateUserOnline();
		echo json_encode(array('ok' => true));
		die();
	}

	echo array('ok' => false);
?>
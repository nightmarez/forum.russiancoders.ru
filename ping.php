<?php
	require_once('utils.php');

	header('Content-type: application/json');

	if (isLogin()) {
		updateUserOnline();
		echo json_encode(array('ok' => true));
		die();
	}

	echo json_encode(array('ok' => false));
?>
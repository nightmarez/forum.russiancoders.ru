<?php
	require_once('utils.php');

	header('Content-type: application/json');

	if (!isLogin()) {
		echo json_encode(array('ok' => false));
		die();
	}

	if (!isset($_GET['id'])) {
		echo json_encode(array('ok' => false));
		die();
	}

	$id = intval($_GET['id']);

	if (!isset($_COOKIE['userid'])) {
		echo json_encode(array('ok' => false));
		die();
	}

	$userid = $_COOKIE['userid'];

	if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
		echo json_encode(array('ok' => false));
		die();
	}

	if (!isPostExists($id)) {
		echo json_encode(array('ok' => false));
		die();
	}

	if (!canVote($id, $userid)) {
		echo json_encode(array('ok' => false));
		die();
	}

	vote($id, $userid);

	echo json_encode(
		array(
			'ok' => true,
			'count' => calcPostVotes($id)
		)
	);
?>
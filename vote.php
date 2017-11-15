<?php
	require_once('utils.php');

	header('Content-type: application/json');

	if (!isLogin()) {
		echo json_encode(
			array(
				'ok' => false,
				'reason' => 'user not login'
			)
		);
		die();
	}

	if (!isset($_GET['id'])) {
		echo json_encode(
			array(
				'ok' => false,
				'reason' => 'not set post id'
			)
		);
		die();
	}

	$id = intval($_GET['id']);

	if (!isset($_COOKIE['userid'])) {
		echo json_encode(
			array(
				'ok' => false,
				'reason' => 'not set user id'
			)
		);
		die();
	}

	$userid = $_COOKIE['userid'];

	if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
		echo json_encode(
			array(
				'ok' => false,
				'reason' => 'invalid user id'
			)
		);
		die();
	}

	if (!isPostExists($id)) {
		echo json_encode(
			array(
				'ok' => false,
				'reason' => 'post not exists'
			)
		);
		die();
	}

	if (!canVote($id, $userid)) {
		echo json_encode(
			array(
				'ok' => false,
				'reason' => 'user cant vote'
			)
		);
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
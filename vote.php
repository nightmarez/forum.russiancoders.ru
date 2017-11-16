<?php
	require_once('utils.php');

	header('Content-type: application/json');

	if (!isLogin()) {
		echo json_encode(
			array(
				'answer' => false,
				'reason' => 'user not login'
			)
		);
		die();
	}

	if (!isset($_GET['id'])) {
		echo json_encode(
			array(
				'answer' => false,
				'reason' => 'not set post id'
			)
		);
		die();
	}

	$id = intval($_GET['id']);

	if (!isset($_GET['value'])) {
		echo json_encode(
			array(
				'answer' => false,
				'reason' => 'vote value not set'
			)
		);
		die();
	}

	$value = intval($_GET['value']);

	if ($value > 0) {
		$value = 1;
	} else if ($value < 0) {
		$value = -1;
	} else {
		$value = 0;
	}

	if (!isset($_COOKIE['userid'])) {
		echo json_encode(
			array(
				'answer' => false,
				'reason' => 'not set user id'
			)
		);
		die();
	}

	$userid = $_GET['userid'];

	if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
		echo json_encode(
			array(
				'answer' => false,
				'reason' => 'invalid post user id'
			)
		);
		die();
	}

	if (!isPostExists($id)) {
		echo json_encode(
			array(
				'answer' => false,
				'reason' => 'post not exists'
			)
		);
		die();
	}

	if (!canVote($id, $userid)) {
		echo json_encode(
			array(
				'answer' => false,
				'reason' => 'user cant vote'
			)
		);
		die();
	}

	$curruserid = $_COOKIE['userid'];

	if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $curruserid)) {
		echo json_encode(
			array(
				'answer' => false,
				'reason' => 'invalid user id'
			)
		);
		die();
	}

	vote($id, $curruserid, $value);

	echo json_encode(
		array(
			'answer' => true,
			'count' => calcPostVotes($id)
		)
	);
?>
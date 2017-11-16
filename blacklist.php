<?php
	require_once('utils.php');
	header('Content-type: application/json');
	echo json_encode(blackList());
?>
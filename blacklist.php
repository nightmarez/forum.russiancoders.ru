<?php
	require_once('utils.php');
	header('Content-type: application/json');

	$arr = array();

	$db = new PdoDb();

	$query =
		'SELECT `ip` FROM `ips` WHERE `userid`=:userid;';

	$req = $db->prepare($query);
	$req->bindParam(':userid', 'dcHw3LiNKzm2lFfeCWyz');
	$req->execute();

	while (list($ip) = $req->fetch(PDO::FETCH_NUM)) {
		$arr[] = $ip;
	}

	echo json_encode($arr);
?>
<?php
	require_once('utils.php');
	header('Content-type: application/json');

	$arr = array();

	$db = new PdoDb();

	$query =
		'SELECT `ip` FROM `ips` WHERE `userid`=:userid;';

	$badid = 'dcHw3LiNKzm2lFfeCWyz';

	$req = $db->prepare($query);
	$req->bindParam(':userid', $badid, PDO::PARAM_STR);
	$req->execute();

	while (list($ip) = $req->fetch(PDO::FETCH_NUM)) {
		$arr[] = $ip;
	}

	echo json_encode($arr);
?>
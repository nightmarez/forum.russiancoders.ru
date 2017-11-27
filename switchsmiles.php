<?php
	require_once('utils.php');

	$userid = $_COOKIE['userid'];
	$param = 'smiles';
	$value = $_GET['value'];

	if ($value !== 'unchecked') {
		$value = 'checked';
	} else {
		$value = 'unchecked';
	}

	setSettingsParam($userid, $param, $value, $readydb);
	echo 'ok';
?>
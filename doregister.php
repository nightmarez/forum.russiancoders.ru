<?php
	require_once('utils.php');

	$login = $_POST['login'];
	$pass = $_POST['pass'];
	$mail = $_POST['mail'];

	addUser($login, $pass, $user);
?>
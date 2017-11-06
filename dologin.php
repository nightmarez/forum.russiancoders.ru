<?php
	require_once('utils.php');

	$login = $_POST['login'];
	$pass = $_POST['pass'];

	if (!tryLogin($login, $pass)) {
		header('Location: /login.php?error=Не удалось залогиниться');
		die();
	} else {
		header('Location: /');
		die();
	}
?>
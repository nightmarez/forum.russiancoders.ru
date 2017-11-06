<?php
	require_once('utils.php');

	if (!isLogin()) {
		header('Location: /login.php');
		die();
	}

	$content = $_POST['content'];

	if ($content !== $_POST['content'] || strlen($content) > 4096) {
		//header('Location: /createtopic.php?error=Некорректно задано содержимое поста');
		die();
	}

	$userid = htmlspecialchars($_COOKIE['userid']);
	$topicid = htmlspecialchars($_POST['topicid']);

	addPost($userid, $topicid, $content);
	header('Location: /topic.php?topicid=' . $topicid);
?>
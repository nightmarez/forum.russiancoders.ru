<?php
	require_once('utils.php');

	if (!isLogin()) {
		header('Location: /login/');
		die();
	}

	$content = $_POST['content'];

	if (strlen($content) > 20000) {
		//header('Location: /createtopic.php?error=Некорректно задано содержимое поста');
		die();
	}

	$userid = htmlspecialchars($_COOKIE['userid']);
	$topicid = htmlspecialchars($_POST['topicid']);
	$postid = htmlspecialchars($_POST['postid']);

	editPost($userid, $topicid, $postid, $content);

	$pageid = ceil(($postid + 1) / postsPerPage());

	header('Location: /topic/' . $topicid . '/' . $pageid . '/#' . $postid);
	
?>
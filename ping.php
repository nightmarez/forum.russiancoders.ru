<?php
	require_once('utils.php');

	if (isLogin()) {
		updateUserOnline();
		echo '{ok=true}';
		die();
	}

	echo '{}';
?>
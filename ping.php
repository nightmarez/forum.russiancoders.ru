<?php
	require_once('utils.php');

	if (!isLogin()) {
		updateUserOnline();
	}
?>
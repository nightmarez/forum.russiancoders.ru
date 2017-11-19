<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$targetid = false;
	$targetLogin = '???';

	if (isLogin() && isset($_GET['userid'])) {
		$id = $_GET['userid'];

		if (isUserIdExists($id)) {
			$targetid = $id;
			$targetLogin = getUserLoginById($targetid);
		}
	}

	$userid = $_COOKIE['userid'];

	if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
		$targetid = false;
		$targetLogin = '???';
	}
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Сообщение пользователю <?php echo $targetLogin; ?></h3>
	</div>

	<div class="panel-body">
		<div class="table-responsive">
			<?php if ($targetid === false) { ?>
				Такого пользователя не существует.
			<?php } else { ?>
				<form method="POST" action="/dosendmessage.php">
					<input type="hidden" name="fromid" value="<?php echo $userid; ?>">
					<input type="hidden" name="toid" value="<?php echo $targetid; ?>">
					<textarea name="content" style="min-width: 800px; min-height: 300px; width: 100%; margin-bottom: 5px;"></textarea>
					<div>
						<div style="float: left;">
							<input type="submit" class="btn btn-primary" value="Отправить">
						</div>
					</div>
				</form>
			<?php } ?>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
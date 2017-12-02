<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	if (!isLogin()) {
		header('Location: /login/');
		die();
	}

	$userid = htmlspecialchars($_COOKIE['userid']);
	$content = $_POST['content'];

	if (strlen($content) > 20000) {
		//header('Location: /createtopic.php?error=Некорректно задано содержимое поста');
		die();
	}

	$topicid = htmlspecialchars($_POST['topicid']);

	if (isTopicClosed($topicid, $readydb)) {
		die();
	}
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">
			Превью сообщения в тему 
			<?php
				$query = 'SELECT `title` FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

				$req = $readydb->prepare($query);
				$req->bindParam(':topicid', $topicid);
				$req->execute();

				while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
					echo '"' . htmlspecialchars($title) . '"';
					break;
				}
			?>
		</h3>
	</div>

	<div class="panel-body">
		<div class="panel panel-info" id="message<?php echo $postnumber; ?>">
			<div class="panel-heading">
				Превью
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<?php echo filterMessage($content, $userid); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if (isLogin() && !isTopicClosed($topicid, $readydb)) { ?>
	<div class="panel panel-primary" style="margin: 20px;">
		<div class="panel-heading">
			<h3 class="panel-title">Сообщение</h3>
		</div>

		<div class="panel-body">
			<div class="table-responsive">
				<form method="POST" action="/addpost.php">
					<input type="hidden" name="topicid" value="<?php echo $topicid; ?>">
					<textarea name="content" style="min-width: 800px; min-height: 300px; width: 100%; margin-bottom: 5px;"></textarea>
					<div>
						<div style="float: left;">
							<input type="submit" class="btn btn-primary" value="Отправить">
						</div>
						<div style="float: left; margin-left: 10px;">
							<input type="button" class="btn btn-primary" id="upload-image-btn" value="Загрузить изображение">
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>

<?php include_once('footer.php'); ?>
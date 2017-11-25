<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$userid = false;

	if (isset($_GET['userid'])) {
		$userid = htmlspecialchars($_GET['userid']);

		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
			$userid = false;
		}
	}
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">
			Сообщения пользователя <?php echo getUserLoginById($userid); ?>
		</h3>
	</div>
	<script>
		document.title = 'Сообщения пользователя';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			<?php
				$query =
					'SELECT `topicid`, `userid`, `content`, `created` FROM `posts` WHERE `userid`=:userid ORDER BY `id` DESC LIMIT 0, 30;';

				$req = $readydb->prepare($query);
				$req->bindParam(':userid', $userid);
				$req->execute();

				while (list($topicid, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
					?>
						<div class="panel panel-info">
							<div class="panel-heading">
								<div class="row">
									<div class="col-md-4">
										<a href="/topic/<?php echo htmlspecialchars($topicid); ?>/"><?php echo getTopicTitleById($topicid, $readydb); ?></a>
									</div>
									<div class="col-md-4">
										<a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo getUserLoginById($userid, $readydb); ?></a>
									</div>
									<div class="col-md-4" style="text-align: right;"><?php echo $created; ?></div>
								</div>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12"><?php echo filterMessage($content, $userid); ?></div>
								</div>
							</div>
						</div>
					<?php
				}
			?>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
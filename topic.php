<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$topicid = htmlspecialchars($_GET['topicid']);
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">
			<?php
				$db = new PdoDb();

				$query =
					'SELECT `title` FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

				$req = $db->prepare($query);
				$req->bindParam(':topicid', $topicid);
				$req->execute();

				while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
					echo htmlspecialchars($title);
					break;
				}
			?>
		</h3>
	</div>

	<div class="panel-body">
		<div class="table-responsive">
					<?php
						$db = new PdoDb();

						$query =
							'SELECT `id`, `userid`, `content`, `created` FROM `posts` WHERE `topicid`=:topicid ORDER BY `id` ASC LIMIT 0, 300;';

						$req = $db->prepare($query);
						$req->bindParam(':topicid', $topicid);
						$req->execute();

						while (list($id, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<table class="table topic-posts">
									<tbody>
										<tr>
											<td>#</td>
											<td><?php
													$pdo = new PdoDb();

													$query =
														'SELECT `login` FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

													$r = $pdo->prepare($query);
													$r->bindParam(':userid', $userid);
													$r->execute();

													while (list($login) = $r->fetch(PDO::FETCH_NUM)) {
														?><a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo htmlspecialchars($login); ?></a><?php
														break;
													}
												?></td>
											<td><?php
													echo $created;
												?></td>
											<td>
												<span class="triangle-up"></span><span class="likes-counter"><?php echo calcPostVotes($id, $db); ?></span><span class="triangle-down"></span>
											</td>
										</tr>
										<tr>
											<td colspan="4"><?php echo filterMessage($content, $userid); ?></td>
										</tr>
									</tbody>
								</table>
							<?php
						}
					?>
		</div>
	</div>
</div>

<?php if (isLogin()) { ?>
	<div class="panel panel-primary" style="margin: 20px;">
		<div class="panel-heading">
			<h3 class="panel-title">Добавить сообщение</h3>
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
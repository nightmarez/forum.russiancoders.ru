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
					echo $title;
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
							'SELECT `id`, `userid`, `content`, `created` FROM `posts` WHERE `topicid`=:topicid ORDER BY `id` ASC LIMIT 0, 100;';

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
														?><a href="/user.php?userid=<?php echo htmlspecialchars($userid); ?>"><?php echo htmlspecialchars($login); ?></a><?php
														break;
													}
												?></td>
											<td><?php
													echo $created;
												?></td>
										</tr>
										<tr>
											<td colspan="3"><?php echo filterMessage($content, $userid); ?></td>
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
				<form method="POST" action="addpost.php">
					<input type="hidden" name="topicid" value="<?php echo $topicid; ?>">
					<textarea name="content" style="min-width: 800px; min-height: 300px; width: 100%; margin-bottom: 5px;"></textarea>
					<div>
						<input type="submit" class="btn btn-primary" value="Отправить">
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>

<?php include_once('footer.php'); ?>
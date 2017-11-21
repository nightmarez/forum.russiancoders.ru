<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$topicid = htmlspecialchars($_GET['topicid']);
	$pagesCount = topicPagesCount($topicid);
	$page = 0;

	if (isset($_GET['page'])) {
		$page = intval($_GET['page']);
	}

	if ($page >= $pagesCount) {
		$page = $pagesCount - 1;
	}
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
							'SELECT `id`, `userid`, `content`, `created` FROM `posts` WHERE `topicid`=:topicid ORDER BY `id` ASC LIMIT :skipcount, :pagesize;';

						$ppp = postsPerPage();
						$skipCount = $page * $ppp;

						echo 'topic: ' . $topicid . '<br>';
						echo 'ppp: ' . $ppp . '<br>';
						echo 'page: ' . $page . '<br>';
						echo 'skip: ' . $skipCount . '<br>';

						$req = $db->prepare($query);
						$req->bindParam(':topicid', $topicid);
						$req->bindParam(':pagesize', $ppp);
						$req->bindParam(':skipcount', $skipCount);
						$req->execute();

						while (list($id, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<table class="table topic-posts">
									<tbody>
										<tr>
											<td>
												<?php
													$pdo = new PdoDb();

													$query =
														'SELECT MD5(LOWER(TRIM(`mail`))) FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

													$r = $pdo->prepare($query);
													$r->bindParam(':userid', $userid);
													$r->execute();

													while (list($mail) = $r->fetch(PDO::FETCH_NUM)) {
												?>
													<img style="margin-right: 5px;" src="<?php echo 'https://secure.gravatar.com/avatar/' . $mail . '.jpg?s=25';?>">
												<?php
														break;
													}
												?>
											</td>
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
												<span class="triangle-up <?php if (!canVote($id, $userid, $db)) { echo 'triangle-up-disabled'; } ?>" data-id="<?php echo $id; ?>" data-userid="<?php echo $userid; ?>"></span><span class="likes-counter"><?php echo calcPostVotes($id, $db); ?></span><span class="triangle-down <?php if (!canVote($id, $userid, $db)) { echo 'triangle-down-disabled'; } ?>" data-id="<?php echo $id; ?>" data-userid="<?php echo $userid; ?>"></span>
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

<?php
	if ($pagesCount > 0) {
?>

	<nav aria-label="Page navigation">
		<ul class="pagination">
			<li<?php if ($page == 0) { echo ' class="disabled"'; } ?>>
				<a href="#" aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<?php
				for ($p = 0; $p < $pagesCount; ++$p) {
					?>
						<li<?php if ($p == $page) { echo ' class="active"'; } ?>><a href="#"><?php echo ($p + 1); ?></a></li>
					<?php
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="#" aria-label="Next">
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</nav>

<?php
	}
?>

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
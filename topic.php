<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$topicid = htmlspecialchars($_GET['topicid']);
	$pagesCount = topicPagesCount($topicid);
	$page = 0;
	$ppp = postsPerPage();

	if (isset($_GET['page'])) {
		$page = intval($_GET['page']) - 1;
	}

	if ($page >= $pagesCount) {
		$page = $pagesCount - 1;
	}

	$number = $page * $ppp;
?>

<?php
	if ($pagesCount > 1) {
?>

	<nav aria-label="Page navigation" style="text-align: center;">
		<ul class="pagination">
			<li<?php if ($page == 0) { echo ' class="disabled"'; } ?>>
				<a href="/topic/<?php echo $topicid; ?>/<?php echo $page; ?>/" rel="prev" aria-label="Previous"<?php if ($page == 0) { echo ' onclick="return false" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<?php
				for ($p = 1; $p <= $pagesCount; ++$p) {
					?>
						<li<?php if ($p == $page + 1) { echo ' class="active"'; } ?>><a href="/topic/<?php echo $topicid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
					<?php
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="/topic/<?php echo $topicid; ?>/<?php echo ($page + 2); ?>/" rel="next" aria-label="Next"<?php if ($page >= $pagesCount - 1) { echo ' onclick="return false;" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</nav>

<?php
	}
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">
			<?php
				$query = 'SELECT `title` FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

				$req = $readydb->prepare($query);
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
		<?php
			$query = 'SELECT `id`, `userid`, `content`, `created` FROM `posts` WHERE `topicid`=:topicid ORDER BY `id` ASC LIMIT :skipcount, :pagesize;';
			$skipCount = $page * $ppp;

			$req = $readydb->prepare($query);
			$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
			$req->bindParam(':pagesize', $ppp, PDO::PARAM_INT);
			$req->bindParam(':skipcount', $skipCount, PDO::PARAM_INT);
			$req->execute();

			while (list($id, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
				?>
					<div class="panel panel-info">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-1">
									<img src="<?php echo getGravatarLink($userid, 25, $readydb); ?>" alt="<?php echo $login; ?>">
								</div>
								<div class="col-md-1">
									<a class="message-link" href="/topic/<?php echo $topicid; ?>/<?php echo ($page + 1); ?>/#<?php echo $number; ?>" title="Ссылка на сообщение">#<?php echo $number++; ?></a>
								</div>
								<div class="col-md-6" id="message<?php echo $number; ?>">
									<a href="/user/<?php echo htmlspecialchars($userid); ?>/" rel="author"><?php echo getUserLoginById($userid, $readydb); ?></a>
								</div>
								<div class="col-md-2" style="text-align: right;"><?php
									echo $created;
									?></div>
								<div class="col-md-2">
									<span class="triangle-up <?php if (!canVote($id, $userid, $readydb)) { echo 'triangle-up-disabled'; } ?>" data-id="<?php echo $id; ?>" data-userid="<?php echo $userid; ?>"></span><span class="likes-counter"><?php echo calcPostVotes($id, $readydb); ?></span><span class="triangle-down <?php if (!canVote($id, $userid, $readydb)) { echo 'triangle-down-disabled'; } ?>" data-id="<?php echo $id; ?>" data-userid="<?php echo $userid; ?>"></span>
								</div>
							</div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<?php echo filterMessage($content, $userid); ?>
								</div>
							</div>
						</div>
					</div>
				<?php
			}
		?>
	</div>
</div>

<?php
	if ($pagesCount > 1) {
?>

	<nav aria-label="Page navigation" style="text-align: center;">
		<ul class="pagination">
			<li<?php if ($page == 0) { echo ' class="disabled"'; } ?>>
				<a href="/topic/<?php echo $topicid; ?>/<?php echo $page; ?>/" rel="prev" aria-label="Previous"<?php if ($page == 0) { echo ' onclick="return false" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<?php
				for ($p = 1; $p <= $pagesCount; ++$p) {
					?>
						<li<?php if ($p == $page + 1) { echo ' class="active"'; } ?>><a href="/topic/<?php echo $topicid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
					<?php
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="/topic/<?php echo $topicid; ?>/<?php echo ($page + 2); ?>/" rel="next" aria-label="Next"<?php if ($page >= $pagesCount - 1) { echo ' onclick="return false;" onmousedown="return false"'; } ?>>
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
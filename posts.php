<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$userid = false;

	if (isset($_GET['userid'])) {
		$userid = htmlspecialchars($_GET['userid']);

		if (!preg_match('/^\{?[0-9a-zA-Z]{1,30}\}?$/', $userid)) {
			$userid = false;
		}
	}

	$postsCount = getPostsCountByUserId($userid, $readydb);
	$pagesCount = postsPagesCount($postsCount);
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
	echo "<!--\r\n";
	echo '====================================================' . "\r\n";
	echo "debug info\r\n";
	echo 'User ID: ' . $userid . "\r\n"; 
	echo 'Posts Count: ' . $postsCount . "\r\n";
	echo 'Pages Count: ' . $pagesCount  . "\r\n";
	echo 'Page: ' . $page  . "\r\n";
	echo 'Posts per Page: ' . $ppp  . "\r\n";
	echo '====================================================' . "\r\n";
	echo "-->\r\n";
?>

<?php
	if ($pagesCount > 1) {
?>
	<nav aria-label="Page navigation" style="text-align: center;">
		<ul class="pagination">
			<li<?php if ($page == 0) { echo ' class="disabled"'; } ?>>
				<a href="/posts/<?php echo $userid; ?>/<?php echo $page; ?>/" rel="prev" aria-label="Previous"<?php if ($page == 0) { echo ' onclick="return false" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<?php
				$dots = false;

				$poffset1 = 3;
				$poffset2 = 3;

				if ($page == 4) {
					$poffset1 = 1;
				} else if ($page == 5) {
					$poffset1 = 2;
				}

				if ($page == $pagesCount - 5) {
					$poffset2 = 1;
				} else if ($page == $pagesCount - 6) {
					$poffset2 = 2;
				}

				if ($pagesCount >= 12) {
					for ($p = 1; $p <= $pagesCount; ++$p) {
						$pagen = $p - 1;

						if ($pagen < $poffset1 || 
							$pagen > $pagesCount - ($poffset2 + 1) || 
							$pagen > $page - 3 && $pagen < $page + 3 || 
							(($page < 3 || $page > $pagesCount - 4) && $pagen > ceil($pagesCount / 2 - 3) && $pagen < ceil($pagesCount / 2 + 3)))
						{
							$dots = false;
							?>
								<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/posts/<?php echo $userid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
							<?php
						}
						else
						{
							if (!$dots)
							{
								$dots = true;
								?>
									<li class="disabled"><a style="border-bottom: none; border-top: none;" href="#" onclick="return false" onmousedown="return false">...</a></li>
								<?php
							}
						}
					}
				} else {
					for ($p = 1; $p <= $pagesCount; ++$p) {
						$pagen = $p - 1;

						?>
							<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/posts/<?php echo $userid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
						<?php
					}
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="/posts/<?php echo $userid; ?>/<?php echo ($page + 2); ?>/" rel="next" aria-label="Next"<?php if ($page >= $pagesCount - 1) { echo ' onclick="return false;" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</nav>
<?php
	}
?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
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
					'SELECT `id`, `topicid`, `content`, `created` 
					FROM `posts` 
					WHERE `userid`=:userid 
					ORDER BY `id` DESC 
					LIMIT :skipcount, :pagesize;';

				$skipCount = $page * $ppp;

				$req = $readydb->prepare($query);
				$req->bindParam(':userid', $userid, PDO::PARAM_STR);
				$req->bindParam(':skipcount', $skipCount, PDO::PARAM_INT);
				$req->bindParam(':pagesize', $ppp, PDO::PARAM_INT);
				$req->execute();

				while (list($id, $topicid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
					$login = getUserLoginById($userid, $readydb);

					?>
						<div class="panel panel-info">
							<div class="panel-heading">
								<div class="row">
									<div class="col-md-3">
										<img src="<?php echo getGravatarLink($userid, 50, $readydb); ?>" alt="<?php echo $login; ?>" style="border-radius: 50%; border: 2px solid <?php if (isUserOnline($userid, $readydb) == 1) { echo 'forestgreen'; } else { echo 'silver'; } ?>; padding: 2px; position: absolute; width: 50px; height: 50px; min-width: 50px; min-height: 50px; background-color: white; top: -16px; left: 10px;">
										<a href="/user/<?php echo htmlspecialchars($userid); ?>/" style="float: left; margin-left: 50px;" title="Пользователь <?php echo getUserTitleById($userid, $readydb); ?>" rel="author"><?php echo getUserTitleById($userid, $readydb); ?></a>
									</div>
									<div class="col-md-7">
										<?php
											$topicTitle = getTopicTitleById($topicid, $readydb);
											$sectionid = getSectionIdByTopicId($topicid, $readydb);
											$sectionTitle = getSectionTitleById($sectionid, $readydb);
											$postnumber = getPostNumber($topicid, $id, $readydb);
											$pageNumber = getPostPageNumber($topicid, $id, $readydb);
										?>
										<a href="/">Форум</a>
										→
										<a href="/section/<?php echo $sectionid; ?>/"><?php echo $sectionTitle; ?></a>
										→
										<a href="/topic/<?php echo $topicid; ?>/"><?php echo $topicTitle; ?></a>
										→
										<a href="/topic/<?php echo $topicid; ?>/<?php echo $pageNumber; ?>/">страница <?php echo $pageNumber; ?></a>
										→
										<a href="/topic/<?php echo $topicid; ?>/<?php echo $pageNumber; ?>/#<?php echo $postnumber; ?>">#<?php echo $postnumber; ?></a>
										<a href="/post/<?php echo htmlspecialchars($userid); ?>/<?php echo $topicid; ?>/<?php echo $postnumber; ?>/" style="width: 10px; height: 10px; font-size: 13px; padding-top: 2px; margin-left: 5px;"><i class="glyphicon glyphicon-edit"></i></a>
										<a href="/deletepost/<?php echo htmlspecialchars($userid); ?>/<?php echo $topicid; ?>/<?php echo $postnumber; ?>/" style="width: 10px; height: 10px; font-size: 13px; padding-top: 2px;"><i class="glyphicon glyphicon-trash"></i></a>
									</div>
									<div class="col-md-2" style="text-align: right;"><?php echo $created; ?></div>
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

<?php
	if ($pagesCount > 1) {
?>
	<nav aria-label="Page navigation" style="text-align: center;">
		<ul class="pagination">
			<li<?php if ($page == 0) { echo ' class="disabled"'; } ?>>
				<a href="/posts/<?php echo $userid; ?>/<?php echo $page; ?>/" rel="prev" aria-label="Previous"<?php if ($page == 0) { echo ' onclick="return false" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<?php
				$dots = false;

				$poffset1 = 3;
				$poffset2 = 3;

				if ($page == 4) {
					$poffset1 = 1;
				} else if ($page == 5) {
					$poffset1 = 2;
				}

				if ($page == $pagesCount - 5) {
					$poffset2 = 1;
				} else if ($page == $pagesCount - 6) {
					$poffset2 = 2;
				}

				if ($pagesCount >= 12) {
					for ($p = 1; $p <= $pagesCount; ++$p) {
						$pagen = $p - 1;

						if ($pagen < $poffset1 || 
							$pagen > $pagesCount - ($poffset2 + 1) || 
							$pagen > $page - 3 && $pagen < $page + 3 || 
							(($page < 3 || $page > $pagesCount - 4) && $pagen > ceil($pagesCount / 2 - 3) && $pagen < ceil($pagesCount / 2 + 3)))
						{
							$dots = false;
							?>
								<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/posts/<?php echo $userid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
							<?php
						}
						else
						{
							if (!$dots)
							{
								$dots = true;
								?>
									<li class="disabled"><a style="border-bottom: none; border-top: none;" href="#" onclick="return false" onmousedown="return false">...</a></li>
								<?php
							}
						}
					}
				} else {
					for ($p = 1; $p <= $pagesCount; ++$p) {
						$pagen = $p - 1;

						?>
							<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/posts/<?php echo $userid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
						<?php
					}
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="/posts/<?php echo $userid; ?>/<?php echo ($page + 2); ?>/" rel="next" aria-label="Next"<?php if ($page >= $pagesCount - 1) { echo ' onclick="return false;" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</nav>
<?php
	}
?>

<?php include_once('footer.php'); ?>

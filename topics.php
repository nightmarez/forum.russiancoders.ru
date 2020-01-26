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

	$topicsCount = getTopicsCountByUserId($userid, $readydb);
	$pagesCount = topicsPagesCount($topicsCount);
	$page = 0;
	$ppp = topicsPerPage();

	if (isset($_GET['page'])) {
		$page = intval($_GET['page']) - 1;
	}

	if ($page < 0) {
		$page = 0;
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
	echo 'Topics Count: ' . $topicsCount . "\r\n";
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
				<a href="/topics/<?php echo $userid; ?>/<?php echo $page; ?>/" rel="prev" aria-label="Previous"<?php if ($page == 0) { echo ' onclick="return false" onmousedown="return false"'; } ?>>
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
								<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/topics/<?php echo $userid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
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
							<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/topics/<?php echo $userid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
						<?php
					}
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="/topics/<?php echo $userid; ?>/<?php echo ($page + 2); ?>/" rel="next" aria-label="Next"<?php if ($page >= $pagesCount - 1) { echo ' onclick="return false;" onmousedown="return false"'; } ?>>
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
			Темы пользователя <?php echo getUserTitleById($userid); ?>
		</h3>
	</div>
	<script>
		document.title = 'Темы пользователя';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			<?php
				$query =
					'SELECT `topicid`, `sectionid`, `title`, `created` 
					FROM `topics` 
					WHERE `userid`=:userid 
					ORDER BY `id` DESC 
					LIMIT :skipcount, :pagesize;';

				$skipCount = $page * $ppp;

				$req = $readydb->prepare($query);
				$req->bindParam(':userid', $userid, PDO::PARAM_STR);
				$req->bindParam(':skipcount', $skipCount, PDO::PARAM_INT);
				$req->bindParam(':pagesize', $ppp, PDO::PARAM_INT);
				$req->execute();

				while (list($topicid, $sectionid, $title, $created) = $req->fetch(PDO::FETCH_NUM)) {
					?>
						<div class="panel panel-info">
							<div class="panel-heading">
								<div class="row">
									<div class="col-md-10">
										<?php
											$topicTitle = getTopicTitleById($topicid, $readydb);
											$sectionid = getSectionIdByTopicId($topicid, $readydb);
											$sectionTitle = getSectionTitleById($sectionid, $readydb);
										?>
										<a href="/">Форум</a>
										→
										<a href="/section/<?php echo $sectionid; ?>/"><?php echo $sectionTitle; ?></a>
										→
										<a href="/topic/<?php echo $topicid; ?>/"><?php echo $topicTitle; ?></a>
									</div>
									<div class="col-md-2" style="text-align: right;"><?php echo $created; ?></div>
								</div>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<?php echo filterMessage(getTopicInitMessage($topicid, $readydb), $userid); ?>
									</div>
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
				<a href="/topics/<?php echo $userid; ?>/<?php echo $page; ?>/" rel="prev" aria-label="Previous"<?php if ($page == 0) { echo ' onclick="return false" onmousedown="return false"'; } ?>>
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
								<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/topics/<?php echo $userid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
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
							<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/topics/<?php echo $userid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
						<?php
					}
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="/topics/<?php echo $userid; ?>/<?php echo ($page + 2); ?>/" rel="next" aria-label="Next"<?php if ($page >= $pagesCount - 1) { echo ' onclick="return false;" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</nav>
<?php
	}
?>

<?php include_once('footer.php'); ?>

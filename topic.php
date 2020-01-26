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

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<?php
			$sectionid = getSectionIdByTopicId($topicid, $readydb);
			$sectiontitle = getSectionTitleById($sectionid, $readydb);
		?>
		<div class="panel-title">
			<a href="/">Форум</a> → <a href="/section/<?php echo $sectionid; ?>/"><?php echo $sectiontitle; ?></a> → <?php
				$query = 'SELECT `title` FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

				$req = $readydb->prepare($query);
				$req->bindParam(':topicid', $topicid);
				$req->execute();

				while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
					echo htmlspecialchars($title);
					break;
				}
			?>
		</div>
	</div>

	<div class="panel-body">
		<?php
			$query = 
				'SELECT `id`, `userid`, `content`, `created` 
				FROM `posts` 
				WHERE `topicid`=:topicid 
				ORDER BY `id` ASC 
				LIMIT :skipcount, :pagesize;';

			$skipCount = $page * $ppp;

			$req = $readydb->prepare($query);
			$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);
			$req->bindParam(':pagesize', $ppp, PDO::PARAM_INT);
			$req->bindParam(':skipcount', $skipCount, PDO::PARAM_INT);
			$req->execute();

			while (list($id, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
					$postnumber = getPostNumber($topicid, $id, $readydb);
					$login = getUserLoginById($userid, $readydb);
				?>
					<div class="panel panel-info" id="message<?php echo $postnumber; ?>">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-8">
									<a href="/topic/<?php echo $topicid; ?>/<?php echo ($page + 1); ?>/#<?php echo $postnumber; ?>" style="float: left; margin-right: 10px;">#<?php echo $postnumber; ?></a>
									<img src="<?php echo getGravatarLink($userid, 50, $readydb); ?>" alt="<?php echo $login; ?>" style="border-radius: 50%; border: 2px solid <?php if (isUserOnline($userid, $readydb) == 1) { echo 'forestgreen'; } else { echo 'silver'; } ?>; padding: 2px; position: absolute; width: 50px; height: 50px; min-width: 50px; min-height: 50px; background-color: white; top: -16px; left: <?php if ($postnumber < 10) { echo '-14.4'; } else if ($postnumber < 100) { echo '-6.64'; } else if ($postnumber < 1000) { echo '1.15'; } else { echo '8'; } ?>px; margin-left: 50px;">
									<a href="/user/<?php echo htmlspecialchars($userid); ?>/" style="float: left; margin-right: 10px; margin-left: 50px;" title="Пользователь <?php echo $login; ?>" rel="author"><?php echo getUserTitleById($userid, $readydb); ?></a>
								</div>
								<div class="col-md-3" style="text-align: right;">
									<span style="float: left;"><?php echo $created; ?></span>
									<a href="#" class="cute-post" style="float: left; width: 10px; height: 10px; font-size: 13px; padding-top: 2px; margin-left: 10px;"><i class="glyphicon glyphicon-scissors"></i></a>
									<a href="/post/<?php echo htmlspecialchars($userid); ?>/<?php echo $topicid; ?>/<?php echo $postnumber; ?>/" style="float: left; width: 10px; height: 10px; font-size: 13px; padding-top: 2px; margin-left: 10px;"><i class="glyphicon glyphicon-edit"></i></a>
									<a href="/deletepost/<?php echo htmlspecialchars($userid); ?>/<?php echo $topicid; ?>/<?php echo $postnumber; ?>/" style="float: left; width: 10px; height: 10px; font-size: 13px; padding-top: 2px; margin-left: 10px;"><i class="glyphicon glyphicon-trash"></i></a>
								</div>
								<div class="col-md-1" style="text-align: right;">
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
								<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/topic/<?php echo $topicid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
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
							<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/topic/<?php echo $topicid; ?>/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
						<?php
					}
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

<?php if (isLogin() && !isTopicClosed($topicid, $readydb)) { ?>
	<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
		<div class="panel-heading">
			<h3 class="panel-title">Добавить сообщение</h3>
		</div>

		<div class="panel-body">
			<div class="table-responsive">
				<form method="POST" action="/addpost.php">
					<input type="hidden" name="topicid" value="<?php echo $topicid; ?>">
					<textarea name="content" style="min-width: 800px; min-height: 300px; width: 100%; margin-bottom: 5px;"></textarea>
					<div>
						<!--
						<div style="float: left;">
							<input type="submit" class="btn btn-primary" value="Отправить">
						</div>
						-->
						<div style="float: left;">
							<input type="button" class="btn btn-primary" id="preview-btn" value="Предпросмотр">
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

<?php if (!isLogin() && !isTopicClosed($topicid, $readydb)) { ?>
	<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
		<div class="panel-heading">
			<h3 class="panel-title">Авторизуйтесь, чтобы добавить сообщение</h3>
		</div>

		<div class="panel-body">
			<div class="table-responsive">
                <?php
                    include_once('google-credentials.php');

                    $params = array(
                        'redirect_uri'  => $redirect_uri,
                        'response_type' => 'code',
                        'client_id'     => $client_id,
                        'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
                    );

                    $link = $url . '?' . urldecode(http_build_query($params));
                ?>
                <a class="btn btn-block btn-social btn-google" style="width: 200px; color: white;" href="<?php echo $link; ?>">
                    <span class="fa fa-google"></span> Войти через Google
                </a>
			</div>
		</div>
	</div>
<?php } ?>

<?php include_once('footer.php'); ?>

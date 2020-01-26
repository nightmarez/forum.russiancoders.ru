<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	if (empty($_GET['userid']) || empty($_GET['topicid'])) {
		die('404');
	}

	$userid = $_GET['userid'];

	if (!validateUserId($userid)) {
		die('Incorrect user ID');
	}

	$topicid = $_GET['topicid'];

	if (!validateTopicId($topicid)) {
		die('Incorrect topic ID');
	}

	$postnumber = empty($_GET['postnumber']) ? 0 : $_GET['postnumber'];

	if (!preg_match('/^\{?[0-9]{1,10}\}?$/', $postnumber)) {
		die('Incorrect post number');
	}

	$postnumber = intval($postnumber);
	$skipcount = $postnumber;
?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<div class="panel-title">
			Сообщение
		</div>
	</div>

	<div class="panel-body">
		<?php
			$postUserId = 0;
			$postId = 0;
			$postText = '';

			$query = $postnumber == 0 ?
				'SELECT `id`, `userid`, `content`, `created` 
				FROM `posts` 
				WHERE `topicid`=:topicid 
				ORDER BY `id` ASC LIMIT 1;' :
				'SELECT `id`, `userid`, `content`, `created` 
				FROM `posts` 
				WHERE `topicid`=:topicid 
				ORDER BY `id` ASC LIMIT :skipcount, 1;';

			$req = $readydb->prepare($query);
			$req->bindParam(':topicid', $topicid, PDO::PARAM_STR);

			if ($postnumber > 0) {
				$req->bindParam(':skipcount', $skipcount, PDO::PARAM_INT);
			}

			$req->execute();

			while (list($id, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
					$login = getUserLoginById($userid, $readydb);
					$page = ceil(($postnumber + 1) / postsPerPage());

					$postId = $id;
					$postText = $content;
				?>
					<div class="panel panel-info" id="message<?php echo $postnumber; ?>">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-9">
									<a href="/topic/<?php echo $topicid; ?>/<?php echo $page; ?>/#<?php echo $postnumber; ?>" style="float: left; margin-right: 10px;">#<?php echo $postnumber; ?></a>
									<img src="<?php echo getGravatarLink($userid, 50, $readydb); ?>" alt="<?php echo $login; ?>" style="border-radius: 50%; border: 2px solid <?php if (isUserOnline($userid, $readydb) == 1) { echo 'forestgreen'; } else { echo 'silver'; } ?>; padding: 2px; position: absolute; width: 50px; height: 50px; min-width: 50px; min-height: 50px; background-color: white; top: -16px; left: <?php if ($postnumber < 10) { echo '-14.4'; } else if ($postnumber < 100) { echo '-6.64'; } else if ($postnumber < 1000) { echo '1.15'; } else { echo '8'; } ?>px; margin-left: 50px;">
									<a href="/user/<?php echo htmlspecialchars($userid); ?>/" style="float: left; margin-right: 10px; margin-left: 50px;" title="Пользователь <?php echo $login; ?>" rel="author"><?php echo $login; ?></a>
								</div>
								<div class="col-md-2" style="text-align: right;">
									<span style="float: left;"><?php echo $created; ?></span>
									<a href="/post/<?php echo htmlspecialchars($userid); ?>/<?php echo $topicid; ?>/<?php echo $postnumber; ?>/" style="float: right;"></a>
								</div>
								<div class="col-md-1" style="text-align: right;">
									<span class="triangle-up <?php if (!canVote($id, $userid, $readydb)) { echo 'triangle-up-disabled'; } ?>" data-id="<?php echo $id; ?>" data-userid="<?php echo $userid; ?>"></span><span class="likes-counter"><?php echo calcPostVotes($id, $readydb); ?></span><span class="triangle-down <?php if (!canVote($id, $userid, $readydb)) { echo 'triangle-down-disabled'; } ?>" data-id="<?php echo $id; ?>" data-userid="<?php echo $userid; ?>"></span>
								</div>
							</div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<?php echo filterMessage($content, $userid); $postUserId = $userid; ?>
								</div>
							</div>
						</div>
					</div>
				<?php
			}
		?>

		<div>
			<form method="GET" action="/topic/<?php echo $topicid; ?>/<?php echo $page; ?>/#<?php echo $postnumber; ?>">
				<div>
					<div style="float: left;">
						<input type="submit" class="btn btn-primary" value="Вернуться к теме">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

		<?php if (isAdmin() || isLogin() && $postUserId == $_COOKIE['userid']) { ?>
			<?php if ($postUserId !== 'jYzACIND80rGj0XngB3N') { ?>
				<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
					<div class="panel-heading">
						<h3 class="panel-title">Редактировать сообщение</h3>
					</div>

					<div class="panel-body">
						<div class="table-responsive">
							<form method="POST" action="/editpost.php">
								<input type="hidden" name="topicid" value="<?php echo $topicid; ?>">
								<input type="hidden" name="postid" value="<?php echo $postId; ?>">
								<textarea name="content" style="min-width: 800px; min-height: 300px; width: 100%; margin-bottom: 5px;"><?php echo htmlspecialchars($postText); ?></textarea>
								<div>
									<div style="float: left;">
										<input type="submit" class="btn btn-primary" id="preview-btn" value="Сохранить">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>

<?php include_once('footer.php'); ?>
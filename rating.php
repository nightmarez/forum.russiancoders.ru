<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">История рейтинга</h3>
	</div>
	<script>
		document.title = 'История рейтинга';
	</script>

	<?php
		$filter = 'all';

		if (isset($_GET['filter'])) {
			$f = $_GET['filter'];

			if ($f == 'negative') {
				$filter = 'neg';
			} else if ($f = 'positive') {
				$filter = 'pos';
			}
		}

		$userid = false;

		if (isset($_GET['userid'])) {
			$userid = $_GET['userid'];

			if (!validateUserId($userid)) {
				die('Invalid user id');
			}
		}
	?>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Пользователь</th>
						<th>Пост</th>
						<th>Оценка</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$query = '';

						if ($filter == 'all') {
							$query = 
								'SELECT `likes`.`postid`, `likes`.`userid`, `likes`.`value` FROM `likes`
								LEFT JOIN `posts`
								ON `likes`.`postid` = `posts`.`id`
								WHERE `posts`.`userid` = :userid;';
						} else if ($filter == 'neg') {
							$query = 
								'SELECT `likes`.`postid`, `likes`.`userid`, `likes`.`value` FROM `likes`
								LEFT JOIN `posts`
								ON `likes`.`postid` = `posts`.`id`
								WHERE `posts`.`userid` = :userid AND `likes`.`value` < 0;';
						} else if ($filter == 'pos') {
							$query = 
								'SELECT `likes`.`postid`, `likes`.`userid`, `likes`.`value` FROM `likes`
								LEFT JOIN `posts`
								ON `likes`.`postid` = `posts`.`id`
								WHERE `posts`.`userid` = :userid AND `likes`.`value` > 0;';
						}

						$req = $readydb->prepare($query);
						$req->bindParam(':userid', $userid);
						$req->execute();

						while (list($postid, $userid, $value) = $req->fetch(PDO::FETCH_NUM)) {
							$login = getUserLoginById($userid, $readydb);

							?>
								<tr>
									<td>
										<img src="<?php echo getGravatarLink($userid, 25, $readydb); ?>" alt="<?php echo $login; ?>" style="float: left; margin-right: 10px; margin-top: -2px;">
										<a href="/user/<?php echo htmlspecialchars($userid); ?>/" style="float: left;" title="Пользователь <?php echo $login; ?>" rel="author"><?php echo $login; ?></a>
									</td>
									<td>
										<?php
											$topicid = getTopicIdByPostId($postid, $readydb);
											$topicTitle = getTopicTitleById($topicid, $readydb);
											$sectionid = getSectionIdByTopicId($topicid, $readydb);
											$sectionTitle = getSectionTitleById($sectionid, $readydb);
											$postnumber = getPostNumber($topicid, $id, $readydb);
											$page = getPostPageNumber($topicid, $id, $readydb);
										?>
										<a href="/">Форум</a>
										→
										<a href="/section/<?php echo $sectionid; ?>/"><?php echo $sectionTitle; ?></a>
										→
										<a href="/topic/<?php echo $topicid; ?>/"><?php echo $topicTitle; ?></a>
										→
										<a href="/topic/<?php echo $topicid; ?>/<?php echo $page; ?>/">страница <?php echo $page; ?></a>
										→
										<a href="/topic/<?php echo $topicid; ?>/<?php echo $page; ?>/#<?php echo $postnumber; ?>">#<?php echo $postnumber; ?></a>
									</td>
									<td>
										<?php if ($value > 0) { ?>
											<span style="color: #00aa00"><?php echo $value; ?></span>
										<?php } else if ($value < 0) { ?>
											<span style="color: #aa0000"><?php echo $value; ?></span>
										<?php } else { ?>
											<span style="color: #000000"><?php echo $value; ?></span>
										<?php } ?>
									</td>
								</tr>
							<?php
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
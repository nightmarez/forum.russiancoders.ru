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
					'SELECT `id`, `topicid`, `userid`, `content`, `created` 
					FROM `posts` 
					WHERE `userid`=:userid 
					ORDER BY `id` 
					DESC;';

				$req = $readydb->prepare($query);
				$req->bindParam(':userid', $userid);
				$req->execute();

				while (list($id, $topicid, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
					$login = getUserLoginById($userid, $readydb);

					?>
						<div class="panel panel-info">
							<div class="panel-heading">
								<div class="row">
									<div class="col-md-4">
										<img src="<?php echo getGravatarLink($userid, 25, $readydb); ?>" alt="<?php echo $login; ?>" style="float: left; margin-right: 10px; margin-top: -2px;">
										<a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo $login; ?></a>
									</div>
									<div class="col-md-6">
										<?php
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

<?php include_once('footer.php'); ?>
<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">
			Трекер
		</h3>
	</div>
	<script>
		document.title = 'Трекер';
	</script>

	<div class="panel-body">
		<?php
			$query = 'SELECT `id`, `topicid`, `userid`, `content`, `created` FROM `posts` ORDER BY `id` DESC LIMIT 0, 30;';

			$req = $readydb->prepare($query);
			$req->execute();

			while (list($id, $topicid, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
				$login = getUserLoginById($userid, $readydb);

				?>
					<div class="panel panel-info" style="margin: 20px;">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-1">
									<?php
										$query = 'SELECT MD5(LOWER(TRIM(`mail`))) FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

										$r = $readydb->prepare($query);
										$r->bindParam(':userid', $userid);
										$r->execute();

										while (list($mail) = $r->fetch(PDO::FETCH_NUM)) {
									?>
										<img style="margin-right: 5px;" src="<?php echo 'https://secure.gravatar.com/avatar/' . $mail . '.jpg?s=25';?>" alt="<?php echo $login; ?>">
									<?php
										break;
										}
									?>
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
								<div class="col-md-3">
									<a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo $login; ?></a>
								</div>
								<div class="col-md-2"><?php echo $created; ?></div>
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

<?php include_once('footer.php'); ?>

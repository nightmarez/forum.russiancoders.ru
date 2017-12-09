<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<h3 class="panel-title">Трекер</h3>
	</div>
	<script>
		document.title = 'Трекер';
	</script>

	<div class="panel-body">
		<?php
			$query = 
				'SELECT `id`, `topicid`, `userid`, `content`, `created` 
				FROM `posts` 
				ORDER BY `id` 
				DESC LIMIT 0, 30;';

			$req = $readydb->prepare($query);
			$req->execute();

			while (list($id, $topicid, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
				$login = getUserLoginById($userid, $readydb);

				?>
					<div class="panel panel-info">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-4">
									<img src="<?php echo getGravatarLink($userid, 25, $readydb); ?>" alt="<?php echo $login; ?>" style="float: left; margin-right: 10px; margin-top: -2px;">
									<a href="/user/<?php echo htmlspecialchars($userid); ?>/" style="float: left;" title="Пользователь <?php echo $login; ?>" rel="author"><?php echo $login; ?></a>
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
						<div class="panel-body" style="max-height: 300px; overflow: hidden; position: relative;">
							<div style="position: absolute; top: 250px; left: 0; height: 50px; width: 100%; background: linear-gradient(to bottom, rgba(255,255,255,0) 0%,rgba(188,232,241,1) 100%); z-index: 1;">
							</div>
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

<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<h3 class="panel-title">RussianCoders</h3>
	</div>
	<script>
		document.title = 'RussianCoders';
	</script>

	<div class="panel-body">
		<?php
			// TODO: сделать пагинацию

			$req = $readydb->prepare('SET sql_mode = "";');
			$req->execute();

			$query = 
				'SELECT `g`.`id`, `g`.`topicid`, `g`.`userid`, `g`.`created` FROM 
				 (SELECT `t`.`id`, `t`.`topicid`, `t`.`userid`, `t`.`created` 
				 FROM (SELECT `id`, `topicid`, `userid`, `created` FROM `posts` ORDER BY `id` DESC) AS `t` 
				 GROUP BY `t`.`topicid` 
				 ORDER BY `t`.`id` DESC) AS `g` 
				 JOIN `topics` 
				 ON `g`.`topicid` = `topics`.`topicid` 
				 ORDER BY `topics`.`updated` DESC LIMIT 0, 30;';

			$req = $readydb->prepare($query);
			$req->execute();

			while (list($id, $topicid, $userid, $created) = $req->fetch(PDO::FETCH_NUM)) {
				$userid = getUserIdByPost(getLastPostIdInTopic($topicid, $readydb), $readydb);
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
										$postnumber = calcPostsInTopic($topicid, $readydb) - 1;
										$page = getPostPageNumber($topicid, getLastPostIdInTopic($topicid, $readydb), $readydb);
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
									<a href="/post/<?php echo htmlspecialchars($userid); ?>/<?php echo $topicid; ?>/<?php echo $postnumber; ?>/" style="width: 10px; height: 10px; font-size: 13px; padding-top: 2px; margin-left: 5px;"><i class="glyphicon glyphicon-edit"></i></a>
								</div>
								<div class="col-md-2" style="text-align: right;"><?php echo getPostDate(getLastPostIdInTopic($topicid, $readydb), $readydb); ?></div>
							</div>
						</div>
						<div class="panel-body" style="max-height: 800px; overflow: hidden; position: relative;">
							<div class="tracker-gradient" style="position: absolute; bottom: 0; left: 0; height: 50px; width: 100%; background: linear-gradient(to bottom, rgba(255,255,255,0) 0%,rgba(188,232,241,1) 100%); z-index: 1;">
							</div>
							<div class="row">
								<div class="col-md-12">
									<?php
										$query = 
											'SELECT `content` 
											 FROM `posts` 
											 WHERE `topicid`=:topicid 
											 ORDER BY `id` DESC;';

										$r = $readydb->prepare($query);
										$r->bindParam(':topicid', $topicid);
										$r->execute();

										while (list($content) = $r->fetch(PDO::FETCH_NUM)) {
											echo filterMessage($content, $userid);
											break;
										}
									?>
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

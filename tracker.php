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
		<div class="table-responsive">
			<?php
				$pdo = new PdoDb();

				$query =
					'SELECT `topicid`, `userid`, `content`, `created` FROM `posts` ORDER BY `id` DESC LIMIT 0, 30;';

				$req = $pdo->prepare($query);
				$req->execute();

				while (list($topicid, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
					$login = getUserLoginById($userid, $pdo);

					?>
						<table class="table tracker-posts">
							<tbody>
								<tr>
									<td>
										<?php
											$query =
												'SELECT MD5(LOWER(TRIM(`mail`))) FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

											$r = $pdo->prepare($query);
											$r->bindParam(':userid', $userid);
											$r->execute();

											while (list($mail) = $r->fetch(PDO::FETCH_NUM)) {
										?>
											<img style="margin-right: 5px;" src="<?php echo 'https://secure.gravatar.com/avatar/' . $mail . '.jpg?s=25';?>" alt="<?php echo $login; ?>">
										<?php
											break;
											}
										?>
									</td>
									<td>
										<?php
											$topicTitle = getTopicTitleById($topicid, $pdo);
											$sectionid = getSectionIdByTopicId($topicid, $pdo);
											$sectionTitle = getSectionTitleById($sectionid, $pdo);
											$postnumber = calcPostsInTopic($topicid, $pdo) - 1;
											$page = topicPagesCount($topicid, $pdo);
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
										<a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo $login; ?></a>
									</td>
									<td><?php
											echo $created;
										?></td>
								</tr>
								<tr>
									<td colspan="4"><?php echo filterMessage($content, $userid); ?></td>
								</tr>
							</tbody>
						</table>
					<?php
				}
			?>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
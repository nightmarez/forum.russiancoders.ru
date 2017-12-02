<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<!--
<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Разделы</h3>
	</div>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table index-topics">
				<thead>
					<tr>
						<th>Раздел</th>
						<th>Темы</th>
						<th>Сообщения</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$query =
							'SELECT `sectionid`, `title` 
							FROM `sections` 
							ORDER BY `id`;';

						$req = $readydb->prepare($query);
						$req->execute();

						while (list($sectionid, $title) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<tr>
									<td>
										<a href="/section/<?php echo htmlspecialchars($sectionid); ?>/"><?php echo htmlspecialchars($title); ?></a>
									</td>
									<td>
										<?php echo intval(calcTopicsInSection($sectionid, $readydb)); ?>
									</td>
									<td>
										<?php echo intval(calcPostsInSection($sectionid, $readydb)); ?>
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
-->

	<?php
		$query =
			'SELECT `sectionid`, `title` 
			FROM `sections` 
			ORDER BY `id`;';

		$req = $readydb->prepare($query);
		$req->execute();

		while (list($sectionid, $title) = $req->fetch(PDO::FETCH_NUM)) {
	?>
		<div class="panel panel-primary" style="margin: 20px;">
			<div class="panel-heading">
				<h3 class="panel-title"><a href="/section/<?php echo htmlspecialchars($sectionid); ?>/"><?php echo htmlspecialchars($title); ?></a></h3>
			</div>

			<div class="panel-body">
				<div class="table-responsive">
					<table class="table index-posts">
						<thead>
							<tr>
								<th>Тема</th>
								<th>Автор</th>
								<th>Последний</th>
								<th>Ответы</th>
								<th>Обновление</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$query =
									'SELECT `topicid`, `title`, `userid`, `updated` 
									FROM `topics` 
									WHERE `sectionid`=:sectionid 
									ORDER BY `updated` 
									DESC LIMIT 0, 10;';

								$r = $readydb->prepare($query);
								$r->bindParam(':sectionid', $sectionid);
								$r->execute();
								
								while (list($topicid, $title, $userid, $updated) = $r->fetch(PDO::FETCH_NUM)) {
									?>
										<tr>
											<td>
												<?php
													$icon = false;

													if (isTopicClosed($topicid, $readydb)) {
														$icon = 'closed.png';
													}

													if ($icon !== false) {
												?>
													<div style="width: 16px; height: 16px; float: left; background-position: center center; background-repeat: no-repeat; background-image: url('https://storage.russiancoders.ru/icons/<?php echo $icon; ?>');"></div>
												<?php
													}
												?>
												<a href="/topic/<?php echo htmlspecialchars($topicid); ?>/"><?php echo htmlspecialchars($title); ?></a>
											</td>
											<td>
												<?php
													$query =
														'SELECT `login` 
														FROM `users` 
														WHERE `userid`=:userid 
														LIMIT 0, 1;';

													$rr = $readydb->prepare($query);
													$rr->bindParam(':userid', $userid);
													$rr->execute();

													while (list($login) = $rr->fetch(PDO::FETCH_NUM)) {
														?>
															<a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo htmlspecialchars($login); ?></a>
														<?php
														break;
													}
												?>
											</td>
											<td>
												<?php
													$query =
														'SELECT `userid` 
														FROM `posts` 
														WHERE `topicid`=:topicid 
														ORDER BY `id` 
														DESC LIMIT 0, 1;';

													$rr = $readydb->prepare($query);
													$rr->bindParam(':topicid', $topicid);
													$rr->execute();

													while (list($userid2) = $rr->fetch(PDO::FETCH_NUM)) {
														$query2 =
															'SELECT `login` 
															FROM `users` 
															WHERE `userid`=:userid 
															LIMIT 0, 1;';

														$rrr = $readydb->prepare($query2);
														$rrr->bindParam(':userid', $userid2);
														$rrr->execute();

														while (list($login2) = $rrr->fetch(PDO::FETCH_NUM)) {
															?>
																<a href="/user/<?php echo htmlspecialchars($userid2); ?>/"><?php echo htmlspecialchars($login2); ?></a>
															<?php
															break;
														}

														break;
													}
												?>
											</td>
											<td><?php echo intval(calcPostsInTopic($topicid, $readydb)); ?></td>
											<td>
												<?php echo $updated; ?>
											</td>
										</tr>
									<?php
								}
							?>
						</tbody>
					</table>
				</div>
			</div>

			<div style="margin: 0 0 10px 10px;">
				<form method="GET" action="/createtopic.php">
					<input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>">
					<input type="submit" class="btn btn-primary" value="Создать тему">
				</form>
			</div>
		</div>
	<?php
		}
	?>

<?php include_once('footer.php'); ?>
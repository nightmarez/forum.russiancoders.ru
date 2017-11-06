<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Разделы</h3>
	</div>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Раздел</th>
						<th>Темы</th>
						<th>Сообщения</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$db = new PdoDb();

						$query =
							'SELECT `sectionid`, `title` FROM `sections` ORDER BY `id`;';

						$req = $db->prepare($query);
						$req->execute();

						while (list($sectionid, $title) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<tr>
									<td>
										<a href="/section.php?sectionid=<?php echo htmlspecialchars($sectionid); ?>"><?php echo htmlspecialchars($title); ?></a>
									</td>
									<td>
										<?php
											$pdo = new PdoDb();

											$query =
												'SELECT `id` FROM `topics` WHERE `sectionid`=:sectionid;';

											$r = $pdo->prepare($query);
											$r->bindParam(':sectionid', $sectionid);
											$r->execute();
											$count = $r->fetchColumn();
											echo intval($count);
										?>
									</td>
									<td>
										?
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

	<?php
		$db = new PdoDb();

		$query =
			'SELECT `sectionid`, `title` FROM `sections` ORDER BY `id`;';

		$req = $db->prepare($query);
		$req->execute();

		while (list($sectionid, $title) = $req->fetch(PDO::FETCH_NUM)) {
	?>
		<div class="panel panel-primary" style="margin: 20px;">
			<div class="panel-heading">
				<h3 class="panel-title"><a href="/section.php?sectionid=<?php echo htmlspecialchars($sectionid); ?>"><?php echo htmlspecialchars($title); ?></a></h3>
			</div>

			<div class="panel-body">
				<div class="table-responsive">
					<table class="table">
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
								$pdo = new PdoDb();

								$query =
									'SELECT `topicid`, `title`, `userid`, `updated` FROM `topics` WHERE `sectionid`=:sectionid ORDER BY `updated` DESC LIMIT 0, 10;';

								$r = $pdo->prepare($query);
								$r->bindParam(':sectionid', $sectionid);
								$r->execute();
								
								while (list($topicid, $title, $userid, $updated) = $r->fetch(PDO::FETCH_NUM)) {
									?>
										<tr>
											<td style="width: 20%;">
												<a href="/topic.php?topicid=<?php echo htmlspecialchars($topicid); ?>"><?php echo htmlspecialchars($title); ?></a>
											</td>
											<td style="width: 20%;">
												<?php
													$p = new PdoDb();

													$query =
														'SELECT `login` FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

													$rr = $p->prepare($query);
													$rr->bindParam(':userid', $userid);
													$rr->execute();

													while (list($login) = $rr->fetch(PDO::FETCH_NUM)) {
														?>
															<a href="/user.php?userid=<?php echo htmlspecialchars($userid); ?>"><?php echo htmlspecialchars($login); ?></a>
														<?php
														break;
													}
												?>
											</td>
											<td style="width: 20%;">
												?
											</td>
											<td style="width: 20%;">
												?
											</td>
											<td style="width: 20%;">
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
		</div>
	<?php
		}
	?>

<?php include_once('footer.php'); ?>
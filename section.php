<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$sectionid = false;

	if (isset($_GET['sectionid'])) {
		$sectionid = htmlspecialchars($_GET['sectionid']);

		if (!preg_match('/^\{?[0-9a-zA-Z]{1,20}\}?$/', $sectionid)) {
			$sectionid = false;
		}
	}
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Раздел</h3>
	</div>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<?php if ($sectionid === false) { ?>
						<tr>
							<td>
								<p>Раздел с указанным идентификатором не найден</p>
							</td>
						</tr>
					<?php } else { ?>
						<?php
							$db = new PdoDb();

							$query =
								'SELECT `title` FROM `sections` WHERE `sectionid`=:sectionid LIMIT 0, 1;';

							$req = $db->prepare($query);
							$req->bindParam(':sectionid', $sectionid);
							$req->execute();

							while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
						?>
							<tr>
								<td>
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
															'SELECT `topicid`, `title`, `userid`, `updated` FROM `topics` WHERE `sectionid`=:sectionid ORDER BY `updated` DESC LIMIT 0, 20;';

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
								</td>
							</tr>
						<?php
							}
						?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
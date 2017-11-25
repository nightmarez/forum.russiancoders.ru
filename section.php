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
		<h3 class="panel-title"><?php
			$query =
				'SELECT `title` 
				FROM `sections` 
				WHERE `sectionid`=:sectionid 
				LIMIT 0, 1;';

			$req = $readydb->prepare($query);
			$req->bindParam(':sectionid', $sectionid);
			$req->execute();

			while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
				echo htmlspecialchars($title);
				break;
			}
		?></h3>
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
							$query =
								'SELECT `title` 
								FROM `sections` 
								WHERE `sectionid`=:sectionid 
								LIMIT 0, 1;';

							$req = $readydb->prepare($query);
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
														$query =
															'SELECT `topicid`, `title`, `userid`, `updated` 
															FROM `topics` 
															WHERE `sectionid`=:sectionid 
															ORDER BY `updated` 
															DESC LIMIT 0, 20;';

														$r = $readydb->prepare($query);
														$r->bindParam(':sectionid', $sectionid);
														$r->execute();
														
														while (list($topicid, $title, $userid, $updated) = $r->fetch(PDO::FETCH_NUM)) {
															?>
																<tr>
																	<td style="width: 20%;">
																		<a href="/topic/<?php echo htmlspecialchars($topicid); ?>/"><?php echo htmlspecialchars($title); ?></a>
																	</td>
																	<td style="width: 20%;">
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

			<div>
				<form method="GET" action="/createtopic.php">
					<input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>">
					<input type="submit" class="btn btn-primary" value="Создать тему">
				</form>
			</div>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
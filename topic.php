<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$topicid = htmlspecialchars($_GET['topicid']);
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">
			<?php
				$db = new PdoDb();

				$query =
					'SELECT `title` FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

				$req = $db->prepare($query);
				$req->bindParam(':topicid', $topicid);
				$req->execute();

				while (list($title) = $req->fetch(PDO::FETCH_NUM)) {
					echo $title;
					break;
				}
			?>
		</h3>
	</div>

	<div class="panel-body">
		<div class="table-responsive">
					<?php
						$db = new PdoDb();

						$query =
							'SELECT `id`, `topicid`, `userid`, `content`, `created` FROM `posts` WHERE `topicid`=:topicid ORDER BY `id` ASC LIMIT 0, 100;';

						$req = $db->prepare($query);
						$req->execute();

						while (list($id, $topicid, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<table class="table">
									<tbody>
										<tr>
											<td>
												#
											</td>
											<td>
												<?php
													$pdo = new PdoDb();

													$query =
														'SELECT `login` FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

													$r = $pdo->prepare($query);
													$r->bindParam(':userid', $userid);
													$r->execute();

													while (list($login) = $req->fetch(PDO::FETCH_NUM)) {
														echo htmlspecialchars($login);
														break;
													}
												?>
											</td>
											<td>
												<?php
													echo $created;
												?>
											</td>
										</tr>
										<tr>
											<td colspan="3">
												<?php echo htmlspecialchars($content); ?>
											</td>
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
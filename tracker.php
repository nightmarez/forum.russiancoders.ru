<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$topicid = htmlspecialchars($_GET['topicid']);
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">
			Трекер
		</h3>
	</div>

	<div class="panel-body">
		<div class="table-responsive">
			<?php
				$db = new PdoDb();

				$query =
					'SELECT `topicid`, `userid`, `content`, `created` FROM `posts` ORDER BY `id` DESC LIMIT 0, 30;';

				$req = $db->prepare($query);
				$req->execute();

				while (list($topicid, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
					?>
						<table class="table topic-posts">
							<tbody>
								<tr>
									<td>
										<?php
											$pdo = new PdoDb();

											$query =
												'SELECT `title` FROM `topics` WHERE `topicid`=:topicid LIMIT 0, 1;';

											$r = $pdo->prepare($query);
											$r->bindParam(':topicid', $topicid);
											$r->execute();

											while (list($title) = $r->fetch(PDO::FETCH_NUM)) {
												?><a href="/user.php?topicid=<?php echo htmlspecialchars($topicid); ?>"><?php echo htmlspecialchars($title); ?></a><?php
												break;
											}
										?>
									</td>
									<td><?php
											$pdo = new PdoDb();

											$query =
												'SELECT `login` FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

											$r = $pdo->prepare($query);
											$r->bindParam(':userid', $userid);
											$r->execute();

											while (list($login) = $r->fetch(PDO::FETCH_NUM)) {
												?><a href="/user.php?userid=<?php echo htmlspecialchars($userid); ?>"><?php echo htmlspecialchars($login); ?></a><?php
												break;
											}
										?></td>
									<td><?php
											echo $created;
										?></td>
								</tr>
								<tr>
									<td colspan="3"><?php echo filterMessage($content); ?></td>
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
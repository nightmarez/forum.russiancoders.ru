<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$userid = false;

	if (isset($_GET['userid'])) {
		$userid = htmlspecialchars($_GET['userid']);

		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
			$userid = false;
		}
	}
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">
			Сообщения пользователя <?php echo getUserLoginById($userid); ?>
		</h3>
	</div>
	<script>
		document.title = 'Сообщения пользователя';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			<?php
				$db = new PdoDb();

				$query =
					'SELECT `topicid`, `userid`, `content`, `created` FROM `posts` WHERE `userid`=:userid ORDER BY `id` DESC LIMIT 0, 30;';

				$req = $db->prepare($query);
				$req->bindParam(':userid', $userid);
				$req->execute();

				while (list($topicid, $userid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
					?>
						<table class="table tracker-posts">
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
												?><a href="/topic.php?topicid=<?php echo htmlspecialchars($topicid); ?>"><?php echo htmlspecialchars($title); ?></a><?php
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
									<td colspan="3"><?php echo filterMessage($content, $userid); ?></td>
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
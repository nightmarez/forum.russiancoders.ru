<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">
			Поиск
		</h3>
	</div>
	<script>
		document.title = 'Поиск';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			<?php
				if (!isset($_POST['search'])) {
					die();
				}

				$search = $_POST['search'];

				$db = new PdoDb();

				$query =
					'SELECT `id`, `topicid`, `userid`, `content`, `created` FROM `posts` WHERE MATCH (`content`) AGAINST (:search) LIMIT 0, 20;';

				$req = $db->prepare($query);
				$req->bindParam(':search', $search);
				$req->execute();

				while (list($id, $topicid, $content, $created) = $req->fetch(PDO::FETCH_NUM)) {
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
												?><a href="/topic/<?php echo htmlspecialchars($topicid); ?>/"><?php echo htmlspecialchars($title); ?></a><?php
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
												?><a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo htmlspecialchars($login); ?></a><?php
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
<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Профиль</h3>
	</div>
	<script>
		document.title = 'Профиль';
	</script>

	<?php
		$userid = false;

		if (isset($_COOKIE['userid'])) {
			$userid = htmlspecialchars($_COOKIE['userid']);

			if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
				$userid = false;
			}
		}
	?>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<?php if ($userid === false) { ?>
						<tr>
							<td>
								<p>Пользователь с указанным идентификатором не найден</p>
							</td>
						</tr>
					<?php } else { ?>
						<?php
							$db = new PdoDb();

							$query =
								'SELECT `login`, `last` FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

							$req = $db->prepare($query);
							$req->bindParam(':userid', $userid);
							$req->execute();

							while (list($login, $last) = $req->fetch(PDO::FETCH_NUM)) {
						?>
							<tr>
								<td colspan="2">
									<h3><?php echo htmlspecialchars($login); ?><h3>
								</td>
							</tr>
							<tr>
								<td>
									Последнее посещение:
								</td>
								<td>
									<?php echo $last; ?>
								</td>
							</tr>
							<tr>
								<td>
									Ваши темы с новыми сообщениями:
								</td>
								<td>
									<?php
										$topics = getSelfTopicsWithNewMessage($userid);

										if ($topics !== false) {
											foreach ($topics as $key => $topic) {
												?>
													<a href="/topic/<?php echo $topic['topicid']; ?>/"><?php echo htmlspecialchars($topic['title']); ?></a><br>
												<?php
											}
										} else {
											echo 'нет';
										}
									?>
								</td>
							</tr>
							<tr>
								<td>
									Рейтинг:
								</td>
								<td>
									<?php
										$pdo = new PdoDb();

										$query =
											'SELECT SUM(`t1`.`value`) FROM
											(SELECT `likes`.`value`, `posts`.`userid`
											FROM `likes`
											LEFT JOIN `posts` ON `likes`.`postid` = `posts`.`id`) AS `t1`
											WHERE `t1`.`userid` = :userid;';

										$r = $db->prepare($query);
										$r->bindParam(':userid', $userid);
										$r->execute();
										$sum = $r->fetchColumn();
										echo intval($sum);
									?>
								</td>
							</tr>
						<?php
								break;
							}
						?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
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
		<h3 class="panel-title">Профиль пользователя</h3>
	</div>

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

							while (list($login, $last, $mail) = $req->fetch(PDO::FETCH_NUM)) {
						?>
							<tr>
								<td colspan="2">
									<img style="margin-right: 15px;" src="<?php echo 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(mb_convert_encoding($mail, 'ISO-8859-1','utf-8')))) . '.jpg?s=200';?>" align="left">
									<h3>
										<?php echo htmlspecialchars($login); ?>
									<h3>
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
									Темы пользователя:
								</td>
								<td>
									<?php
										$pdo = new PdoDb();

										$query =
											'SELECT COUNT(*) FROM `topics` WHERE `userid`=:userid LIMIT 0, 1;';

										$r = $db->prepare($query);
										$r->bindParam(':userid', $userid);
										$r->execute();
										$count = $r->fetchColumn();

										?>
											<a href="/topics/<?php echo $userid;?>/"><?php echo intval($count); ?></a>
										<?php
									?>
								</td>
							</tr>
							<tr>
								<td>
									Сообщения пользователя:
								</td>
								<td>
									<?php
										$pdo = new PdoDb();

										$query =
											'SELECT COUNT(*) FROM `posts` WHERE `userid`=:userid LIMIT 0, 1;';

										$r = $db->prepare($query);
										$r->bindParam(':userid', $userid);
										$r->execute();
										$count = $r->fetchColumn();

										?>
											<a href="/posts/<?php echo $userid;?>/"><?php echo intval($count); ?></a>
										<?php
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
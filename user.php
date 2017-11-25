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
							$query =
								'SELECT `login`, `last`, MD5(LOWER(TRIM(`mail`))) 
								FROM `users` 
								WHERE `userid`=:userid 
								LIMIT 0, 1;';

							$req = $readydb->prepare($query);
							$req->bindParam(':userid', $userid);
							$req->execute();

							while (list($login, $last, $mail) = $req->fetch(PDO::FETCH_NUM)) {
						?>
							<tr>
								<td colspan="2">
									<img style="margin-right: 15px;" src="<?php echo 'https://secure.gravatar.com/avatar/' . $mail . '.jpg?s=200';?>" align="left">
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
										$query =
											'SELECT COUNT(*) 
											FROM `topics` 
											WHERE `userid`=:userid 
											LIMIT 0, 1;';

										$r = $readydb->prepare($query);
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
										$query =
											'SELECT COUNT(*) 
											FROM `posts` 
											WHERE `userid`=:userid 
											LIMIT 0, 1;';

										$r = $readydb->prepare($query);
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
										$query =
											'SELECT SUM(`t1`.`value`) FROM
											(SELECT `likes`.`value`, `posts`.`userid`
											FROM `likes`
											LEFT JOIN `posts` ON `likes`.`postid` = `posts`.`id`) AS `t1`
											WHERE `t1`.`userid` = :userid;';

										$r = $readydb->prepare($query);
										$r->bindParam(':userid', $userid);
										$r->execute();
										$sum = $r->fetchColumn();
										echo intval($sum);
									?>
								</td>
							</tr>
							<tr>
								<td>
									Друзья:
								</td>
								<td>
									<?php
										$friends = getFriendsById($userid, $readydb);

										if (count($friends) == 0) {
											echo 'Нет друзей';
										} else {
											foreach ($friends as $key => $friendid) {
												$login = getUserLoginById($friendid, $readydb);

												?>
													<div style="width: 100%; display: block;">
														<img src="<?php echo getGravatarLink($friendid, 25, $readydb); ?>" alt="<?php echo $login; ?>" style="float: left; margin-right: 10px; margin-top: -2px;">
														<a href="/user/<?php echo htmlspecialchars($friendid); ?>/" style="float: left;" title="Пользователь <?php echo $login; ?>" rel="author"><?php echo $login; ?></a>
													</div>
												<?php
											}
										}
									?>
								</td>
							</tr>
							<tr>
								<td>
									Взаимные друзья:
								</td>
								<td>
									<?php
										if (count($friends) == 0) {
											echo 'Нет взаимных друзей';
										} else {
											foreach ($friends as $key => $friendid) {
												if (isFriend($friendid, $userid, $readydb)) {
													$login = getUserLoginById($friendid, $readydb);

													?>
														<div style="width: 100%; display: block;">
															<img src="<?php echo getGravatarLink($friendid, 25, $readydb); ?>" alt="<?php echo $login; ?>" style="float: left; margin-right: 10px; margin-top: -2px;">
															<a href="/user/<?php echo htmlspecialchars($friendid); ?>/" style="float: left;" title="Пользователь <?php echo $login; ?>" rel="author"><?php echo $login; ?></a>
														</div>
													<?php
												}
											}
										}
									?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<form method="GET" action="/sendmessage/<?php echo $userid; ?>/" style="float: left;">
										<input type="submit" class="btn btn-primary" value="Написать сообщение">
									</form>
									<?php
										if ($yourid !== $userid && isLogin()) {
											$yourid = htmlspecialchars($_COOKIE['userid']);

											if (!isFriend($yourid, $userid)) {
												?>
													<form method="GET" action="/addfriend/<?php echo $userid; ?>/" style="float: left; margin-left: 10px;">
														<input type="submit" class="btn btn-success" value="Добавить в друзья">
													</form>
												<?php
											} else {
												?>
													<form method="GET" action="/removefriend/<?php echo $userid; ?>/" style="float: left; margin-left: 10px;">
														<input type="submit" class="btn btn-danger" value="Удалить из друзей">
													</form>
												<?php
											}
										}
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
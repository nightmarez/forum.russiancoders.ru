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
								<td style="width: 50%;">
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
									<a href="/rating/<?php echo $userid; ?>/all/">
										<span style="color: #000000">
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
										</span>
									</a>
									(<a href="/rating/<?php echo $userid; ?>/positive/"><span style="color: #00aa00">
									<?php
										$query =
											'SELECT SUM(`t1`.`value`) FROM
											(SELECT `likes`.`value`, `posts`.`userid`
											FROM `likes`
											LEFT JOIN `posts` ON `likes`.`postid` = `posts`.`id`) AS `t1`
											WHERE `t1`.`userid` = :userid AND `t1`.`value` > 0;';

										$r = $readydb->prepare($query);
										$r->bindParam(':userid', $userid);
										$r->execute();
										$sum = $r->fetchColumn();
										echo '+' . intval($sum);
									?>
									</span></a> <a href="/rating/<?php echo $userid; ?>/negative/"><span style="color: #aa0000">
									<?php
										$query =
											'SELECT SUM(`t1`.`value`) FROM
											(SELECT `likes`.`value`, `posts`.`userid`
											FROM `likes`
											LEFT JOIN `posts` ON `likes`.`postid` = `posts`.`id`) AS `t1`
											WHERE `t1`.`userid` = :userid AND `t1`.`value` < 0;';

										$r = $readydb->prepare($query);
										$r->bindParam(':userid', $userid);
										$r->execute();
										$sum = $r->fetchColumn();
										echo '-' . abs(intval($sum));
									?>
									</span></a>)
								</td>
							</tr>
							<tr>
								<td>
									Оценки:
								</td>
								<td>
									<a href="/votes/<?php echo $userid; ?>/all/">
										<span style="color: #000000">
											<?php
												$query =
													'SELECT SUM(`value`)
													FROM `likes`
													WHERE `userid`=:userid;';

												$r = $readydb->prepare($query);
												$r->bindParam(':userid', $userid);
												$r->execute();
												$sum = $r->fetchColumn();
												echo intval($sum);
											?>
										</span>
									</a>
									(<a href="/votes/<?php echo $userid; ?>/positive/"><span style="color: #00aa00">
									<?php
										$query =
											'SELECT SUM(`value`)
											FROM `likes`
											WHERE `userid`=:userid AND `value` > 0;';

										$r = $readydb->prepare($query);
										$r->bindParam(':userid', $userid);
										$r->execute();
										$sum = $r->fetchColumn();
										echo '+' . intval($sum);
									?>
									</span></a> <a href="/votes/<?php echo $userid; ?>/negative/"><span style="color: #aa0000">
									<?php
										$query =
											'SELECT SUM(`value`)
											FROM `likes`
											WHERE `userid`=:userid AND `value` < 0;';

										$r = $readydb->prepare($query);
										$r->bindParam(':userid', $userid);
										$r->execute();
										$sum = $r->fetchColumn();
										echo '-' . abs(intval($sum));
									?>
									</span></a>)
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
												if (!isFriend($friendid, $userid, $readydb)) {
													$login = getUserLoginById($friendid, $readydb);

													?>
														<div style="float: left; margin-right: 20px;">
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
								<td>
									Подписчики:
								</td>
								<td>
									<?php
										$fans = getFansById($userid, $readydb);

										if (count($fans) == 0) {
											echo 'Нет';
										} else {
											foreach ($fans as $key => $fanid) {
												$login = getUserLoginById($fanid, $readydb);

												?>
													<div style="float: left; margin-right: 20px;">
														<img src="<?php echo getGravatarLink($fanid, 25, $readydb); ?>" alt="<?php echo $login; ?>" style="float: left; margin-right: 10px; margin-top: -2px;">
														<a href="/user/<?php echo htmlspecialchars($fanid); ?>/" style="float: left;" title="Пользователь <?php echo $login; ?>" rel="author"><?php echo $login; ?></a>
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
														<div style="float: left; margin-right: 20px;">
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
								<td>
									Награды:
								</td>
								<td>
									<ul>
										<li>
											<?php
												$rewards = getRewards($userid, $readydb);

												foreach ($variable as $key => $value) {
													?>
														<img src="https://storage.russiancoders.ru/rewards/<?php echo $value; ?>.jpg" alt="<?php echo $value; ?>" style="float: left; margin-right: 10px; width: 64px; height: 64px;">
													<?php
												}
											?>
										</li>
									</ul>
								</td>
							</tr>
							<!--
							<tr>
								<td>
									Личные настройки:
								</td>
								<td>
									<ul>
										<li>
											<?php
												$smilesEnabled = getSettingsParam($userid, 'smiles', $readydb);

												if ($smilesEnabled === false || $smilesEnabled === 'checked') {
													$smilesEnabled = 'checked';
												} else {
													$smilesEnabled = '';
												}
											?>
											<label class="checkbox-inline"><input type="checkbox" <?php echo $smilesEnabled; ?> id="smiles">Включить смайлы</label>
										</li>
									</ul>
								</td>
							</tr>
							-->
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
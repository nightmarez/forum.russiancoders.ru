<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
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

			if (!preg_match('/^\{?[0-9a-zA-Z]{1,30}\}?$/', $userid)) {
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
								'SELECT `login`, `last`, MD5(LOWER(TRIM(`mail`))), `mail` 
								FROM `users` 
								WHERE `userid`=:userid 
								LIMIT 0, 1;';

							$req = $readydb->prepare($query);
							$req->bindParam(':userid', $userid);
							$req->execute();

							while (list($login, $last, $mailmd5, $mail) = $req->fetch(PDO::FETCH_NUM)) {
						?>
							<tr>
								<td colspan="2">
									<?php
										$userAvatar = getAvatar($userid, TRUE);

										if ($userAvatar) {
											?>
												<img style="margin-right: 15px;" src="<?php echo $userAvatar; ?>?stamp=<?php echo time(); ?>" align="left">
											<?php
										} else {
											?>
												<img style="margin-right: 15px;" src="<?php echo 'https://secure.gravatar.com/avatar/' . $mailmd5 . '.jpg?s=200'; ?>" align="left">
											<?php
										}
									?>
									<h3><?php echo getUserTitleById($userid, $readydb); ?></h3>
									<form method="POST" action="/upload-avatar.php" enctype="multipart/form-data">
										<div class="input-group">
											<label for="imgInp" id="imgInpLbl" style="cursor: pointer; color: blue; text-decoration: underline;">Выбрать новый аватар…</label>
											<input type="file" id="imgInp" name="imgInp" style="opacity: 0; position: absolute; z-index: -1;">
											<label for="imgSubmit" id="imgSubmitLbl" style="cursor: pointer; color: blue; text-decoration: underline; display: none;">Загрузить на сервер…</label>
											<input type="submit" id="imgSubmit" style="opacity: 0; position: absolute; z-index: -1;">
										</div>
									</form>
									<script>
										document.addEventListener('DOMContentLoaded', function() {
											document.getElementById('imgInp').addEventListener("change", function() {
												document.getElementById('imgInpLbl').style.display = 'none';
												document.getElementById('imgSubmitLbl').style.display = 'block';
												document.getElementById('imgSubmitLbl').click();
											});
										});
									</script>
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
											$tpcscount = 11;

											foreach ($topics as $key => $topic) {
												if (!--$tpcscount) {
													break;
												}

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
									Загрузил изображений:
								</td>
								<td>
									<?php
										echo getLoadedImagesCount($userid);
										tryAddPhotographerReward($userid);
									?>
								</td>
							</tr>
							<tr>
								<td>
									Последние загрузки:
								</td>
								<td>
									<?php
										$images = getLoadedImages($userid);
										$imgcnt = 8;

										foreach ($images as $image) {
											if (!--$imgcnt) {
												break;
											}

											$image = 'https://russiancoders.club/' . $userid . '/' . basename($image);

											?>
												<a href="<?php echo $image; ?>" target="_blank" type="image/jpeg" title="<?php echo basename($image); ?>">
													<div style="float: left; width: 64px; height: 64px; overflow: hidden; margin: 3px; background-repeat: no-repeat; background-size: contain; background-color: white; border: 1px solid silver; background-position: center center; background-image: url(<?php echo $image; ?>);"></div>
												</a>
											<?php
										}
									?>
								</td>
							</tr>
							<tr>
								<td>
									Подписан на:
								</td>
								<td>
									<div style="margin-top: -5px;">
										<?php
											$friends = getFriendsById($userid, $readydb);

											if (count($friends) == 0) {
												echo 'Ни на кого';
											} else {
												$fcount = 0;

												foreach ($friends as $key => $friendid) {
													if (!isFriend($friendid, $userid, $readydb)) {
														$fcount++;
														$login = getUserLoginById($friendid, $readydb);

														?>
															<div style="float: left; margin-right: 20px; margin-top: 5px;">
																<img src="<?php echo getGravatarLink($friendid, 25, $readydb); ?>" alt="<?php echo $login; ?>" style="float: left; margin-right: 10px; margin-top: -2px;">
																<a href="/user/<?php echo htmlspecialchars($friendid); ?>/" style="float: left;" title="Пользователь <?php echo $login; ?>" rel="author"><?php echo $login; ?></a>
															</div>
														<?php
													}
												}

												if ($fcount == 0) {
													echo 'Ни на кого';
												}
											}
										?>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									Подписчики:
								</td>
								<td>
									<div style="margin-top: -5px;">
										<?php
											$fans = getFansById($userid, $readydb);

											if (count($fans) == 0) {
												echo 'Нет подписчиков';
											} else {
												foreach ($fans as $key => $fanid) {
													$login = getUserLoginById($fanid, $readydb);

													?>
														<div style="float: left; margin-right: 20px; margin-top: 5px;">
															<img src="<?php echo getGravatarLink($fanid, 25, $readydb); ?>" alt="<?php echo $login; ?>" style="float: left; margin-right: 10px; margin-top: -2px;">
															<a href="/user/<?php echo htmlspecialchars($fanid); ?>/" style="float: left;" title="Пользователь <?php echo $login; ?>" rel="author"><?php echo $login; ?></a>
														</div>
													<?php
												}
											}
										?>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									Взаимные друзья:
								</td>
								<td>
									<div style="margin-top: -5px;">
										<?php
											if (count($friends) == 0) {
												echo 'Нет взаимных друзей';
											} else {
												foreach ($friends as $key => $friendid) {
													if (isFriend($friendid, $userid, $readydb)) {
														$login = getUserLoginById($friendid, $readydb);

														?>
															<div style="float: left; margin-right: 20px; margin-top: 5px;">
																<img src="<?php echo getGravatarLink($friendid, 25, $readydb); ?>" alt="<?php echo $login; ?>" style="float: left; margin-right: 10px; margin-top: -2px;">
																<a href="/user/<?php echo htmlspecialchars($friendid); ?>/" style="float: left;" title="Пользователь <?php echo $login; ?>" rel="author"><?php echo $login; ?></a>
															</div>
														<?php
													}
												}
											}
										?>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									Награды:
								</td>
								<td>
									<div style="margin-left: -16px;">
										<?php
											$rewards = getRewards($userid, $readydb);

											foreach ($rewards as $key => $value) {
												?>
													<div style="background-image: url('https://russiancoders.club/rewards/<?php echo $value; ?>.jpg'); background-repeat: no-repeat; background-position: center center; width: 64px; height: 64px; float: left; margin-right: 10px;" title="<?php echo getRewardInfo($value, $readydb); ?>"></div>
												<?php
											}
										?>
									</div>
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

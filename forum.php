<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

	<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
			<div class="panel-heading">
				<h3 class="panel-title"><a href="/section/<?php echo htmlspecialchars($sectionid); ?>/"><?php echo htmlspecialchars($title); ?></a></h3>
			</div>

			<div class="panel-body">
				<?php
					$query = 'SELECT `sectionid`, `title` FROM `sections` ORDER BY `orderid`;';
					$req = $readydb->prepare($query);
					$req->execute();

					while (list($sectionid, $title) = $req->fetch(PDO::FETCH_NUM)) {
				?>
					<a href="/section/<?php echo htmlspecialchars($sectionid); ?>" style="float: left; margin-right: 20px;"><?php echo htmlspecialchars($title); ?></a>
				<?php } ?>

				<a href="/" style="float: left; margin-right: 20px;">Главная</a>
				<a href="/tracker/" style="float: left; margin-right: 20px;">Трекер</a>
				<a href="/gallery/" style="float: left; margin-right: 20px;">Галерея</a>
				<a href="/faq/" style="float: left; margin-right: 20px;">ЧаВо</a>
				<a href="/donate/" style="float: left; margin-right: 20px;">Донат</a>
			</div>
	</div>

	<!-------------------------------------------------------------------->

	<?php
		$query = 'UPDATE `topics` SET `hot`=0 WHERE TIME_TO_SEC(TIMEDIFF(NOW(), `last`)) > 24 * 60 * 60 * 7);';
		$req = $readydb->prepare($query);
		$req->execute();

		$query =
			'SELECT `sectionid`, `title` 
			 FROM `sections` 
			 ORDER BY `orderid`;';

		$req = $readydb->prepare($query);
		$req->execute();

		while (list($sectionid, $title) = $req->fetch(PDO::FETCH_NUM)) {
	?>
		<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
			<div class="panel-heading">
				<h3 class="panel-title"><a href="/section/<?php echo htmlspecialchars($sectionid); ?>/"><?php echo htmlspecialchars($title); ?></a></h3>
			</div>

			<div class="panel-body">
				<div class="table-responsive">
					<table class="table index-posts">
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
									'SELECT `topicid`, `title`, `userid`, `updated`, `closed`, `pinned`, `hot`, `lastpost` 
									FROM `topics` 
									WHERE `sectionid`=:sectionid 
									ORDER BY `pinned` DESC, `pinned` + `closed` DESC, `hot` > 0 DESC, `updated` DESC
									LIMIT 0, 20;';

								$r = $readydb->prepare($query);
								$r->bindParam(':sectionid', $sectionid, PDO::PARAM_STR);
								$r->execute();

								$counter = 11;
								
								while (list($topicid, $title, $userid, $updated, $closed, $pinned, $hot, $postnumber) = $r->fetch(PDO::FETCH_NUM)) {
									if (!--$counter) {
										break;
									}

									?>
										<tr>
											<td>
												<?php
													if (/* isPinnedTopic($topicid, $readydb) */ $pinned > 0) {
														++$counter;
												?>
													<div style="margin-right: 5px; width: 16px; height: 16px; float: left; background-position: center center; background-repeat: no-repeat; background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4QwDBxEzL8906wAAAR1JREFUOMtjYCASPPexYX3uY8NAFnjuYyP/3MfmHpSNIsdEpBn2DAwMis99bC5LbjnCcNHRiHgDnvvYuDIwMCxkYGBgYGRk1LnsZHxFf/85hgOW2gwMDAwMjERo3gXVzPD66zeGN99+MDAyMlxxOH5V94ClNm4D8GiGgQcMDAyqLGia1BkYGNYzMDD8ZmBg0MOjOYyBgeGww/Grfxix2JzLwMAwCY9mG4fjV4/COFi98MLDIpORiWnaqx8/8WrGGQs32mZznDKyY3j79RtezVgNOHT8dMmdG1f7jgiKpzExMGRDhc9g08yATfOcZSv+V3d0ZsDEDlhq5x6w1L5AhOYz+eiakQxRJmjA3GUr/1V3dKaSlVmmLVzMxjBQAAAKYnxfnVVxpAAAAABJRU5ErkJggg==');" title="Прикреплённая тема"></div>
												<?php
													}
												?>
												
												<?php
													if (/* isTopicClosed($topicid, $readydb) */ $closed > 0) {
												?>
													<div style="margin-right: 5px; width: 16px; height: 16px; float: left; background-position: center center; background-repeat: no-repeat; background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAQCAYAAAAmlE46AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7DAAAOwwHHb6hkAAAAB3RJTUUH4QwCDQkOQJdCEAAAAcxJREFUKM+Vkc1LlFEUxn/n3juvzpSMUhQpFbaYkqB2UkTYShclYUTU0CroX2gTIURraV0RtOgDJCKo6GMVDUJtrLBEqKA29iEODU4zOu+9p8XrhKMvRb/V4fA89znnXGGZvv6DTL96wYEjx/PACVUdVFUxIiXnohvP798uswIBGDx5hqd3rjNw9FTP0lL9WQihT0RmFQKqPSLyybnMcOnB+Pum0QJ8nJoEoHtHYTyEsM9ad9E6d8FYexOYRrWoqvne3Xsff5l5FwO45guHjhV7a9XqfmPsm4lHd0eb/cPFs1Pz5bl+VR2JG41zQA3ANAW/6vF2RSKvTKzc5eGtqz5WU1JkXbVhM392rF3u6IxonJ6pdAy8/N41siVbez209duTJZ+IFJic69zztpwfGt42e2VDtPg5uLZrsjjWft6E+iUjYEyijAMtWAExqPeIAmoy95wQ7wIICsG3nluXa6+AT34AAPXdBvCsQgQaAXxI6hSCS+v+rMGHH8nIhc3QlQXVVo1ZbVKgUk8SBfhaAZOSuiZRgPVtENkkZWNubVqqESDfDjs3Jem5KDlcmlHSjpOL+CtGkXn+G6k4Nbkx1YUCaB4I/3KALASbHf0NdRSy7ZQ3MF8AAAAASUVORK5CYII=');" title="Закрытая тема"></div>
												<?php
													}
												?>

												<?php
													if (/* isHotTopic($topicid, $readydb) */ $hot > 0) {
												?>
													<div style="margin-right: 5px; width: 16px; height: 16px; float: left; background-position: center center; background-repeat: no-repeat; background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAANCAYAAAB/9ZQ7AAABA0lEQVR4AWNAB7c9Vdg/xDjvehbpIMJACLyPdgr8EOPy/32089VXoQ48OBW+DHdSfh/t8gykGKqhEbuJkY72H6KdX4MUwXG0y3WoNCNc4YcoFxegO3/BFMFNjnH5ApF3Knkbbc7HAHSXCtDqjyBJLIofvox0FgfKP30X7ZzFAHTXAogkJgYpAuJHUCftZAASb1EVOH/GphHkHwaQu5AU3n8f4+iH3RbnbyBn7EOyNgkUGUD2X0z3O19keB/uqA9kvAcJvItxtoVEjPNmDMVRLjXgoHsT4WgO8vmHaKdUaFAqgdyIZOq5q6HabPCwfuZrzPU20tkDHklRTvIgJwLxYli0AwDQPva9BmGXugAAAABJRU5ErkJggg==');" title="Горячая тема"></div>
												<?php
													}
												?>

												<a href="/topic/<?php echo htmlspecialchars($topicid); ?>/"><?php echo htmlspecialchars($title); ?></a>

												<?php if ($postnumber > 0) { ?>
													<!--
													<a href="/post/<?php echo htmlspecialchars($userid); ?>/<?php echo $topicid; ?>/<?php echo $postnumber; ?>/" style="width: 10px; height: 10px; font-size: 13px; padding-top: 2px; margin-left: 5px;"><i class="glyphicon glyphicon-edit"></i></a>
													-->
												<?php } ?>
											</td>
											<td>
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
															<a href="/user/<?php echo htmlspecialchars($userid); ?>/" rel="nofollow"><?php echo getUserTitleById($userid, $readydb); ?></a>
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
																<a href="/user/<?php echo htmlspecialchars($userid2); ?>/" rel="nofollow"><?php echo getUserTitleById($userid2, $readydb); ?></a>
															<?php
															break;
														}

														break;
													}
												?>
											</td>
											<td><?php echo intval(calcPostsInTopic($topicid, $readydb)); ?></td>
											<td>
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

			<?php
				if (isLogin()) {
			?>
			<div style="margin: 0 0 10px 10px;">
				<form method="GET" action="/createtopic.php">
					<input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>">
					<input type="submit" class="btn btn-primary" value="Создать тему">
				</form>
			</div>
			<?php
				}
			?>
		</div>
	<?php
		}
	?>

<?php include_once('footer.php'); ?>
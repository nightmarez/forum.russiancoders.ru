<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

	<?php
		$query =
			'SELECT `sectionid`, `title` 
			FROM `sections` 
			ORDER BY `id`;';

		$req = $readydb->prepare($query);
		$req->execute();

		while (list($sectionid, $title) = $req->fetch(PDO::FETCH_NUM)) {
	?>
		<div class="panel panel-primary" style="margin: 20px;">
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
									'SELECT `topicid`, `title`, `userid`, `updated`, (SELECT COUNT(*) FROM `posts` WHERE `topicid` = `topics`.`topicid` AND TIME_TO_SEC(TIMEDIFF(NOW(), `created`)) <= 24 * 60 * 60 * 7) AS `count`
									FROM `topics` 
									WHERE `sectionid`=:sectionid 
									ORDER BY `pinned` DESC, `count` >= 30 DESC, `updated` DESC
									LIMIT 0, 10;';

								$r = $readydb->prepare($query);
								$r->bindParam(':sectionid', $sectionid);
								$r->execute();
								
								while (list($topicid, $title, $userid, $updated) = $r->fetch(PDO::FETCH_NUM)) {
									?>
										<tr>
											<td>
												<?php
													if (isPinnedTopic($topicid, $readydb)) {
												?>
													<div style="width: 16px; height: 16px; float: left; background-position: center center; background-repeat: no-repeat; background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4QwDBxEzL8906wAAAR1JREFUOMtjYCASPPexYX3uY8NAFnjuYyP/3MfmHpSNIsdEpBn2DAwMis99bC5LbjnCcNHRiHgDnvvYuDIwMCxkYGBgYGRk1LnsZHxFf/85hgOW2gwMDAwMjERo3gXVzPD66zeGN99+MDAyMlxxOH5V94ClNm4D8GiGgQcMDAyqLGia1BkYGNYzMDD8ZmBg0MOjOYyBgeGww/Grfxix2JzLwMAwCY9mG4fjV4/COFi98MLDIpORiWnaqx8/8WrGGQs32mZznDKyY3j79RtezVgNOHT8dMmdG1f7jgiKpzExMGRDhc9g08yATfOcZSv+V3d0ZsDEDlhq5x6w1L5AhOYz+eiakQxRJmjA3GUr/1V3dKaSlVmmLVzMxjBQAAAKYnxfnVVxpAAAAABJRU5ErkJggg==');" title="Прикреплённая тема"></div>
												<?php
													}
												?>
												
												<?php
													if (isTopicClosed($topicid, $readydb)) {
												?>
													<div style="width: 16px; height: 16px; float: left; background-position: center center; background-repeat: no-repeat; background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAQCAYAAAAmlE46AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7DAAAOwwHHb6hkAAAAB3RJTUUH4QwCDQkOQJdCEAAAAcxJREFUKM+Vkc1LlFEUxn/n3juvzpSMUhQpFbaYkqB2UkTYShclYUTU0CroX2gTIURraV0RtOgDJCKo6GMVDUJtrLBEqKA29iEODU4zOu+9p8XrhKMvRb/V4fA89znnXGGZvv6DTL96wYEjx/PACVUdVFUxIiXnohvP798uswIBGDx5hqd3rjNw9FTP0lL9WQihT0RmFQKqPSLyybnMcOnB+Pum0QJ8nJoEoHtHYTyEsM9ad9E6d8FYexOYRrWoqvne3Xsff5l5FwO45guHjhV7a9XqfmPsm4lHd0eb/cPFs1Pz5bl+VR2JG41zQA3ANAW/6vF2RSKvTKzc5eGtqz5WU1JkXbVhM392rF3u6IxonJ6pdAy8/N41siVbez209duTJZ+IFJic69zztpwfGt42e2VDtPg5uLZrsjjWft6E+iUjYEyijAMtWAExqPeIAmoy95wQ7wIICsG3nluXa6+AT34AAPXdBvCsQgQaAXxI6hSCS+v+rMGHH8nIhc3QlQXVVo1ZbVKgUk8SBfhaAZOSuiZRgPVtENkkZWNubVqqESDfDjs3Jem5KDlcmlHSjpOL+CtGkXn+G6k4Nbkx1YUCaB4I/3KALASbHf0NdRSy7ZQ3MF8AAAAASUVORK5CYII=');" title="Закрытая тема"></div>
												<?php
													}
												?>

												<?php
													if (isHotTopic($topicid, $readydb)) {
												?>
													<div style="width: 16px; height: 16px; float: left; background-position: center center; background-repeat: no-repeat; background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAANCAYAAAB/9ZQ7AAABA0lEQVR4AWNAB7c9Vdg/xDjvehbpIMJACLyPdgr8EOPy/32089VXoQ48OBW+DHdSfh/t8gykGKqhEbuJkY72H6KdX4MUwXG0y3WoNCNc4YcoFxegO3/BFMFNjnH5ApF3Knkbbc7HAHSXCtDqjyBJLIofvox0FgfKP30X7ZzFAHTXAogkJgYpAuJHUCftZAASb1EVOH/GphHkHwaQu5AU3n8f4+iH3RbnbyBn7EOyNgkUGUD2X0z3O19keB/uqA9kvAcJvItxtoVEjPNmDMVRLjXgoHsT4WgO8vmHaKdUaFAqgdyIZOq5q6HabPCwfuZrzPU20tkDHklRTvIgJwLxYli0AwDQPva9BmGXugAAAABJRU5ErkJggg==');" title="Горячая тема"></div>
												<?php
													}
												?>

												<a href="/topic/<?php echo htmlspecialchars($topicid); ?>/"><?php echo htmlspecialchars($title); ?></a>
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
															<a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo htmlspecialchars($login); ?></a>
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
																<a href="/user/<?php echo htmlspecialchars($userid2); ?>/"><?php echo htmlspecialchars($login2); ?></a>
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
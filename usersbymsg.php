<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<h3 class="panel-title">Пользователи</h3>
	</div>
	<script>
		document.title = 'Пользователи';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Аватар</th>
						<th>Логин</th>
						<th>Количество сообщений</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$query = '
							SELECT `userid`, MD5(LOWER(TRIM(`mail`))), `state` 
							FROM `users` 
							ORDER BY (SELECT COUNT(*) 
									  FROM `posts` 
									  WHERE `users`.`userid`=`posts`.`userid`) DESC;';

						$req = $readydb->prepare($query);
						$req->execute();

						while (list($userid, $login, $state) = $req->fetch(PDO::FETCH_NUM)) {
							if (!isAdmin($readydb) && $state == 0) {
								continue;
							}

							?>
								<tr>
									<td><img style="margin-right: 15px; width: 25px; height: 25px;" src="<?php echo getGravatarLink($userid, 25, $readydb); ?>" align="left"><?php if (isUserOnline($userid, $readydb) == 1) { echo '<div class="online-indicator"></div>'; } ?></td>
									<td><a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo getUserTitleById($userid, $readydb); ?></a><?php if (isUserBanned($userid, $readydb)) { ?> <span style="color: black;">(</span><span style="color: maroon;">banned</span><span style="color: black;">)</span><?php } ?></td>
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
							<?php
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
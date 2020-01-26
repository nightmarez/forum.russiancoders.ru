<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<h3 class="panel-title">Пользователи OnLine за последние 24 часа</h3>
	</div>
	<script>
		document.title = 'Пользователи OnLine за последние 24 часа';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Логин</th>
						<th>Дата</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$query = 
							'SELECT `userid`, `login`, `last`, (now() - `last`) AS `online`, MD5(LOWER(TRIM(`mail`))), `state`  
							 FROM `users` WHERE TIME_TO_SEC(TIMEDIFF(NOW(), `last`)) <= 24 * 60 * 60 
							 ORDER BY `last` DESC LIMIT 0, 100;';

						$req = $readydb->prepare($query);
						$req->execute();

						while (list($userid, $login, $last, $online, $mailmd5, $state) = $req->fetch(PDO::FETCH_NUM)) {
							//if (!isAdmin($readydb) && $state == 0) {
							//	continue;
							//}

							//if ($state == 2) {
							//	continue;
							//}

							?>
								<tr>
									<td>
										<img style="margin-right: 15px; width: 25px; height: 25px;" src="<?php echo getGravatarLink($userid, 50, $readydb); ?>" align="left">
										<a href="/user/<?php echo htmlspecialchars($userid); ?>/" style="float: left;"><?php echo getUserTitleById($userid, $readydb); ?></a><?php if (intval($online <= 80 /* seconds */)) { ?><div class="online-indicator"></div><?php } ?>
									</td>
									<td>
										<?php echo $last; ?>
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
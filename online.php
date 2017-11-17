<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
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
						$db = new PdoDb();

						$query =
							'SELECT `userid`, `login`, `last`, (now() - `last`) as `online`, MD5(LOWER(TRIM(`mail`))) FROM `users` WHERE TIME_TO_SEC(TIMEDIFF(NOW(), `last`)) <= 24 * 60 * 60
 ORDER BY `last` DESC LIMIT 0, 100;';

						$req = $db->prepare($query);
						$req->execute();

						while (list($userid, $login, $last, $online, $mail) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<tr>
									<td>
										<img style="margin-right: 15px;" src="<?php echo 'https://secure.gravatar.com/avatar/' . $mail . '.jpg?s=200';?>" align="left">
										<a href="/user/<?php echo htmlspecialchars($userid); ?>/" style="float: left;"><?php echo htmlspecialchars($login); ?></a><?php if (intval($online <= 80 /* seconds */)) { ?><div class="online-indicator"></div><?php } ?>
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
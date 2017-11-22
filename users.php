<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
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
						<th>Последнее посещение</th>
						<th>Регистрация</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$db = new PdoDb();

						$query =
							'SELECT `userid`, `login`, MD5(LOWER(TRIM(`mail`))) FROM `users` ORDER BY `id` DESC LIMIT 0, 1000;';

						$req = $db->prepare($query);
						$req->execute();

						while (list($userid, $login, $mail) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<tr>
									<td>
										<img style="margin-right: 15px;" src="<?php echo 'https://secure.gravatar.com/avatar/' . $mail . '.jpg?s=25';?>" align="left">
									</td>
									<td>
										<a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo htmlspecialchars($login); ?></a>
									</td>
									<td>
										<?php echo getUserLastVisit($userid, $db); ?>
									</td>
									<td>
										<?php echo getUserFirstVisit($userid, $db); ?>
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
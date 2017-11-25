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
						<th>Регистрация</th>
						<th>Последнее посещение</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$query = 'SELECT `userid`, `login`, MD5(LOWER(TRIM(`mail`))) FROM `users` ORDER BY `first` DESC;';

						$req = $readydb->prepare($query);
						$req->execute();

						while (list($userid, $login, $mail) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<tr>
									<td>
										<img style="margin-right: 15px;" src="<?php echo getGravatarLink($userid, 25, $readydb); ?>" align="left">
									</td>
									<td>
										<a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo getUserLoginById($userid, $readydb); ?></a>
									</td>
									<td>
										<?php echo getUserFirstVisit($userid, $readydb); ?>
									</td>
									<td>
										<?php echo getUserLastVisit($userid, $readydb); ?>
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
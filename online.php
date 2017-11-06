<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Пользователи OnLine</h3>
	</div>

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
							'SELECT `userid`, `login`, `last` FROM `users` ORDER BY `last` DESC LIMIT 0, 100;';

						$req = $db->prepare($query);
						$req->execute();

						while (list($userid, $login, $last) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<tr>
									<td>
										<a href="/user.php?userid=<?php echo htmlspecialchars($userid); ?>"><?php echo htmlspecialchars($login); ?></a>
									</td>
									<td>
										echo $last;
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
<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Пользователи OnLine</h3>
	</div>
	<script>
		document.title = 'Пользователи OnLine';
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
							'SELECT `userid`, `login`, `last`, (now() - `last`) as `online` FROM `users` ORDER BY `last` DESC LIMIT 0, 100;';

						$req = $db->prepare($query);
						$req->execute();

						while (list($userid, $login, $last, $online) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<tr>
									<td>
										<a href="/user.php?userid=<?php echo htmlspecialchars($userid); ?>"><?php echo htmlspecialchars($login); ?></a>
										<!-- online-indicator -->
										<!-- <?php echo $online; ?> -->
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
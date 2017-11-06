<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Разделы</h3>
	</div>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Раздел</th>
						<th>Темы</th>
						<th>Сообщения</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$db = new PdoDb();

						$query =
							'SELECT `sectionid`, `title` FROM `sections` ORDER BY `id`;';

						$req = $db->prepare($query);
						$req->execute();

						while (list($sectionid, $title) = $req->fetch(PDO::FETCH_NUM)) {
							?>
								<tr>
									<td>
										<a href="/section.php?sectionid=<?php echo htmlspecialchars($sectionid); ?>"><?php echo htmlspecialchars($title); ?></a>
									</td>
									<td>
										?
									</td>
									<td>
										?
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
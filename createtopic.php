<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Создание темы</h3>
	</div>

	<?php
		$sectionid = false;

		if (isset($_GET['sectionid'])) {
			$sectionid = htmlspecialchars($_GET['sectionid']);
		}
	?>

	<div class="panel-body">
		<div class="table-responsive">
			<form method="POST" action="docreatetopic.php">
				<table class="table">
					<tbody>
						<tr>
							<td>
								Раздел
							</td>
							<td>
								<select name="sectionid">
									<?php
										$db = new PdoDb();

										$query =
											'SELECT `sectionid`, `title` FROM `sections` ORDER BY `id`;';

										$req = $db->prepare($query);
										$req->execute();

										while (list($sectionid2, $title) = $req->fetch(PDO::FETCH_NUM)) {
											?>
												<option value="<?php echo $sectionid2; ?>" <?php if ($sectionid === $sectionid2) echo 'selected'; ?>><?php echo htmlspecialchars($title); ?></option>
											<?php
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								Тема
							</td>
							<td>
								<input type="text" maxlength="40" name="title" style="min-width: 200px;">
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<p>Сообщение</p>
								<textarea name="content" style="min-width: 800px; min-height: 300px;"></textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" class="btn btn-primary" value="Создать">
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>
<?php require_once('recaptchalib.php'); ?>

<script src='https://www.google.com/recaptcha/api.js'></script>

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
								<select class="form-control" name="sectionid">
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
								<input class="form-control" type="text" maxlength="40" name="title" style="min-width: 200px;">
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<p>Сообщение</p>
								<textarea class="form-control" name="content" style="min-width: 800px; min-height: 300px;"></textarea>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo recaptcha_get_html('6LdlUjcUAAAAACmECdupCukxHQt-KGv-AKn0UTy3', null, true); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" class="btn btn-primary" style="float: left;" value="Создать">
								<div style="float: left; margin-left: 10px;">
									<input type="button" class="btn btn-primary" id="upload-image-btn" value="Загрузить изображение">
								</div>
							</td>
						</tr>
						<?php if (isset($_GET['error'])) { ?>
							<tr>
								<td class="form-group col-md-12 col-sm-12">
									<p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
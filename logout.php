<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<h3 class="panel-title">Выход</h3>
	</div>

	<div class="panel-body">
		<form action="/dologout.php" method="POST">
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-12 col-sm-12">
					<input type="submit" id="submit-button" class="btn btn-primary" value="Выйти в этом браузере">
				</div>
			</div>
		</form>
	</div>

	<div class="panel-body">
		<form action="/dofulllogout.php" method="POST">
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-12 col-sm-12">
					<input type="submit" id="submit-button" class="btn btn-primary" value="Выйти на всех устройствах">
				</div>
			</div>
		</form>
	</div>
</div>

<?php include_once('footer.php'); ?>
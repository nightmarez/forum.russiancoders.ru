<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Авторизация</h3>
	</div>

	<div class="panel-body">
		<form action="/dologin.php" method="POST">
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-6 col-sm-6">
					<label for="name">Логин*</label>
					<input type="text" class="form-control input-sm" id="login" name="login" placeholder="" maxlength="20">
				</div>
				<div class="form-group col-md-6 col-sm-6">
					<label for="email">Пароль*</label>
					<input type="email" class="form-control input-sm" id="pass" name="pass" placeholder="">
				</div>
			</div>
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-12 col-sm-12">
					<input type="submit" id="submit-button" class="btn btn-primary" value="Войти">
				</div>
			</div>
			<?php if (isset($_GET['error'])) { ?>
				<div class="col-md-12 col-sm-12">
					<div class="form-group col-md-12 col-sm-12">
						<p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
					</div>
				</div>
			<?php } ?>
		</form>
	</div>
</div>

<?php include_once('footer.php'); ?>
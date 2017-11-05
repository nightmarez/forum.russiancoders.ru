<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<script>
	function testLogin() {
		return true;
	}

	function testMail() {
		return true;
	}

	function testPasswords() {
		return $('#pass1').val() == $('#pass2').val();
	}

	$(document).ready(function() {
		$('form').submit(function() {
			if (testLogin() && testMail() && testPasswords()) {
				$('form').submit();
			}
		});
	});
</script>

<div class="panel panel-primary" style="margin:20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Регистрация</h3>
	</div>

	<div class="panel-body">
		<form>
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-6 col-sm-6">
					<label for="name">Логин*</label>
					<input type="text" class="form-control input-sm" id="name" placeholder="" maxlength="20">
				</div>
				<div class="form-group col-md-6 col-sm-6">
					<label for="email">Email*</label>
					<input type="email" class="form-control input-sm" id="mail" placeholder="">
				</div>
			</div>
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-6 col-sm-6">
					<label for="name">Пароль*</label>
					<input type="password" class="form-control input-sm" id="pass1" placeholder="" maxlength="40">
				</div>
				<div class="form-group col-md-6 col-sm-6">
					<label for="email">Повторите пароль*</label>
					<input type="password" class="form-control input-sm" id="pass2" placeholder="" maxlength="40">
				</div>
			</div>
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-12 col-sm-12">
					<input type="submit" class="btn btn-primary" value="Зарегистрироваться">
				</div>
			</div>
		</form>
	</div>
</div>


<?php include_once('footer.php'); ?>
<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>
<?php require_once('recaptchalib.php'); ?>

<script src='https://www.google.com/recaptcha/api.js' defer></script>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Регистрация</h3>

		<br>

		<h4 class="panel-title">Данный форум располагается на серверах, находящися на территории Российской Федерации. Регистрируясь на данном форуме,
		Вы соглашаетесь соблюдать законы РФ, и нести ответственность за их нарушение. Любая информация о зарегистрированных пользователях в любой момент
		может быть передана правоохранительным органам по первому же официальному запросу. Не смотря на вышесказанное, данный форум предназначен для свободного
		общения на любую тематику, в том числе на нём разрешена нецензурная лексика и "взрослый" контент. Потому регистрируясь на данном форуме, Вы даёте согласие
		на то, что не имеете претензий по поводу общения с использованием нецензурной лексики, а также прямых оскорблений в Ваш адрес со стороны других пользователей.
		Также, регистрируясь на данном форуме, Вы подтверждаете, что являетесь совершеннолетним дееспособным человеком и не возражаете против просмотра информации,
		предназначенной исключительно для совершеннолетних граждан. Также, регистрируясь на данном форуме, Вы даёте согласие на обработку личных данных, а также
		соглашаетесь на хранение информации в Вашем браузере с использованием таких технологий как cookie, LocalStorage, WebSQL и подобных. Если что-либо из
		вышеперечисленного Вас не устраивает, пожалуйста, закройте страницу с данным форумом и не посещайте его больше.</h4>
	</div>

	<div class="panel-body">
		<h3>Правила регистрации.</h3>
		<br>
		<div>
			Во избежание регистрации на форуме неадекватных личностей, ботов, мимокрокодилов и прочей швали, на данном форуме присутствует
			премодерация всех желающих зарегистрироваться. Заполните, пожалуйста, внимательно все поля, укажите корректный e-mail,
			напишите о себе, где и под какими никами вы присутствуюете на других ресурсах, вобщем, предоставьте информацию, однозначно подтверждающую,
			что Вы - взрослый адекватный человек. В течении суток Ваша заявка будет рассмотрена и, в случае положительного решения,
			Вам будет создан аккаунт и выслано извещение об этом на указанную почту. На форуме присутствуют аватары, которые берутся по
			Вашему e-mail адресу из сервиса gravatar. Будет плюсом, если Вы зарегистрированы на нём, и имеете там загруженную аватару.
		</div>
	</div>

	<div class="panel-body">
		<form action="/doregrequest.php" method="POST">
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-6 col-sm-6">
					<label for="login">Логин*</label>
					<input type="text" class="form-control input-sm" id="login" name="login" placeholder="" maxlength="20">
				</div>
				<div class="form-group col-md-6 col-sm-6">
					<label for="email">Email*</label>
					<input type="email" class="form-control input-sm" id="mail" name="mail" placeholder="" maxlength="255">
				</div>
			</div>
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-6 col-sm-6">
					<label for="pass1">Пароль*</label>
					<input type="password" class="form-control input-sm" id="pass1" name="pass1" placeholder="" maxlength="40">
				</div>
				<div class="form-group col-md-6 col-sm-6">
					<label for="pass2">Повторите пароль*</label>
					<input type="password" class="form-control input-sm" id="pass2" name="pass2" placeholder="" maxlength="40">
				</div>
			</div>
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-12 col-sm-12">
					<?php echo recaptcha_get_html('6LdlUjcUAAAAACmECdupCukxHQt-KGv-AKn0UTy3', null, true); ?>
				</div>
			</div>
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-12 col-sm-12">
					<label for="txt">Укажите информацию о себе*</label>
					<textarea id="txt" name="txt" maxlength="2000" style="min-width: 800px; min-height: 300px; width: 100%; margin-bottom: 5px;"></textarea>
				</div>
			</div>
			<div class="col-md-12 col-sm-12">
				<div class="form-group col-md-12 col-sm-12">
					<input type="button" id="submit-button" class="btn btn-primary" value="Зарегистрироваться">
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
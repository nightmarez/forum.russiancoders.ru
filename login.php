<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
    <div class="panel-heading">
        <h3 class="panel-title">Вход</h3>

        <br>

        <h4 class="panel-title" style="padding-top: 5px; padding-bottom: 5px;">Посещая на данный форум,
            Вы соглашаетесь соблюдать законы, и нести ответственность за их нарушение. Любая информация о зарегистрированных пользователях в любой момент
            может быть передана правоохранительным органам по первому же официальному запросу. Не смотря на вышесказанное, данный форум предназначен для свободного
            общения на любую тематику, в том числе на нём разрешена нецензурная лексика и "взрослый" контент. Потому регистрируясь на данном форуме, Вы даёте согласие
            на то, что не имеете претензий по поводу общения с использованием нецензурной лексики, а также прямых оскорблений в Ваш адрес со стороны других пользователей.
            Также, регистрируясь на данном форуме, Вы подтверждаете, что являетесь совершеннолетним дееспособным человеком и не возражаете против просмотра информации,
            предназначенной исключительно для совершеннолетних граждан. Также, регистрируясь на данном форуме, Вы даёте согласие на обработку личных данных, а также
            соглашаетесь на хранение информации в Вашем браузере с использованием таких технологий как cookie, LocalStorage, WebSQL и подобных. Если что-либо из
            вышеперечисленного Вас не устраивает, пожалуйста, закройте страницу с данным форумом и не посещайте его больше.</h4>
    </div>

	<div class="panel-body">
        <?php
            include_once('google-credentials.php');

            $params = array(
                'redirect_uri'  => $redirect_uri,
                'response_type' => 'code',
                'client_id'     => $client_id,
                'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
            );

            $link = $url . '?' . urldecode(http_build_query($params));
        ?>
        <a class="btn btn-block btn-social btn-google" style="width: 200px; color: white;" href="<?php echo $link; ?>">
            <span class="fa fa-google"></span> Войти через Google
        </a>
	</div>
</div>

<?php include_once('footer.php'); ?>

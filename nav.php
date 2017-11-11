<?php require_once('utils.php'); ?>

		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-collapse">
					<ul class="nav navbar-nav">
						<li><a href="/">Форум</a></li>
						<li><a href="/tracker/">Трекер</a></li>
						<li><a href="/faq/">ЧаВо</a></li>
						<li><a href="/users/">Пользователи</a></li>
						<li><a href="/online/">Онлайн</a></li>
						<li><a href="/profile/">Профиль</a></li>
						<li><a href="/gallery/">Галерея</a></li>
						<li><a href="/donate/">Донат</a></li>
						<?php if (isLogin()) { ?>
							<li><a href="/logout/">Выход</a></li>
						<?php } else { ?>
							<li><a href="/login/">Вход</a></li>
							<li><a href="/register/">Регистрация</a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</nav>
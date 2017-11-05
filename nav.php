<?php require_once('utils.php'); ?>

		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-collapse">
					<ul class="nav navbar-nav">
						<li><a href="/">Форум</a></li>
						<li><a href="/faq.php">ЧаВо</a></li>
						<li><a href="/users.php">Пользователи</a></li>
						<li><a href="/online.php">Онлайн</a></li>
						<?php if (isLogin()) { ?>
							<li><a href="/logout.php">Выход</a></li>
						<?php } else { ?>
							<li><a href="/login.php">Вход</a></li>
							<li><a href="/register.php">Регистрация</a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</nav>
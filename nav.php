<?php require_once('utils.php'); ?>

<nav class="navbar navbar-default">
	<div class="container-fluid">
		<ul class="nav navbar-nav" style="width: calc(100% - 56px);">
			<li><a href="/">Форум</a></li>
			<li><a href="/tracker/">Трекер</a></li>
			<li><a href="/faq/">ЧаВо</a></li>
			<li><a href="/users/">Пользователи</a></li>
			<li><a href="/online/">Онлайн</a></li>

			<?php if (isLogin()) { ?>
				<li><a href="/profile/">Профиль</a></li>
				<li><a href="/messages/">Сообщения</a><?php $cu = getCountUnviewedMessages(); echo $cu; if ($cu > 0) { ?>&nbsp;<p style="color red;">( <?php echo $cu; ?> )</p><?php } ?></li>
				<li><a href="/chat/">Чат</a></li>
			<?php } ?>

			<li><a href="/gallery/">Галерея</a></li>
			<li><a href="/donate/">Донат</a></li>

			<?php if (isLogin()) { ?>
				<li><a href="/logout/">Выход</a></li>
			<?php } else { ?>
				<li><a href="/login/">Вход</a></li>
				<li><a href="/register/">Регистрация</a></li>
			<?php } ?>

			<li style="float: right;">
				<div id="custom-search-input" style="max-width: 300px; margin-top: 7px;">
					<div class="input-group col-md-12" style="max-height: 35px; padding-top: 1px;">
						<form method="POST" action="/search/">
							<input type="text" class="form-control input-lg" name="search" placeholder="Поиск" style="width: 300px; height: 35px; font-size: 14px;" />
						</form>
						<span class="input-group-btn">
							<button class="btn btn-info btn-lg" type="button" id="btn-search" style="height: 35px; padding-top: 6px;">
								<i class="glyphicon glyphicon-search"></i>
							</button>
						</span>
					</div>
				</div>
			</li>
		</ul>
	</div>
</nav>
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" style="float: left; margin-left: 10px;">
				<span>Menu</span>
			</button>
		</div>

		<div class="collapse navbar-collapse" id="navbar-collapse">
		<ul class="nav navbar-nav" style="width: calc(100% - 56px);">
			<?php
				$isLogin = isLogin($readydb);
			?>

			<li>
				<a href="/tracker/">Трекер</a>
			</li>

			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Форум <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="/">Главная</a></li>
					<li><a href="/faq/">ЧаВо</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="/gallery/">Галерея</a></li>
					<li><a href="/donate/">Донат</a></li>
					<li role="separator" class="divider"></li>
					<?php if ($isLogin) { ?>
						<li><a href="/logout/">Выход</a></li>
					<?php } else { ?>
						<li><a href="/login/">Вход</a></li>
						<li><a href="/register/">Регистрация</a></li>
					<?php } ?>
				</ul>
			</li>

			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Разделы <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<?php
						$query =
							'SELECT `sectionid`, `title` 
							 FROM `sections` 
							 ORDER BY `id`;';

						$req = $readydb->prepare($query);
						$req->execute();

						while (list($sectionid, $title) = $req->fetch(PDO::FETCH_NUM)) {
					?>
					<li><a href="/section/<?php echo $sectionid; ?>/"><?php echo htmlspecialchars($title); ?></a></li>
					<?php
						}
					?>
				</ul>
			</li>

			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Пользователи <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="/users/">Пользователи ресурса</a></li>
					<li><a href="/online/">Онлайн за 24 часа</a></li>
					<li><a href="/onlineweek/">Онлайн за неделю</a></li>
					<li><a href="/usersbymsg/">По количеству сообщений</a></li>
				</ul>
			</li>

			<?php if ($isLogin) { ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Профиль <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="/profile/">Профиль</a></li>
						<li><a href="/messages/" style="float: left;">Сообщения</a><?php $cu = getCountUnviewedMessages($readydb); if ($cu > 0) { ?>&nbsp;<p style="color: red; font-size: 12px; float: left; margin: 17px 0 0 -10px;">(<?php echo $cu; ?>)</p><?php } ?></li>
					</ul>
				</li>
			<?php } ?>

			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Прочее <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="/webcam/">Веб-камера</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="/about/">О разработчике</a></li>
				</ul>
			</li>

			<?php if ($isLogin) { ?>
				<li style="float: right; display: none;">
					<div id="custom-search-input" style="max-width: 150px; margin-top: 7px;">
						<div class="input-group col-md-12" style="max-height: 35px; padding-top: 1px;">
							<form method="POST" action="/search/">
								<input type="text" class="form-control input-lg" name="search" placeholder="Поиск" style="width: 150px; height: 35px; font-size: 14px;" />
							</form>
							<span class="input-group-btn">
								<button class="btn btn-info btn-lg" type="button" id="btn-search" style="height: 35px; padding-top: 6px;">
									<i class="glyphicon glyphicon-search"></i>
								</button>
							</span>
						</div>
					</div>
				</li>
			<?php } ?>
		</ul>
		</div>
	</div>
</nav>
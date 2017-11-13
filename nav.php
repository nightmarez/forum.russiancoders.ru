<?php require_once('utils.php'); ?>

		<nav class="navbar navbar-default">
			<div class="container-fluid">
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
						<li style="float: right;">
										<div id="custom-search-input" style="max-width: 300px; margin-top: 7px;">
											<div class="input-group col-md-12" style="max-height: 35px; padding-top: 1px;">
												<input type="text" class="form-control input-lg" placeholder="Поиск" style="width: 300px; height: 35px;" />
												<span class="input-group-btn">
													<button class="btn btn-info btn-lg" type="button" style="height: 35px; padding-top: 6px;">
														<i class="glyphicon glyphicon-search"></i>
													</button>
												</span>
											</div>
										</div>
						</li>
					</ul>
			</div>
		</nav>
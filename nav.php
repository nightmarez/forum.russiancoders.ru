<?php require_once('utils.php'); ?>

<?php
	$isLogin = isLogin($readydb);
?>

<nav id="left-navigation">
	<ul>
		<li>
			<a href="#" class="left-navigation-header" id="ln-1">Форум</a>
			<ul style="display: none;">
				<li><a href="/forum/">Главная</a></li>
				<li><a href="/">Трекер</a></li>
				<li><a href="/gallery/">Галерея</a></li>
				<li><a href="/faq/">ЧаВо</a></li>
				<li><a href="/donate/">Донат</a></li>
			</ul>
		</li>
		<?php 
			$groupQuery = 'SELECT `id`, `title` FROM `groups` ORDER BY `orderid`';
			$groupReq = $readydb->prepare($groupQuery);
			$groupReq->execute();
			$ln = 0;

			while (list($groupId, $groupTitle) = $groupReq->fetch(PDO::FETCH_NUM)) {
		?>
			<li>
				<a href="#" class="left-navigation-header" id="ln<?php echo $ln++; ?>"><?php echo htmlspecialchars($groupTitle); ?></a>
				<ul style="display: none;">
					<?php
						$query = 'SELECT `sectionid`, `title` FROM `sections` WHERE `groupid`=:groupid ORDER BY `orderid`;';
						$req = $readydb->prepare($query);
						$req->bindParam(':groupid', $groupId, PDO::PARAM_INT);
						$req->execute();

						while (list($sectionid, $title) = $req->fetch(PDO::FETCH_NUM)) {
					?>
						<li><a href="/section/<?php echo $sectionid; ?>/"><?php echo htmlspecialchars($title); ?></a></li>
					<?php
						}
					?>
				</ul>
			</li>
		<?php
			}
		?>
		<li>
			<a href="#" class="left-navigation-header" id="ln-2">Пользователи</a>
			<ul style="display: none;">
				<li><a href="/users/">Пользователи ресурса</a></li>
				<li><a href="/online/">Онлайн за 24 часа</a></li>
				<li><a href="/onlineweek/">Онлайн за неделю</a></li>
				<li><a href="/usersbymsg/">По количеству сообщений</a></li>
			</ul>
		</li>

		<li>
			<a href="#" class="left-navigation-header" id="ln-3">Профиль</a>
			<?php $cu = getCountUnviewedMessages($readydb); if ($cu > 0) { ?>
				<ul style="display: block;">
			<?php } else { ?>
				<ul style="display: none;">
			<?php } ?>
				<?php if ($isLogin) { ?>
					<li><a href="/profile/">Профиль</a></li>
					<li><a href="/messages/">Сообщения</a><?php $cu = getCountUnviewedMessages($readydb); if ($cu > 0) { ?>&nbsp;<p style="color: red; font-size: 12px; float: right; font-weight: bold;">(<?php echo $cu; ?>)</p><?php } ?></li>
					<li><a href="/logout/">Выход</a></li>
				<?php } else { ?>
					<li><a href="/login/">Вход</a></li>
					<li><a href="/register/">Регистрация</a></li>
				<?php } ?>
			</ul>
		</li>

		<li style="display: none;">
			<a href="#" class="left-navigation-header" id="ln-4">Прочее</a>
			<ul style="display: none;">
				<li><a href="/webcam/">Веб-камера</a></li>
				<li><a href="/about/">О разработчике</a></li>
			</ul>
		</li>
	</ul>
</nav>
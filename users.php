<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$usersCount = totalUsersCount($readydb);
	$pagesCount = usersPagesCount($usersCount);
	$page = 0;
	$ppp = usersPerPage();

	if (isset($_GET['page'])) {
		$page = intval($_GET['page']) - 1;
	}

	if ($page >= $pagesCount) {
		$page = $pagesCount - 1;
	}

	$number = $page * $ppp;
?>

<?php
	echo "<!--\r\n";
	echo '====================================================' . "\r\n";
	echo "debug info\r\n";
	echo 'Users Count: ' . $usersCount . "\r\n"; 
	echo 'Pages Count: ' . $pagesCount  . "\r\n";
	echo 'Page: ' . $page  . "\r\n";
	echo 'Users per Page: ' . $ppp  . "\r\n";
	echo '====================================================' . "\r\n";
	echo "-->\r\n";
?>

<?php
	if ($pagesCount > 1) {
?>
	<nav aria-label="Page navigation" style="text-align: center;">
		<ul class="pagination">
			<li<?php if ($page == 0) { echo ' class="disabled"'; } ?>>
				<a href="/users/<?php echo $page; ?>/" rel="prev" aria-label="Previous"<?php if ($page == 0) { echo ' onclick="return false" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<?php
				$dots = false;

				$poffset1 = 3;
				$poffset2 = 3;

				if ($page == 4) {
					$poffset1 = 1;
				} else if ($page == 5) {
					$poffset1 = 2;
				}

				if ($page == $pagesCount - 5) {
					$poffset2 = 1;
				} else if ($page == $pagesCount - 6) {
					$poffset2 = 2;
				}

				if ($pagesCount >= 12) {
					for ($p = 1; $p <= $pagesCount; ++$p) {
						$pagen = $p - 1;

						if ($pagen < $poffset1 || 
							$pagen > $pagesCount - ($poffset2 + 1) || 
							$pagen > $page - 3 && $pagen < $page + 3 || 
							(($page < 3 || $page > $pagesCount - 4) && $pagen > ceil($pagesCount / 2 - 3) && $pagen < ceil($pagesCount / 2 + 3)))
						{
							$dots = false;
							?>
								<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/users/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
							<?php
						}
						else
						{
							if (!$dots)
							{
								$dots = true;
								?>
									<li class="disabled"><a style="border-bottom: none; border-top: none;" href="#" onclick="return false" onmousedown="return false">...</a></li>
								<?php
							}
						}
					}
				} else {
					for ($p = 1; $p <= $pagesCount; ++$p) {
						$pagen = $p - 1;

						?>
							<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/users/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
						<?php
					}
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="/users/<?php echo ($page + 2); ?>/" rel="next" aria-label="Next"<?php if ($page >= $pagesCount - 1) { echo ' onclick="return false;" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</nav>
<?php
	}
?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<h3 class="panel-title">Пользователи</h3>
	</div>
	<script>
		document.title = 'Пользователи';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Аватар</th>
						<th>Логин</th>
						<th>Регистрация</th>
						<th>Последнее посещение</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$query = isAdmin($readydb) ?
							'SELECT `userid`, MD5(LOWER(TRIM(`mail`))), `state` 
							 FROM `users` 
							 ORDER BY `first` DESC 
							 LIMIT :skipcount, :pagesize;' :
							 'SELECT `userid`, MD5(LOWER(TRIM(`mail`))), `state` 
							 FROM `users` 
							 WHERE `state` > 0 
							 ORDER BY `first` DESC 
							 LIMIT :skipcount, :pagesize;';

						$skipCount = $page * $ppp;

						$req = $readydb->prepare($query);
						$req->bindParam(':pagesize', $ppp, PDO::PARAM_INT);
						$req->bindParam(':skipcount', $skipCount, PDO::PARAM_INT);
						$req->execute();

						while (list($userid, $login, $state) = $req->fetch(PDO::FETCH_NUM)) {
							if (!isAdmin($readydb) && $state == 0) {
								continue;
							}

							?>
								<tr>
									<td><img style="margin-right: 15px; max-width: 25px; max-width: 25px min-width: 25px; min-height: 25px;" src="<?php echo getGravatarLink($userid, 25, $readydb); ?>" align="left"><?php if (isUserOnline($userid, $readydb) == 1) { echo '<div class="online-indicator"></div>'; } ?></td>
									<td><a href="/user/<?php echo htmlspecialchars($userid); ?>/"><?php echo getUserTitleById($userid, $readydb); ?></a><?php if (isUserBanned($userid, $readydb)) { ?> <span style="color: black;">(</span><span style="color: maroon;">banned</span><span style="color: black;">)</span><?php } ?></td>
									<td><?php echo getUserFirstVisit($userid, $readydb); ?></td>
									<td><?php echo getUserLastVisit($userid, $readydb); ?></td>
								</tr>
							<?php
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
	if ($pagesCount > 1) {
?>

	<nav aria-label="Page navigation" style="text-align: center;">
		<ul class="pagination">
			<li<?php if ($page == 0) { echo ' class="disabled"'; } ?>>
				<a href="/users/<?php echo $page; ?>/" rel="prev" aria-label="Previous"<?php if ($page == 0) { echo ' onclick="return false" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<?php
				$dots = false;

				$poffset1 = 3;
				$poffset2 = 3;

				if ($page == 4) {
					$poffset1 = 1;
				} else if ($page == 5) {
					$poffset1 = 2;
				}

				if ($page == $pagesCount - 5) {
					$poffset2 = 1;
				} else if ($page == $pagesCount - 6) {
					$poffset2 = 2;
				}

				if ($pagesCount >= 12) {
					for ($p = 1; $p <= $pagesCount; ++$p) {
						$pagen = $p - 1;

						if ($pagen < $poffset1 || 
							$pagen > $pagesCount - ($poffset2 + 1) || 
							$pagen > $page - 3 && $pagen < $page + 3 || 
							(($page < 3 || $page > $pagesCount - 4) && $pagen > ceil($pagesCount / 2 - 3) && $pagen < ceil($pagesCount / 2 + 3)))
						{
							$dots = false;
							?>
								<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/users/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
							<?php
						}
						else
						{
							if (!$dots)
							{
								$dots = true;
								?>
									<li class="disabled"><a style="border-bottom: none; border-top: none;" href="#" onclick="return false" onmousedown="return false">...</a></li>
								<?php
							}
						}
					}
				} else {
					for ($p = 1; $p <= $pagesCount; ++$p) {
						$pagen = $p - 1;

						?>
							<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/users/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
						<?php
					}
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="/users/<?php echo ($page + 2); ?>/" rel="next" aria-label="Next"<?php if ($page >= $pagesCount - 1) { echo ' onclick="return false;" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</nav>

<?php
	}
?>

<?php include_once('footer.php'); ?>
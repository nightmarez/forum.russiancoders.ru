<?php
	include_once('head.php');
	include_once('nav.php');

	function scanDirectory($base) {
		$result = array();
		$cdir = scandir($base);
		$exclude = array('.', '..', 'gallery', 'rewards', '.git');

		foreach ($cdir as $value) { 
			if (!in_array($value, $exclude)) {
				$path = $base . DIRECTORY_SEPARATOR . $value;

				if (is_dir($path)) {
					if (isUserIdExists($value)) {
						$result = array_merge($result, scanDirectory($path)); 
					} else {
						continue;
					}
				} else {
					if (endsWith($value, '.gif') || endsWith($value, '.png') || endsWith($value, '.sh') || endsWith($value, '.mp3')) {
						continue;
					} else {
						$result[] = $path; 
					}
				} 
			} 
		} 
   
		return $result; 
	}

	function createThumbnail($image_name, $new_width, $new_height, $uploadDir, $moveToDir) {
		$result = NULL;

		$path = $uploadDir . '/' . $image_name;
		$mime = getimagesize($path);

		if ($mime['mime'] == 'image/png') { 
			$src_img = imagecreatefrompng($path);
		}

		if ($mime['mime'] == 'image/jpg' || $mime['mime'] == 'image/jpeg' || $mime['mime'] == 'image/pjpeg') {
			$src_img = imagecreatefromjpeg($path);
		}   

		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);

		if ($old_x > $old_y) {
			$thumb_w = $new_width;
			$thumb_h = $old_y * ($new_height / $old_x);
		}

		if ($old_x < $old_y) {
			$thumb_w = $old_x * ($new_width / $old_y);
			$thumb_h = $new_height;
		}

		if ($old_x == $old_y) {
			$thumb_w = $new_width;
			$thumb_h = $new_height;
		}

		$dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y); 
		$new_thumb_loc = $moveToDir . $image_name;

		if ($mime['mime'] == 'image/png') {
			$result = imagepng($dst_img, $new_thumb_loc, 8);
			chmod($new_thumb_loc, 0777);
		}

		if ($mime['mime'] == 'image/jpg' || $mime['mime'] == 'image/jpeg' || $mime['mime'] == 'image/pjpeg') {
			$result = imagejpeg($dst_img, $new_thumb_loc, 80);
			chmod($new_thumb_loc, 0777);
		}

		imagedestroy($dst_img); 
		imagedestroy($src_img);

		return $result;
	}

	$files = scanDirectory('/var/www/russiancoders.club/');
	$filesCount = count($files);
	$filesPerPage = 20;
	$pagesCount = ceil($filesCount / $filesPerPage);
	$page = 0;
	$skip = 0;

	if (isset($_GET['page'])) {
		$page = intval($_GET['page']) - 1;
		$skip = $page * $filesPerPage;
	}

	if ($pagesCount > 1) {
?>

	<nav aria-label="Page navigation" style="text-align: center;">
		<ul class="pagination">
			<li<?php if ($page == 0) { echo ' class="disabled"'; } ?>>
				<a href="/gallery/<?php echo $page; ?>/" rel="prev" aria-label="Previous"<?php if ($page == 0) { echo ' onclick="return false" onmousedown="return false"'; } ?>>
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
								<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/gallery/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
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
							<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/gallery/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
						<?php
					}
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="/gallery/<?php echo ($page + 2); ?>/" rel="next" aria-label="Next"<?php if ($page >= $pagesCount - 1) { echo ' onclick="return false;" onmousedown="return false"'; } ?>>
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
		<h3 class="panel-title">
			Галерея
		</h3>
	</div>
	<script>
		document.title = 'Галерея';
	</script>
	<div class="panel-body">
		<div class="table-responsive">

			<?php
				$c = 0;
				$t = 0;

				foreach ($files as $key => $value) {
					if ($c++ < $skip) {
						continue;
					}

					if ($t++ >= $filesPerPage) {
						break;
					}

					if (!file_exists('/var/www/russiancoders.club/gallery/' . basename($value))) {
						createThumbnail(basename($value), '200', '200', dirname($value), '/var/www/russiancoders.club/gallery/');
					}

					$image = basename($value);
					$image = 'https://russiancoders.club/' . getUserIdByThumbnail($image) . '/' . basename($image);

					?>
						<a href="<?php echo $image; ?>" target="_blank" type="image/jpeg" title="<?php echo basename($image); ?>">
							<div style="width: 200px; height: 200px; overflow: hidden; border: 1px solid silver; margin: 3px; background-image: url('<?php echo 'https://russiancoders.club/gallery/' . basename($value); ?>'); float: left; background-repeat: no-repeat; background-position: center center;"></div>
						</a>
					<?php
				}
			?>

		</div>

		<br><br>

		<div>
			<form action="/uploader/" method="POST">
				<input type="submit" class="btn btn-primary" value="Загрузить изображение">
			</form>
		</div>
	</div>
</div>

<?php
	if ($pagesCount > 1) {
?>

	<nav aria-label="Page navigation" style="text-align: center;">
		<ul class="pagination">
			<li<?php if ($page == 0) { echo ' class="disabled"'; } ?>>
				<a href="/gallery/<?php echo $page; ?>/" rel="prev" aria-label="Previous"<?php if ($page == 0) { echo ' onclick="return false" onmousedown="return false"'; } ?>>
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
								<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/gallery/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
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
							<li<?php if ($pagen == $page) { echo ' class="active"'; } ?>><a href="/gallery/<?php echo $p; ?>/"><?php echo $p; ?></a></li>
						<?php
					}
				}
			?>
			<li<?php if ($page >= $pagesCount - 1) { echo ' class="disabled"'; } ?>>
				<a href="/gallery/<?php echo ($page + 2); ?>/" rel="next" aria-label="Next"<?php if ($page >= $pagesCount - 1) { echo ' onclick="return false;" onmousedown="return false"'; } ?>>
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</nav>

<?php
	}
?>

<br><br>

<?php include_once('footer.php'); ?>
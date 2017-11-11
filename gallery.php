<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
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
				function scanDirectory($base) {
					$result = array(); 
					$cdir = scandir($base);

					foreach ($cdir as $key => $value) 
					{ 
						if (!in_array($value, array('.', '..', 'gallery', '.git'))) 
						{ 
							if (is_dir($base . DIRECTORY_SEPARATOR . $value)) 
							{ 
								$result = array_merge($result, scanDirectory($base . DIRECTORY_SEPARATOR . $value)); 
							} 
							else
							{ 
								$result[] = $base . DIRECTORY_SEPARATOR . $value; 
							} 
						} 
   					} 
   
   					return $result; 
				}

				function createThumbnail($image_name, $new_width, $new_height, $uploadDir, $moveToDir)
				{
					$path = $uploadDir . '/' . $image_name;
					$mime = getimagesize($path);

					if ($mime['mime'] == 'image/png') { 
						$src_img = imagecreatefrompng($path);
					}

					if ($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
						$src_img = imagecreatefromjpeg($path);
					}   

					$old_x = imageSX($src_img);
					$old_y = imageSY($src_img);

					if ($old_x > $old_y) 
					{
						$thumb_w = $new_width;
						$thumb_h = $old_y * ($new_height / $old_x);
					}

					if ($old_x < $old_y) 
					{
						$thumb_w = $old_x*($new_width/$old_y);
						$thumb_h = $new_height;
					}

					if ($old_x == $old_y) 
					{
						$thumb_w = $new_width;
						$thumb_h = $new_height;
					}

					$dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
					imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y); 
					$new_thumb_loc = $moveToDir . $image_name;

					if ($mime['mime'] == 'image/png') {
						$result = imagepng($dst_img, $new_thumb_loc, 8);
						chmod($new_thumb_loc, 777);
					}

					if($mime['mime'] == 'image/jpg' || $mime['mime'] == 'image/jpeg' || $mime['mime'] == 'image/pjpeg') {
						$result = imagejpeg($dst_img, $new_thumb_loc, 80);
						chmod($new_thumb_loc, 777);
					}

					imagedestroy($dst_img); 
					imagedestroy($src_img);

					return result;
				}

				$files = scanDirectory('/var/www/domains/storage.russiancoders.ru/');

				foreach ($files as $key => $value) {
					if (!file_exists('/var/www/domains/storage.russiancoders.ru/gallery/' . basename($value))) {
						createThumbnail(basename($value), '200', '200', dirname($value), '/var/www/domains/storage.russiancoders.ru/gallery/');
					}

					?>
						<div style="width: 200px; height: 200px; overflow: hidden; border: 1px solid silver; margin: 3px; background-image: url('<?php echo 'https://storage.russiancoders.ru/gallery/' . basename($value); ?>'); float: left; background-repeat: no-repeat; background-position: center center;"></div>
					<?php
				}
			?>

		</div>

		<div>
			<form action="uploader.php" method="GET">
				<input type="submit" class="btn btn-primary" value="Загрузить изображение">
			</form>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<h3 class="panel-title">Загрузка изображения</h3>
	</div>
	<script>
		document.title = 'Загрузка изображения';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			<?php
				$userid = $_COOKIE['userid'];

				$userid = false;

				if (isset($_COOKIE['userid'])) {
					$userid = htmlspecialchars($_COOKIE['userid']);

					if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
						$userid = false;
					}
				}

				if ($userid === false) {
					die();
				}

				$fileid = generateUserId();

				$target_dir = "uploads/";
				$target_file = $target_dir . basename($_FILES["imgInp"]["name"]);
				$uploadOk = 1;
				$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

				// Check if image file is a actual image or fake image
				if(isset($_POST["imgSubmit"])) {
					$check = getimagesize($_FILES["imgInp"]["tmp_name"]);
					if($check !== false) {
						//echo "File is an image - " . $check["mime"] . ".";
						$uploadOk = 1;
					} else {
						echo "Файл не является графическим изображением.";
						$uploadOk = 0;
					}
				}

				// Check if file already exists
				if (file_exists($target_file)) {
					echo "Коллизия в директории загрузки файлов. Напишите об этом на форуме и я постараюсь в ближайшее время починить.";
					$uploadOk = 0;
				}

				// Check file size
				if ($_FILES["imgInp"]["size"] > 500000) {
					echo "Извините, файл имеет слишком большой размер.";
					$uploadOk = 0;
				}

				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" &&
				   $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG" && $imageFileType != "GIF") {
					echo "Извините, только JPG, JPEG, PNG и GIF файлы разрешены.";
					$uploadOk = 0;
				}

				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					echo "Загрузка файла не удалась. Напишите об этом на форуме и я постараюсь в ближайшее время починить.";
				// if everything is ok, try to upload file
				} else {
					if (move_uploaded_file($_FILES["imgInp"]["tmp_name"], $target_file)) {
						echo "The file ". basename($_FILES["imgInp"]["name"]). " has been uploaded.";

						if (!file_exists('/var/www/domains/storage.russiancoders.ru/' . $userid)) {
							mkdir('/var/www/domains/storage.russiancoders.ru/' . $userid);
							chmod('/var/www/domains/storage.russiancoders.ru/' . $userid, 0777);
						}

						if (copy($target_file, '/var/www/domains/storage.russiancoders.ru/' . $userid . '/' . $fileid . '.' . $imageFileType)) {
							$image = false;
							$filename = '/var/www/domains/storage.russiancoders.ru/' . $userid . '/' . $fileid . '.' . $imageFileType;

							switch ($imageFileType) {
								case 'jpeg':
									$image = imagecreatefromjpeg($filename);
									break;
								case 'gif':
									$image = imagecreatefromgif($filename);
									break;
								case 'png':
									$image = imagecreatefrompng($filename);
									break;
							}

							if ($image !== false) {
								imagejpeg($image, '/var/www/domains/storage.russiancoders.ru/' . $userid . '/' . $fileid . '.jpg');
								unlink('/var/www/domains/storage.russiancoders.ru/' . $userid . '/' . $fileid . '.' . $imageFileType);
								tryAddPhotographerReward($userid, $readydb);
							}

							echo '<br>' . "\r\n";
							echo 'Файл успешно загружен.' . "\r\n";
							echo '<br>' . "\r\n";
							echo 'Для вставки в сообщение, используйте код:' . "\r\n";
							echo '<br>' . "\r\n";
							echo '<input type="text" value="[img=' . $fileid . ']">' . "\r\n";
						}

						unlink($target_file);
					} else {
						echo 'Что-то где-то пошло не так. Напишите об этом на форуме и я постараюсь в ближайшее время починить.';
					}
				}
			?>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
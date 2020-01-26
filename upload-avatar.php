<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<h3 class="panel-title">Загрузка аватара</h3>
	</div>
	<script>
		document.title = 'Загрузка аватара';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			<?php
				$userid = $_COOKIE['userid'];

				$userid = false;

				if (isset($_COOKIE['userid'])) {
					$userid = htmlspecialchars($_COOKIE['userid']);

					if (!preg_match('/^\{?[0-9a-zA-Z]{1,30}\}?$/', $userid)) {
						$userid = false;
					}
				}

				if ($userid === false) {
					die();
				}

				$uploadOk = 0;
				$imageFileType = '';

				if (isset($_POST['imgUrl'])) {
					$target_dir = "uploads/";
					$img = $_POST['imgUrl'];
					$img = str_replace('data:image/png;base64,', '', $img);
					$img = str_replace(' ', '+', $img);
					$data = base64_decode($img);
					$target_file = $target_dir . 'upload_img.jpg';
					file_put_contents($target_file, $data);
				} else {
					$target_dir = "uploads/";
					$target_file = $target_dir . basename($_FILES["imgInp"]["name"]);
				}

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
				    unlink($target_file);

                    if (file_exists($target_file)) {
                        echo "Коллизия в директории загрузки файлов. Напишите об этом на форуме и я постараюсь в ближайшее время починить.";
                        echo '<br><br>';
                        echo htmlentities($target_file);
                        $uploadOk = 0;
                    }
				}

				// Check file size
				if ($_FILES["imgInp"]["size"] > 5242880 /* 5 MB */) {
					echo "Извините, файл имеет слишком большой размер.";
					$uploadOk = 0;
				}

				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" &&
				   $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG" /*&&
				   $imageFileType != "mp3" &&
				   $imageFileType != "MP3"*/) {
					echo "Извините, только JPG, JPEG, PNG файлы разрешены.";
					$uploadOk = 0;
				}

				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					echo "Загрузка файла не удалась. Напишите об этом на форуме и я постараюсь в ближайшее время починить.";
				// if everything is ok, try to upload file
				} else {
					if (move_uploaded_file($_FILES["imgInp"]["tmp_name"], $target_file)) {

						if (copy($target_file, '/var/www/russiancoders.club/avatars/' . $userid . '.' . $imageFileType) &&
							copy($target_file, '/var/www/russiancoders.club/avatars/' . $userid . '-small.' . $imageFileType)) {
							$image = false;
							$image2 = false;
							$filename = '/var/www/russiancoders.club/avatars/' . $userid . '.' . $imageFileType;
							$filename2 = '/var/www/russiancoders.club/avatars/' . $userid . '-small.' . $imageFileType;

							switch (strtolower($imageFileType)) {
								case 'jpeg':
									$image = imagecreatefromjpeg($filename);
									$image2 = imagecreatefromjpeg($filename2);
									break;
								case 'jpg':
									$image = imagecreatefromjpeg($filename);
									$image2 = imagecreatefromjpeg($filename2);
									break;
								case 'gif':
									$image = imagecreatefromgif($filename);
									$image2 = imagecreatefromgif($filename2);
									break;
								case 'png':
									$image = imagecreatefrompng($filename);
									$image2 = imagecreatefrompng($filename2);
									break;
							}

							if ($image !== false && $image2 !== false) {
								$newName = '/var/www/russiancoders.club/avatars/' . $userid . '.jpg';
								$oldName = '/var/www/russiancoders.club/avatars/' . $userid . $imageFileType;

								$newName2 = '/var/www/russiancoders.club/avatars/' . $userid . '-small.jpg';
								$oldName2 = '/var/www/russiancoders.club/avatars/' . $userid . '-small' . $imageFileType;

								list($width, $height) = getimagesize($filename);
								list($width2, $height2) = getimagesize($filename2);

								$imageResized = imagecreatetruecolor(200, 200);
								$imageResized2 = imagecreatetruecolor(50, 50);

								imagecopyresized($imageResized, $image, 0, 0, 0, 0, 200, 200, $width, $height);
								imagecopyresized($imageResized2, $image2, 0, 0, 0, 0, 50, 50, $width2, $height2);

								imagejpeg($imageResized, $newName);
								imagejpeg($imageResized2, $newName2);
								
								if ($newName != $oldName) {
									unlink($oldName);
								}

								if ($newName2 != $oldName2) {
									unlink($oldName2);
								}

								echo '<br>' . "\r\n";
								echo 'Файл успешно загружен.' . "\r\n";
								echo '<br>' . "\r\n";

								?>
									Возвращаемся в профиль… <p id="return-timer">3</p>

									<script>
										document.addEventListener('DOMContentLoaded', function() {
											var timer = 3;

											function Tick() {
												document.getElementById('return-timer').innerHTML = timer;

												setTimeout(function() {
													--timer;

													if (timer) {
														Tick();
													} else {
														location.href = '/profile/';
													}
												}, 1000);
											}

											Tick();
										});
									</script>
								<?php
							} else {
								echo 'Не удалось создать объект изображения из файла.';
							}
						} else {
							echo 'Не удалось скопировать файл в хранилище.';
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

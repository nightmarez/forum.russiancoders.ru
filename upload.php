<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Профиль</h3>
	</div>
	<script>
		document.title = 'Профиль';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			<?php
				$userid = $_COOKIE['userid'];
				$fileid = generateUserId();

				$target_dir = "uploads/";
				$target_file = $target_dir . basename($_FILES["imgInp"]["name"]);
				$uploadOk = 1;
				$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

				// Check if image file is a actual image or fake image
				if(isset($_POST["imgSubmit"])) {
					$check = getimagesize($_FILES["imgInp"]["tmp_name"]);
					if($check !== false) {
						echo "File is an image - " . $check["mime"] . ".";
						$uploadOk = 1;
					} else {
						echo "File is not an image.";
						$uploadOk = 0;
					}
				}

				// Check if file already exists
				if (file_exists($target_file)) {
					echo "Sorry, file already exists.";
					$uploadOk = 0;
				}

				// Check file size
				if ($_FILES["imgInp"]["size"] > 500000) {
					echo "Sorry, your file is too large.";
					$uploadOk = 0;
				}

				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
					echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					$uploadOk = 0;
				}

				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
				} else {
					if (move_uploaded_file($_FILES["imgInp"]["tmp_name"], $target_file)) {
						echo "The file ". basename($_FILES["imgInp"]["name"]). " has been uploaded.";

						if (!file_exists('/var/www/domains/storage.russiancoders.ru/' . $userid)) {
							mkdir('/var/www/domains/storage.russiancoders.ru/' . $userid);
						}

						if (copy($target_file, '/var/www/domains/storage.russiancoders.ru/' . $userid . '/' . $fileid . '.' . $imageFileType)) {
							echo 'Файл успешно загружен.<br>Для вставки в сообщение, используйте код:<br>[img=' . $fileid . ']';
						}
					} else {
						echo "Sorry, there was an error uploading your file.";
					}
				}
			?>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
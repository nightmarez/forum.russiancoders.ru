<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Загрузка изображения</h3>
	</div>
	<script>
		document.title = 'Загрузка изображения';
	</script>

	<?php
		$userid = false;

		if (isset($_COOKIE['userid'])) {
			$userid = htmlspecialchars($_COOKIE['userid']);

			if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
				$userid = false;
			}
		}
	?>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<?php if ($userid === false) { ?>
						<tr>
							<td>
								<p>Пользователь с указанным идентификатором не найден</p>
							</td>
						</tr>
					<?php } else { ?>
						<?php
							$db = new PdoDb();

							$query =
								'SELECT `login`, `last` FROM `users` WHERE `userid`=:userid LIMIT 0, 1;';

							$req = $db->prepare($query);
							$req->bindParam(':userid', $userid);
							$req->execute();

							while (list($login, $last) = $req->fetch(PDO::FETCH_NUM)) {
						?>
							<tr>
								<td colspan="2">
									<div class="form-group">
										<label>Загрузка изображения</label>
										<form method="POST" action="upload.php" enctype="multipart/form-data">
											<div class="input-group">
												<span class="input-group-btn">
													<span class="btn btn-default btn-file">
														Выбрать… <input type="file" id="imgInp" name="imgInp">
													</span>
												</span>
												<input type="text" class="form-control" readonly style="width: 337px;">
												<span class="input-group-btn">
													<span class="btn btn-default btn-file">
														Загрузить… <input type="submit" id="imgSubmit">
													</span>
												</span>
											</div>
										</form>
										<img id='img-upload'/>
									</div>
								</td>
							</tr>
						<?php
								break;
							}
						?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready( function() {
		$(document).on('change', '.btn-file :file', function() {
		var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [label]);
		});

		$('.btn-file :file').on('fileselect', function(event, label) {
			
			var input = $(this).parents('.input-group').find(':text'),
				log = label;
			
			if( input.length ) {
				input.val(log);
			} else {
				if( log ) alert(log);
			}
		
		});
		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				
				reader.onload = function (e) {
					$('#img-upload').attr('src', e.target.result);
				};
				
				reader.readAsDataURL(input.files[0]);
			}
		}

		$("#imgInp").change(function(){
			readURL(this);
		}); 	
	});
</script>

<?php include_once('footer.php'); ?>
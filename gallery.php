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
								$result[] = $value; 
							} 
						} 
   					} 
   
   					return $result; 
				}

				$files = scanDirectory('/var/www/domains/storage.russiancoders.ru/');

				foreach ($files as $key => $value) {
					echo $value . '<br>';
				}
			?>

		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
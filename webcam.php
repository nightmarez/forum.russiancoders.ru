<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
	<div class="panel-heading">
		<h3 class="panel-title">Веб-камера</h3>
	</div>
	<script>
		document.title = 'Веб-камера';
	</script>

	<div>
		<div style="width: 100%;">
			<video id="video" width="640" height="480" autoplay></video>
		</div>

		<div style="width: 100%;">
			<button class="btn btn-info btn-lg" id="snap">Сфотографировать</button>
		</div>

		<div style="width: 100%;">
			<canvas id="canvas" width="640" height="480"></canvas>
		</div>

		<div style="width: 100%;">
			<button class="btn btn-info btn-lg" id="save" disabled="disabled">Сохранить</button>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
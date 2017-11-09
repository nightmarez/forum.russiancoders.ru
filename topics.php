<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$userid = false;

	if (isset($_GET['userid'])) {
		$userid = htmlspecialchars($_GET['userid']);

		if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
			$userid = false;
		}
	}
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">
			Темы пользователя <?php echo getUserLoginById($userid); ?>
		</h3>
	</div>
	<script>
		document.title = 'Темы пользователя';
	</script>

	<div class="panel-body">
		<div class="table-responsive">
			Not Implemented Yet
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<?php
	$targetid = false;
	$targetLogin = '???';

	if (isset($_GET['userid'])) {
		$id = $_GET['userid'];

		if (isUserIdExists($id)) {
			$targetid = $id;
			$targetLogin = getUserLoginById($targetid);
		}
	}
?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Сообщение пользователю <?php echo $targetLogin; ?></h3>
	</div>

	<div class="panel-body">
		<div class="table-responsive">
			<table class="table">
				<tbody>
					<tr>
						<td>
							<?php if ($targetid === false) { ?>
								Такого пользователя не существует.
							<?php } else { ?>
								Not Implemented Yet
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
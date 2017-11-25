<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<div class="panel panel-primary" style="margin: 20px;">
	<div class="panel-heading">
		<h3 class="panel-title">Сообщения</h3>
	</div>

	<?php
		if (!isLogin()) {
			die();
		}

		$userid = $_COOKIE['userid'];

		$query =
			'SELECT `fromid`, `toid`, `text`, `last` 
			FROM `messages`
			WHERE `fromid`=:userid OR `toid`=:userid ORDER BY `id` ASC;';

		$req = $readydb->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->execute();
	?>

	<div class="panel-body">
		<div class="table-responsive">
			<?php
				while (list($fromid, $toid, $text, $last) = $req->fetch(PDO::FETCH_NUM)) {
			?>
				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-4">
								От: <a href="/user/<?php echo htmlspecialchars($fromid); ?>/"><?php echo getUserLoginById($fromid, $readydb); ?></a>
							</div>
							<div class="col-md-4">
								Кому: <a href="/user/<?php echo htmlspecialchars($toid); ?>/"><?php echo getUserLoginById($toid, $readydb); ?></a>
							</div>
							<div class="col-md-4" style="text-align: right;"><?php echo $last; ?></div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<?php echo filterMessage($text, $userid); ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<input type="button" class="btn btn-primary" value="Ответить" data-id="<?php echo $fromid; ?>">
							</div>
						</div>
					</tbody>
				</table>
			<?php
				}
			?>
		</div>
	</div>
</div>

<?php checkPrivateMessagesAsViewed(); ?>

<?php include_once('footer.php'); ?>
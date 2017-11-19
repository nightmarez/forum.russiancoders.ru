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
		$db = new PdoDb();

		$query =
			'SELECT `fromid`, `toid`, `text`, `last` 
			FROM `messages`
			WHERE `fromid`=:userid OR `toid`=:userid ORDER BY `id` ASC;';

		$req = $db->prepare($query);
		$req->bindParam(':userid', $userid);
		$req->execute();
	?>

	<div class="panel-body">
		<div class="table-responsive">
			<?php
				while (list($fromid, $toid, $text, $last) = $req->fetch(PDO::FETCH_NUM)) {
			?>
				<table class="table topic-posts">
					<tbody>
						<tr>
							<td style="width: 200px;">From: <?php echo getUserLoginById($fromid); ?></td>
							<td style="width: 200px;">To: <?php echo getUserLoginById($toid); ?></td>
							<td><?php echo $last; ?></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4"><?php echo filterMessage($text, $userid); ?></td>
						</tr>
						<tr>
							<td colspan="4">
								<form method="GET" action="/sendmessage.php?userid=<?php echo $fromid; ?>">
									<input type="submit" class="btn btn-primary" value="Ответить">
								</form>
							</td>
						</tr>
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
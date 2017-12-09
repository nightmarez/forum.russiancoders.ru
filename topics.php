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

<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
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
			<?php
				$query =
					'SELECT `topicid`, `userid`, `sectionid`, `title`, `created` 
					FROM `topics` 
					WHERE `userid`=:userid 
					ORDER BY `id` 
					DESC LIMIT 0, 30;';

				$req = $readydb->prepare($query);
				$req->bindParam(':userid', $userid);
				$req->execute();

				while (list($topicid, $userid, $sectionid, $title, $created) = $req->fetch(PDO::FETCH_NUM)) {
					?>
						<div class="panel panel-info">
							<div class="panel-heading">
								<div class="row">
									<div class="col-md-10">
										<?php
											$topicTitle = getTopicTitleById($topicid, $readydb);
											$sectionid = getSectionIdByTopicId($topicid, $readydb);
											$sectionTitle = getSectionTitleById($sectionid, $readydb);
										?>
										<a href="/">Форум</a>
										→
										<a href="/section/<?php echo $sectionid; ?>/"><?php echo $sectionTitle; ?></a>
										→
										<a href="/topic/<?php echo $topicid; ?>/"><?php echo $topicTitle; ?></a>
									</div>
									<div class="col-md-2" style="text-align: right;"><?php echo $created; ?></div>
								</div>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<?php echo filterMessage(getTopicInitMessage($topicid, $readydb), $userid); ?>
									</div>
								</div>
							</div>
						</div>
					<?php
				}
			?>
		</div>
	</div>
</div>

<?php include_once('footer.php'); ?>
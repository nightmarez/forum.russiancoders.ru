<?php include_once('db.php'); ?>

<?php
	$groupby = NULL;

	if (isset($_GET['groupby'])) {
		$groupby = htmlspecialchars($_GET['groupby']);

		switch ($groupby) {
			case 'mins':
				$query = 'SELECT `BTC`, `LTC`, `XRP`, `BCH`, `ETH` FROM `coins` ORDER BY `id` DESC LIMIT 60;';
				break;

			case 'hrs':
				$query = 
					'SELECT ANY_VALUE(`BTC`), ANY_VALUE(`LTC`), ANY_VALUE(`XRP`), ANY_VALUE(`BCH`), ANY_VALUE(`ETH`) FROM
					 ((SELECT * FROM `coins` WHERE `stamp` > DATE_SUB(NOW(), INTERVAL 1 DAY)) AS t)
					 GROUP BY HOUR(`stamp`);';
				break;

			case 'days':
				$query = 
					'SELECT ANY_VALUE(`BTC`), ANY_VALUE(`LTC`), ANY_VALUE(`XRP`), ANY_VALUE(`BCH`), ANY_VALUE(`ETH`) FROM 
					 ((SELECT * FROM `coins` WHERE `stamp` > DATE_SUB(NOW(), INTERVAL 1 MONTH)) AS t)
					 GROUP BY DAY(`stamp`)';
				break;

			default:
				die();
		}
	}

	$db = new PdoDb();
	$req = $db->prepare($query);
	$req->execute();

	$arr = array();

	while (list($BTC, $LTC, $XRP, $BCH, $ETH) = $req->fetch(PDO::FETCH_NUM)) {
		$arr[] = $BTC . '&' . $LTC . '&' . $XRP . '&' . $BCH . '&' . $ETH;
	}

	$arr = array_reverse($arr);
	echo json_encode($arr);
?>
<?php include_once('utils.php'); ?>

<?php
	function httpGet($url) {
		if ($curl = curl_init()) {
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$out = curl_exec($curl);
			curl_close($curl);
			return $out;
		}

		return NULL;
	}

	// bitfinex
	$priceBTC0 = str_replace(',', '.', json_decode(httpGet('https://api.bitfinex.com/v2/ticker/tBTCUSD'))[0]);
	$priceLTC0 = str_replace(',', '.', json_decode(httpGet('https://api.bitfinex.com/v2/ticker/tLTCUSD'))[0]);
	$priceXRP0 = str_replace(',', '.', json_decode(httpGet('https://api.bitfinex.com/v2/ticker/tXRPUSD'))[0]);
	$priceBCH0 = str_replace(',', '.', json_decode(httpGet('https://api.bitfinex.com/v2/ticker/tBCHUSD'))[0]);
	$priceETH0 = str_replace(',', '.', json_decode(httpGet('https://api.bitfinex.com/v2/ticker/tETHUSD'))[0]);

	// bittrex
	$priceBTC1 = str_replace(',', '.', json_decode(httpGet('https://bittrex.com/api/v1.1/public/getticker?market=USDT-BTC'))->result->Last);
	$priceLTC1 = str_replace(',', '.', json_decode(httpGet('https://bittrex.com/api/v1.1/public/getticker?market=USDT-LTC'))->result->Last);
	$priceXRP1 = str_replace(',', '.', json_decode(httpGet('https://bittrex.com/api/v1.1/public/getticker?market=USDT-XRP'))->result->Last);
	$priceBCH1 = str_replace(',', '.', json_decode(httpGet('https://bittrex.com/api/v1.1/public/getticker?market=USDT-BCC'))->result->Last);
	$priceETH1 = str_replace(',', '.', json_decode(httpGet('https://bittrex.com/api/v1.1/public/getticker?market=USDT-ETH'))->result->Last);

	// poloniex
	$poloniex = json_decode(httpGet('https://poloniex.com/public?command=returnTicker'));

	$priceBTC2 = str_replace(',', '.', $poloniex->USDT_BTC->last);
	$priceLTC2 = str_replace(',', '.', $poloniex->USDT_LTC->last);
	$priceXRP2 = str_replace(',', '.', $poloniex->USDT_XRP->last);
	$priceBCH2 = str_replace(',', '.', $poloniex->USDT_BCH->last);
	$priceETH2 = str_replace(',', '.', $poloniex->USDT_ETH->last);

	$db = new PdoDb();
	$query = 'INSERT INTO `coins` (`BTC`, `LTC`, `XRP`, `BCH`, `ETH`) VALUES (:btc, :ltc, :xrp, :bch, :eth);';
	$req = $db->prepare($query);

	$priceBTC = $priceBTC0 . '|' . $priceBTC1 . '|' . $priceBTC2;
	$priceLTC = $priceLTC0 . '|' . $priceLTC1 . '|' . $priceLTC2;
	$priceXRP = $priceXRP0 . '|' . $priceXRP1 . '|' . $priceXRP2;
	$priceBCH = $priceBCH0 . '|' . $priceBCH1 . '|' . $priceBCH2;
	$priceETH = $priceETH0 . '|' . $priceETH1 . '|' . $priceETH2;

	$req->bindParam(':btc', $priceBTC);
	$req->bindParam(':ltc', $priceLTC);
	$req->bindParam(':xrp', $priceXRP);
	$req->bindParam(':bch', $priceBCH);
	$req->bindParam(':eth', $priceETH);
	$req->execute();
?>
<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>
		<div class="container" style="padding-left: 0;">
			<div class="panel panel-primary" style="margin: 20px 0 20px 0;">
				<div class="panel-heading">
					<h3 class="panel-title">Crypto Currencies</h3>
				</div>

				<div class="panel-body">
					<div>
						<canvas id="chart-container" />
					</div>

					<div style="float: left; width: 100%;">
						<div style="float: left; width: 50%;">
							<div style="float: left; width: 100%;">
								<div class="checkbox" id="cbBTC" style="float: left; margin-right: 10px; margin-top: 10px;">
									<label>
										<input type="radio" checked="checked" name="cryptocurrency" value="0"> BTC
									</label>
								</div>

								<div class="checkbox" id="cbLTC" style="float: left; margin-right: 10px; margin-top: 10px;">
									<label>
										<input type="radio" name="cryptocurrency" value="1"> LTC
									</label>
								</div>

								<div class="checkbox" id="cbXRP" style="float: left; margin-right: 10px; margin-top: 10px;">
									<label>
										<input type="radio" name="cryptocurrency" value="2"> XRP
									</label>
								</div>

								<div class="checkbox" id="cbBCH" style="float: left; margin-right: 10px; margin-top: 10px;">
									<label>
										<input type="radio" name="cryptocurrency" value="3"> BCH
									</label>
								</div>

								<div class="checkbox" id="cbETH" style="float: left; margin-right: 10px; margin-top: 10px;">
									<label>
										<input type="radio" name="cryptocurrency" value="4"> ETH
									</label>
								</div>
							</div>

							<div style="float: left; width: 100%; margin-left: 20px;">
								<form>
									<div class="checkbox" id="cbBitFinex" style="float: left; margin-right: 10px; margin-top: 10px;">
										<label>
											<input type="checkbox" value="0" checked="checked"> BitFinex
										</label>
									</div>

									<div class="checkbox" id="cbBitTrex" style="float: left; margin-right: 10px; margin-top: 10px;">
										<label>
											<input type="checkbox" value="1" checked="checked"> BitTrex
										</label>
									</div>

									<div class="checkbox" id="cbPoloniex" style="float: left; margin-right: 10px; margin-top: 10px;">
										<label>
											<input type="checkbox" value="2" checked="checked"> Poloniex
										</label>
									</div>

									<div class="checkbox" id="cbAverage" style="float: left; margin-right: 10px; margin-top: 10px;">
										<label>
											<input type="checkbox" value="3"> Среднее значение
										</label>
									</div>
								</form>
							</div>
						</div>
						<div style="float: left; width: 49%">
							<div style="float: left; width: 100%;">
								<div class="dropdown">
									<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">
										<span id="time">Минуты</span>
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
										<li><a href="#" onclick="window.cryptoperiod = 'mins'; window.redrawChart(); $('#time').text('Минуты'); return false;">Минуты</a></li>
										<li><a href="#" onclick="window.cryptoperiod = 'hrs'; window.redrawChart(); $('#time').text('Часы'); return false;">Часы</a></li>
										<li><a href="#" onclick="window.cryptoperiod = 'days'; window.redrawChart(); $('#time').text('Дни'); return false;">Дни</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script src="/crypto.js?rnd=99" defer></script>
<?php include_once('footer.php'); ?>
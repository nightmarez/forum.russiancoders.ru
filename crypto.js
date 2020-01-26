$(document).ready(function() {
	var canvas = $('#chart-container')[0];
	var ctx = canvas.getContext('2d');

	var randomScalingFactor = function() {
		return Math.ceil(Math.random() * 100);
	};

	var uuidv4 = function() {
		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
			var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
			return v.toString(16);
		});
	};

	window.cryptoperiod = 'mins';

	var getValues = function(callback) {
		$.get('/getcryptodata.php?groupby=' + window.cryptoperiod + '&rnd=' + uuidv4(), function(result) {
			result = JSON.parse(result);

			result = _.map(result, function(str) {
				return str.split('&');
			});

			for (var i = 0; i < result.length; ++i) {
				for (var j = 0; j < result[i].length; ++j) {
					result[i][j] = result[i][j].split('|');

					for (var k = 0; k < result[i][j].length; ++k) {
						result[i][j][k] = parseFloat(result[i][j][k]);
					}
				}
			}

			callback(result);
		});
	};

	getValues(function(result) {
		window.config = {
			type: 'line',
			data: {
				labels: _.map(_.range(60), function(val) { return val - 60; }),
				datasets: [{
					label: "BitFinex",
					backgroundColor: 'rgb(255, 99, 132)',
					borderColor: 'rgb(255, 99, 132)',
					data: _.map(_.map(result, function(r) { return r[0]; }), function(r) { return r[0]; }),
					fill: false,
					hidden: false
				}, {
					label: "BitTrex",
					fill: false,
					hidden: false,
					backgroundColor: 'rgb(54, 162, 235)',
					borderColor: 'rgb(54, 162, 235)',
					data: _.map(_.map(result, function(r) { return r[0]; }), function(r) { return r[1]; }),
				}, {
					label: "Poloniex",
					fill: false,
					hidden: false,
					backgroundColor: 'rgb(54, 235, 162)',
					borderColor: 'rgb(54, 235, 162)',
					data: _.map(_.map(result, function(r) { return r[0]; }), function(r) { return r[2]; })
				}, {
					label: "Среднее значение",
					fill: false,
					hidden: true,
					backgroundColor: 'rgb(128, 128, 128)',
					borderColor: 'rgb(128, 128, 128)',
					data: _.map(_.map(result, function(r) { return r[0]; }), function(r) { return (r[0] + r[1] + r[2]) / 3; })
				}]
			},
			options: {
				responsive: true,
				title:{
					display:true,
					text: 'Динамика стоимости криптовалют'
				},
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Минуты'
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Стоимость в долларах США'
						}
					}]
				}
			}
		};

		window.myChart = new Chart(ctx, window.config);
	});

	var exchange = [0, 1, 2];
	var currency = 0;

	var redrawChart = function() {
		exchange = [];

		$('input[type=checkbox]:checked').each(function(i, c) {
			exchange.push($(c).val());
		});

		currency = $('input[type=radio]:checked').val();

		getValues(function(result) {
			var idx = 0;
			var label = '';
			var count = 0;

			switch (window.cryptoperiod) {
				case 'mins':
					label = 'Минуты';
					count = 60;
					break;

				case 'hrs':
					label = 'Часы';
					count = 24;
					break;

				case 'days':
					label = 'Дни';
					count = 31;
					break;
			}

			window.config.data.datasets.forEach(function(dataset) {
				if (_.indexOf(exchange, idx.toString()) !== -1) {
					dataset.hidden = false;

					if (idx == 3) {
						dataset.data = _.map(_.map(result, function(r) { return r[currency]; }), function(r) { return (r[0] + r[1] + r[2]) / 3; });
						count = Math.min(count, dataset.data.length);
					} else {
						dataset.data = _.map(_.map(result, function(r) { return r[currency]; }), function(r) { return r[idx]; });
						count = Math.min(count, dataset.data.length);
					}
				} else {
					dataset.hidden = true;
				}

				++idx;
			});

			if (window.config.data.datasets.length) {
				
			}

			window.config.options.scales.xAxes[0].scaleLabel.labelString = label;
			window.config.data.labels = _.map(_.range(count), function(val) { return val - count; });

			window.myChart.update();
		});
	};

	window.redrawChart = redrawChart;

	$(':checkbox').change(function() {
		redrawChart();
	});

	$(':radio').change(function() {
		redrawChart();
	});

	setInterval(function() {
		redrawChart();
	}, 1000 * 60);
});
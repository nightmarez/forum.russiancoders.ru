function uuidv4() {
	var timeStampInMs =
		window.performance &&
		window.performance.now &&
		window.performance.timing &&
		window.performance.timing.navigationStart ? window.performance.now() + window.performance.timing.navigationStart : Date.now();

	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
		var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
		return v.toString(16) + '-' + timeStampInMs;
	});
}

$(document).ready(function() {
	var ping = function() {
		setTimeout(function() {
			$.get('https://forum.russiancoders.ru/ping.php?uniq=' + uuidv4(), function(data) {
				data = JSON.parse(data);

				if (data.ok) {
					ping();
				}
			});
		}, 60000);
	};

	ping();
});
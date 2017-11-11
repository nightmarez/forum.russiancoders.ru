$(document).ready(function() {
	var ping = function() {
		setTimeout(function() {
			$.get('https://forum.russiancoders.ru/ping.php');
		}, 60000);
	};

	ping();
});
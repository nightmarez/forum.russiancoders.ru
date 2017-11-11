function uuidv4() {
	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
		var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
		return v.toString(16);
	});
}

$(document).ready(function() {
	var ping = function() {
		setTimeout(function() {
			$.get('https://forum.russiancoders.ru/ping.php?uniq=' + uuidv4());
			ping();
		}, 60000);
	};

	ping();
});
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

var trim = function(string) {
	return string.replace(/(^\s+)|(\s+$)/g, "");
};

var decToHex = function(dec) {
	var result = parseInt(dec.toString()).toString(16);

	if (result.length == 1) {
		result = "0".concat(result);
	}

	return result;
};

var createGrayscaleArray = function() {
	var colors = [];

	for (var i = 0xf; i >= 0; i--) {
		var hexString = i.toString(16);
		if (hexString.length == 1) hexString = hexString.concat(hexString);
		colors[0xf - i] = "#".concat(hexString, hexString, hexString);
	}

	return colors;
};

var hue = function(P, Q, h) {
	if (h < 0.0) h += 1.0;
	if (h > 1.0) h -= 1.0;
	if (h * 6.0 < 1.0) return P + (Q - P) * h * 6.0;
	if (h * 2.0 < 1.0) return Q;
	if (h * 3.0 < 2.0) return P + (Q - P) * (2.0 / 3.0 - h) * 6.0;
	return P;
};

var HSLToRGB = function(h, s, l) {
	var r, g, b;
	var Q, P;

	if (s == 0.0) {
		r = g = b = 0.0;
	}
	else {
		Q = l < 0.5 ? l * (s + 1.0) : l + s - (l * s);
		P = l * 2.0 - Q;
		r = hue(P, Q, h + 1.0 / 3.0);
		g = hue(P, Q, h);
		b = hue(P, Q, h - 1.0 / 3.0);
	}

	return [r * 255, g * 255, b * 255];
};

var createHueArray = function() {
	var colors = [];

	for (var i = 0; i < 360; i++) {
		var color = HSLToRGB(i / 360.0, 1.0, 0.5);

		colors[i] = "#".concat(
			decToHex(color[0]),
			decToHex(color[1]),
			decToHex(color[2]));
	}

	return colors;
};

$(document).ready(function() {
	var ping = function() {
		setTimeout(function() {
			$.get('https://forum.russiancoders.ru/ping.php?uniq=' + uuidv4(), function(data) {
				//data = JSON.parse(data);

				//if (data.ok) {
					ping();
				//}
			});
		}, 60000);
	};

	ping();
});

$(document).ready(function() {
	$('input[type=submit]').click(function(e) {
		$(this).attr('disabled', true);
		e.stopPropagation();
		$('form').submit();
		return false;
	});
});

$(document).ready(function() {
	if (location.href.indexOf('/topic/') !== -1) {
		$('html, body').animate({ scrollTop: $(document).height() }, 'fast');

		$('#upload-image-btn').click(function() {
			window.open('/uploader/');
		});

		$('.triangle-up').click(function() {
			var self = $(this);

			if (!self.hasClass('triangle-up-disabled')) {
				var id = parseInt(self.attr('data-id'));
				var userid = self.attr('data-userid');

				$.get('/vote.php?id=' + id + '&userid=' + userid, function(result) {
					result = JSON.parse(result);

					if (result.ok === true) {
						self.addClass('triangle-up-disabled');
						self.parent().find('.triangle-down').addClass('triangle-down-disabled');
						self.parent().find('.likes-counter').text(result.count);
					} else {
						console.log('Voting error: ' + result.reason);
					}
				});
			}
		});
	}
});

$(document).ready(function() {
	if (location.href.indexOf('/uploader/') !== -1) {
		var self = $('input[type=file]')[0];
		var input = $(self),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [label]);

		$('.btn-file :file').on('fileselect', function(event, label) {
			var input = $(self).parents('.input-group').find(':text'),
				log = label;
			
			if( input.length ) {
				input.val(log);
			} else {
				if( log ) alert(log);
			}
		});

		var readURL = function(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				
				reader.onload = function (e) {
					$('#img-upload').attr('src', e.target.result);
				};
				
				reader.readAsDataURL(input.files[0]);
			}
		};

		$("#imgInp").change(function(){
			readURL(self);
		});
	}
});

$(document).ready(function() {
	$('#btn-search').click(function() {
		$(this).parent().parent().find('form').submit();
	});
});

$(document).ready(function() {
	function testLogin() {
		return true;
	}

	function testMail() {
		return true;
	}

	function testPasswords() {
		return $('#pass1').val() == $('#pass2').val();
	}

	$(document).ready(function() {
		$('#submit-button').click(function() {
			if (testLogin() && testMail() && testPasswords()) {
				$('form').submit();
			}
		});
	});
});
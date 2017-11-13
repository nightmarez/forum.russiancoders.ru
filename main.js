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
	if (location.href.indexOf('/topic/') !== -1) {
		$('html, body').animate({ scrollTop: $(document).height() }, 'fast');
	}
});

$(document).ready(function() {
	if (location.href.indexOf('/uploader/') !== -1) {
		$(document).on('change', '.btn-file :file', function() {
			var input = $(this),
				label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
			input.trigger('fileselect', [label]);
		});

		$('.btn-file :file').on('fileselect', function(event, label) {
			var input = $(this).parents('.input-group').find(':text'),
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
			readURL(this);
		});
	}
});
$(document).ready(function() {
	var ws = false;
	var uid = false;

	var createSocket = function() {
		return new WebSocket('ws://195.161.114.157:9339');
	};

	var connect = function() {
		ws = createSocket();

		ws.onmessage = function (message) {
			var data = JSON.parse(message.data);

			if (data.action == 'connected') {
				uid = data.uid;
				$('#search-container').find('p').text('Поиск собеседника...');
				$('.messages').empty();
			} else if (data.action == 'finded') {
				$('#search-container').css('display', 'none');
				$('#chat-container').css('display', 'block');
				$('#sendmessage-container').css('display', 'block');
			} else if (data.action == 'disconnect') {
				$('#search-container').css('display', 'block');
				$('#chat-container').css('display', 'none');
				$('#sendmessage-container').css('display', 'none');
				$('#search-container').find('p').text('Поиск собеседника...');
				$('.messages').empty();

				ws.close();

				setTimeout(function() {
					connect();
				}, 1000);
			} else if (data.action == 'message') {
				getMessage(data.content);
			}
		};

		ws.onerror = function() {
			$('#search-container').css('display', 'block');
			$('#chat-container').css('display', 'none');
			$('#sendmessage-container').css('display', 'none');
			$('#search-container').find('p').text('Поиск собеседника...');
			$('.messages').empty();

			setTimeout(function() {
				connect();
			}, 1000);
		};

		ws.onclose = function() {
			$('#search-container').css('display', 'block');
			$('#chat-container').css('display', 'none');
			$('#sendmessage-container').css('display', 'none');
			$('#search-container').find('p').text('Поиск собеседника...');
			$('.messages').empty();

			ws.close();

			setTimeout(function() {
				connect();
			}, 1000);
		};
	};

	setTimeout(function() {
		connect();
	}, 1000);

	var getMessage = function(message) {
		var li, wrapper;
		li = $(document.createElement('li')).addClass('message').addClass('left').addClass('appeared');
		li.append($(document.createElement('div')).addClass('avatar'));
		wrapper = $(document.createElement('div')).addClass('text_wrapper');
		wrapper.append($(document.createElement('div')).addClass('text').text(message));
		li.append(wrapper);
		$('.messages').append(li);
		$('html, body').animate({ scrollTop: $(document).height() }, 'slow');
	};

	var addMessage = function(message) {
		var li, wrapper;
		li = $(document.createElement('li')).addClass('message').addClass('right').addClass('appeared');
		li.append($(document.createElement('div')).addClass('avatar'));
		wrapper = $(document.createElement('div')).addClass('text_wrapper');
		wrapper.append($(document.createElement('div')).addClass('text').text(message));
		li.append(wrapper);
		$('.messages').append(li);
		$('html, body').animate({ scrollTop: $(document).height() }, 'slow');
	};

	var sendMessage = function() {
		var message = $('.input-lg').val();

		if (ws.readyState == 1) {
			ws.send(JSON.stringify({
				action: 'message',
				uid: uid,
				content: message
			}));

			addMessage(message);
			$('.input-lg').val('');
		}
	};

	$('.input-lg').keypress(function (e) {
		if (e.which == 13) {
			sendMessage();
		}
	});

	$('#btnsend').click(function() {
		sendMessage();
	});
});
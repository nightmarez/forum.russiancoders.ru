(function() {
	var WebSocketServer = require('ws').Server,
		wss = new WebSocketServer({ port: 9339 });
	console.log('server start');

	var uuidv4 = function() {
		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
			var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
			return v.toString(16);
		});
	};

	var usersFree = [];
	var usersPairs = [];

	var getWsByUid = function(uid) {
		for (var i = 0; i < usersFree.length; ++i) {
			if (usersFree[i].uid == uid) {
				return usersFree[i].ws;
			}
		}

		return false;
	};

	var makePairs = function() {
		var any, i, u1, u2;
		console.log('make pairs');

		for (i = 0; i < usersFree.length; ++i) {
			if (usersFree[i].readyState < 2) {
				usersFree.splice(i--, 1);
			}
		}

		for (i = 0; i < usersPairs.length; ++i) {
			u1 = usersPairs[i].first;
			u2 = usersPairs[i].second;

			if (u1.readyState > 1 || u2.readyState > 1) {
				usersPairs.splice(i--, 1);

				if (u1.readyState < 2) {
					u1.ws.send(JSON.stringify({
						action: 'disconnect'
					}));

					u1.ws.close();
				}

				if (u2.readyState < 2) {
					u2.ws.send(JSON.stringify({
						action: 'disconnect'
					}));

					u2.ws.close();
				}
			}
		}

		do {
			any = false;

			for (i = 0; i < usersFree.length; ++i) {
				any = createPair(usersFree[i].uid);
			}
		} while (any);

		console.log('free users count: ' + usersFree.length);
		console.log('pairs count: ' + usersPairs.length);
	};

	var createPair = function(uid) {
		for (var i = 0; i < usersFree.length; ++i) {
			if (usersFree[i].uid != uid) {
				var second = usersFree[i];
				usersFree.splice(i--, 1);

				var ws = getWsByUid(uid);

				var pair = {
					first: {
						uid: uid,
						ws: ws
					},
					second: {
						uid: second.uid,
						ws: second.ws
					}
				};

				usersPairs.push(pair);

				pair.first.ws.send(JSON.stringify({
					action: 'finded'
				}));

				pair.second.ws.send(JSON.stringify({
					action: 'finded'
				}));

				for (var j = 0; j < usersFree.length; ++j) {
					if (usersFree[j].uid == uid) {
						usersFree.splice(j--, 1);
						break;
					}
				}

				console.log('chat established: ' + uid + ' + ' + second.uid);
				return true;
			}
		}

		return false;
	};

	wss.on('connection', function (ws) {
		var i, j, u1, u2;
		var uid = uuidv4();
		console.log('connection: ' + uid);

		ws.send(JSON.stringify({
			action: 'connected',
			uid: uid
		}));

		ws.on('close', function() {
			makePairs();
		});

		ws.on('message', function (message) {
			var data = JSON.parse(message);

			if (data.action == 'close') {
				for (i = 0; i < usersFree.length; ++i) {
					if (usersFree[i].uid == uid) {
						usersFree[i].ws.close();
						console.log('disconnected: ' + usersFree[i].uid);
						usersFree.splice(i--, 1);
						break;
					}
				}
			} else if (data.action == 'message') {
				for (i = 0; i < usersPairs.length; ++i) {
					var pair = usersPairs[i];

					try
					{
						if (pair.first.uid == data.uid) {
							pair.second.ws.send(JSON.stringify({
								action: 'message',
								content: data.content
							}));

							console.log('message ' + pair.first.uid + ' -> ' + pair.second.uid);
							break;
						}

						if (pair.second.uid == data.uid) {
							pair.first.ws.send(JSON.stringify({
								action: 'message',
								content: data.content
							}));

							console.log('message ' + pair.second.uid + ' -> ' + pair.first.uid);
							break;
						}
					} catch(e) {
						u1 = pair.first;
						u2 = pair.second;

						if (u1.readyState == 1) {
							u1.ws.send(JSON.stringify({
								action: 'disconnect'
							}));

							console.log('disconnect ' + u1.uid);
							u1.ws.close();
						}

						if (u2.readyState == 1) {
							u2.ws.send(JSON.stringify({
								action: 'disconnect'
							}));

							console.log('disconnect ' + u2.uid);
							u2.ws.close();
						}

						usersPairs.splice(i, 1);
						console.log('pairs count: ' + usersPairs.length);
					}
				}
			}
		});

		usersFree.push({
			uid: uid,
			ws: ws
		});

		makePairs();
	});
})();
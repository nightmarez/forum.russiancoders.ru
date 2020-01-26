;(function(document) {
	var uuidv4 = function() {
		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
			var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
			return v.toString(16);
		});
	};

	if (!String.prototype.trim) {
		(function() {
			String.prototype.trim = function() {
				return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
			};
		})();
	}

	var decToHex = function(dec) {
		var result = parseInt(dec.toString(), 10).toString(16);

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
				$.get('/ping.php?uniq=' + uuidv4(), function(data) {
					//data = JSON.parse(data);
					//if (data.ok) {
						ping();
					//}
				});
			}, 60000);
		};

		ping();
	});

	// $(document).ready(function() {
	// 	// prevent double clicks
	// 	$('input[type=submit]').click(function(e) {
	// 		if ($(this).parent().prop('tagName') == 'FORM') {
	// 			$(this).attr('disabled', true);
	// 			e.stopPropagation();
	// 			$(this).parent().submit();
	// 			return false;
	// 		}
	// 	});

	$('#upload-image-btn').click(function() {
		window.open('/uploader/');
	});

	// 	if (location.href.indexOf('/unset/') === -1) {
	// 		// save cookies to localStorage
	// 		var cookies = _.map(document.cookie.split(';'), function(cookie) { return cookie.trim().split('='); });
	// 		var names = ['userid', 'session'];

	// 		_.each(names, function(name) {
	// 			if (_.isNull(localStorage.getItem(name))) {
	// 				_.each(cookies, function(kvp) {
	// 					if (kvp[0] == name) {
	// 						localStorage.setItem(name, kvp[1]);
	// 					}
	// 				});
	// 			}
	// 		});

	// 		// restore cookies from localStorage
	// 		var date = new Date(new Date().getTime() + 1000 * 60 * 60 * 24 * 30);
	// 		_.each(names, function(name) {
	// 			if (!_.isNull(localStorage.getItem(name))) {
	// 				document.cookie = name + '=' + localStorage.getItem(name) + '; path=/; expires=' + date.toUTCString();
	// 			}
	// 		});
	// 	}
	// });

	// $(document).ready(function() {
	// 	if (location.href.indexOf('/unset/') !== -1) {
	// 		var names = ['userid', 'session'];

	// 		// remove data from localstorage
	// 		_.each(names, function(name) {
	// 			if (!_.isNull(localStorage.getItem(name))) {
	// 				localStorage.removeItem(name);
	// 			}
	// 		});

	// 		// remove data from cookies
	// 		var date = new Date(0);
	// 		_.each(names, function(name) {
	// 			document.cookie = name + '=; path=/; expires=' + date.toUTCString();
	// 		});

	// 		// redirect
	// 		location.href = '/';
	// 	}
	// });

	$(document).ready(function() {
		if (location.href.indexOf('/tracker') !== -1) {
			$('iframe').add('img').each(function(rowIdx, row) {
				$(row).children().each(function(childIdx, child) {
					child = $(child);

					if (child.position().top > 800) {
						child.remove();
					}
				});

				//if ($(row).height() < 750) {
				//	$('.tracker-gradient').css('display', 'none');
				//}
			});
		}
	});

	$(document).ready(function() {
		if (location.href.indexOf('/topic') !== -1) {
			var gotoMessage = function() {
				var hashIdx = location.href.indexOf('#');

				if (hashIdx !== -1) {
					var messageId = location.href.substr(hashIdx + 1);

					if (messageId.length) {
						messageId = parseInt(messageId, 10);
						$('html, body').animate({ scrollTop: $('#message' + messageId).position().top }, 'fast');
					}
				}
			};

			gotoMessage();

			window.addEventListener('hashchange', function(e) {
				gotoMessage();
			});

			$('.triangle-up').click(function() {
				var self = $(this);

				if (!self.hasClass('triangle-up-disabled')) {
					var id = parseInt(self.attr('data-id'), 10);
					var userid = self.attr('data-userid');

					(function(self) {
						$.get('/vote.php?id=' + id + '&userid=' + userid + '&value=1', function(result) {
							if (result.answer === true) {
								self.addClass('triangle-up-disabled');
								self.parent().find('.triangle-down').addClass('triangle-down-disabled');
								self.parent().find('.likes-counter').text(result.count);
							} else {
								console.log('Voting error: ' + result.reason);
							}
						});
					})(self);
				}
			});

			$('.triangle-down').click(function() {
				var self = $(this);

				if (!self.hasClass('triangle-down-disabled')) {
					var id = parseInt(self.attr('data-id'), 10);
					var userid = self.attr('data-userid');

					(function(self) {
						$.get('/vote.php?id=' + id + '&userid=' + userid + '&value=-1', function(result) {
							if (result.answer === true) {
								self.addClass('triangle-down-disabled');
								self.parent().find('.triangle-up').addClass('triangle-up-disabled');
								self.parent().find('.likes-counter').text(result.count);
							} else {
								console.log('Voting error: ' + result.reason);
							}
						});
					})(self);
				}
			});

			$('#preview-btn').click(function() {
				var form = $('form');
				form.attr('action', '/preview/');
				form.submit();
			});

			$('img').each(function(idx, img) {
				img = $(img);

				if (img.attr('src').indexOf('gravatar') !== -1) {
					var a = $(img.parent().find('a')[0]);
					img.css('left', (a.position().left + a.width() - 45) + 'px');
				}
			});
		}
	});

	$(document).ready(function() {
		if (location.href.indexOf('/preview/') !== -1) {
			$('#preview-btn').click(function() {
				var form = $('form');
				form.attr('action', '/preview/');
				form.submit();
			});

			$('#cancel-btn').click(function() {
				location.href = '/topic/' + $('input[name=topicid]').val() + '/';
			});
		}
	});

	$(document).ready(function() {
		if (location.href.indexOf('/profile/') !== -1) {
			$('#smiles').change(function() {
				$.get('/switchsmiles.php?value=' + (this.checked ? 'checked' : 'unchecked'));
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
						$('#img-upload').css('background-image', 'url("' + e.target.result + '")');
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
		if (location.href.indexOf('/messages/') !== -1) {
			$('input[type=button].btn-primary').each(function(idx, button) {
				button = $(button);

				(function(button, id) {
					button.click(function() {
						location.href = '/sendmessage/' + id + '/';
					});
				})(button, button.attr('data-id'));
			});
		}

		if (location.href.indexOf('/messages/') !== -1) {
			$('input[type=button].btn-danger').each(function(idx, button) {
				//button = $(button);
				//button.parent().parent().parent().parent().remove();
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

	$(document).ready(function() {
		$('pre code').each(function(i, block) {
			hljs.highlightBlock(block);
		});
	});

	////////////////Config//////////////////////////////
	var showColorPalette = true; // color palette
	var longPalette = true; // long color palette
	var showTags = true; // tags panel
	var showSmiles = true; // smiles panel
	////////////////////////////////////////////////////

	// Browser type
	// 1 - Opera, 2 - Firefox | Chrome
	var browserType;

	var textArea;

	var incorrectBrowserVersion = function() {
		console.log('Unsupported browser version!');
	};

	var isOperaBrowser = function() {
		return browserType == 1;
	};

	var isFirefoxOrChromeBrowser = function() {
		return browserType == 2 || browserType == 3;
	};

	var insertText = function (text) {
		var sa = textArea.selectionStart;

		textArea.value =
			textArea.value.substr(0, sa) +
			text +
			textArea.value.substr(sa, textArea.value.length - sa);

		textArea.selectionEnd =
			textArea.selectionStart = sa + text.length;

		textArea.focus();
	};

	var insertTextD = function (first, last) {
		var start = textArea.selectionStart;
		var end = textArea.selectionEnd;
		var firstPart;

		if (end - start > 0) {
			var s = textArea.value.substr(start, end - start);
			firstPart = textArea.value.substr(0, start) + first + s + last;

			textArea.value =
				firstPart +
				textArea.value.substr(end, textArea.value.length - end);

			textArea.selectionEnd =
				textArea.selectionStart = firstPart.length;

			textArea.focus();
		}
		else {
			var sa = textArea.selectionStart;
			firstPart = textArea.value.substr(0, sa) + first;

			textArea.value =
				firstPart + last +
				textArea.value.substr(sa, textArea.value.length - sa);

			textArea.selectionEnd =
				textArea.selectionStart = firstPart.length;

			textArea.focus();
		}
	};

	var insertTextH = function (first, second, last) {
		var start = textArea.selectionStart;
		var end = textArea.selectionEnd;

		if (end - start > 0) {
			var s = textArea.value.substr(start, end - start);

			var firstPart = textArea.value.substr(0, start) +
				first + s + second + s + last;

			textArea.value =
				firstPart +
				textArea.value.substr(end, textArea.value.length - end);

			textArea.selectionEnd =
				textArea.selectionStart = firstPart.length;

			textArea.focus();
		}
		else {
			insertTextD(first, second + last);
		}
	};

	var createATag = function(innerHTML) {
		var tag = document.createElement('a');

		if (isOperaBrowser())
			tag.style.cursor = "pointer";
		else
			tag.href = 'javascript:void(0);';

		tag.innerHTML = innerHTML;
		return tag;
	};

	var createSmileBtn = function(node, smile, alt) {
		var smileTag = createATag('<img src="' + smile + '" alt="' + alt + '" />');
		smileTag.addEventListener('click', function () { insertText(alt); }, false);
		node.appendChild(smileTag);
	};

	var createColorBtn = function(node, color) {
		sizer = "&nbsp;";
		if (!longPalette)
			sizer = new Array(4).join('&nbsp;');

		var colorTag = createATag('<span style="background-color:' + color + ';">' + sizer + '</span>');
		colorTag.addEventListener('click', function () { insertTextD("[color=" + color + "]", "[/color]"); }, false);
		node.appendChild(colorTag);
	};

	var createTagBtn = function(node, lnk) {
		var span = document.createElement('span');
		span.appendChild(lnk);
		span.innerHTML = /*'[' + */span.innerHTML/* + ']'*/;
		node.appendChild(span);
	};

	var createTagBtnFF = function(node, lnk, fn) {
		var span = document.createElement('span');
		span.appendChild(lnk);
		span.innerHTML = /*'[' + */span.innerHTML/* + ']'*/;
		span.addEventListener('click', fn, false);
		node.appendChild(span);
	};

	var createSpace = function(node) {
		var span = document.createElement('span');
		span.innerHTML = '&nbsp;&nbsp;&nbsp;';
		node.appendChild(span);
	};

	var trim = function(string) {
		return string.replace(/(^\s+)|(\s+$)/g, "");
	};

	var selectEach = function(arr, each) {
		var targetArray = [];
		var j = 0;
		for (var i in arr) {
			j++;
			if (j >= each) {
				targetArray.push(arr[i]);
				j = 0;
			}
		}
		return targetArray;
	};

	var createTagsArray = function() {
		return [
			["https://russiancoders.ru/static/bold.png", "Жирный шрифт", "[b]", "[/b]"],
			["https://russiancoders.ru/static/strike.png", "Перечёркнутый шрифт", "[s]", "[/s]"],
			["https://russiancoders.ru/static/italic.png", "Наклонный шрифт", "[i]", "[/i]"],
			["https://russiancoders.ru/static/underline.png", "Подчёркнутый шрифт", "[u]", "[/u]"],
			[],
			["https://russiancoders.ru/static/h1.png", "H1", "[h1]", "[/h1]"],
			["https://russiancoders.ru/static/h2.png", "H2", "[h2]", "[/h2]"],
			["https://russiancoders.ru/static/h3.png", "H3", "[h3]", "[/h3]"],
			["https://russiancoders.ru/static/h4.png", "H4", "[h4]", "[/h4]"],
			[],
			["https://russiancoders.ru/static/strong.png", "strong", "[strong]", "[/strong]"],
			["https://russiancoders.ru/static/small.png", "small", "[small]", "[/small]"],
			["https://russiancoders.ru/static/sup.png", "Верхний индекс", "[sup]", "[/sup]"],
			["https://russiancoders.ru/static/sub.png", "Нижний индекс", "[sub]", "[/sub]"],
			["https://russiancoders.ru/static/pre.png", "Отформатированный текст", "[pre]", "[/pre]"],
			["https://russiancoders.ru/static/hr.png", "Разделитель", "[hr]"],
			["https://russiancoders.ru/static/p.png", "Параграф", "[p]", "[/p]"],
			["https://russiancoders.ru/static/br.png", "Перенос строки", "[br]"],
			[],
			["https://russiancoders.ru/static/youtube.png", "YouTube", "[youtube=", "]"],
			["https://russiancoders.ru/static/img.png", "Изображение", "[img=", "]"],
			["https://russiancoders.ru/static/url.png", "Ссылка", "[url=", "]", "[/url]"]
		];
	};

	var createTags = function(node) {
		console.log('create tags');

		if (showTags) {
			var tags = createTagsArray(), tag;
			for (var i in tags) {
				(function(tagInfo) {
					if (tagInfo.length == 5) {
						tag = createATag('<img src="' + tagInfo[0] + '" alt="' + tagInfo[1] + '">');
						(function (x) { createTagBtnFF(node, tag, function () { insertTextH(x[2], x[3], x[4]); }); })(tagInfo);
					} else if (tagInfo.length == 4) {
						tag = createATag('<img src="' + tagInfo[0] + '" alt="' + tagInfo[1] + '">');
						(function (x) { createTagBtnFF(node, tag, function () { insertTextD(x[2], x[3]); }); })(tagInfo);
					} else if (tagInfo.length == 3) {
						tag = createATag('<img src="' + tagInfo[0] + '" alt="' + tagInfo[1] + '">');
						(function (x) { createTagBtnFF(node, tag, function () { insertText(x[2]); }); })(tagInfo);
					} else if (tagInfo.length == 0) {
						createSpace(node);
					}
				})(tags[i]);
			}
		}

		if (showColorPalette) {
			var br = document.createElement('br');
			node.appendChild(br);

			if (longPalette) {
				var hues = selectEach(createHueArray(), 2);

				for (var i in hues) {
					createColorBtn(node, hues[i]);
				}

				var grayscales = createGrayscaleArray();

				for (var i in grayscales) {
					createColorBtn(node, grayscales[i]);
				}
			} else {
				var colors = [
					"#000000",
					"#800000",
					"#008000",
					"#808000",
					"#000080",
					"#800080",
					"#008080",
					"#808080",
					"#C0C0C0",
					"#FF0000",
					"#00FF00",
					"#FFFF00",
					"#0000FF",
					"#FF00FF",
					"#00FFFF",
					"#FFFFFF"
				];

				for (var i in colors) {
					createColorBtn(node, colors[i]);
				}
			}
		}

		if (showSmiles) {
			var smiles = [
				["https://russiancoders.ru/static/smile.gif", ":-)"],
				["https://russiancoders.ru/static/wink.gif", ";-)"],
				["https://russiancoders.ru/static/tongue.gif", ":-P"],
				["https://russiancoders.ru/static/sorrow.gif", ":-("],
				["https://russiancoders.ru/static/cry.gif", ":'-("],
				["https://russiancoders.ru/static/amazement.gif", "O_O"],
				["https://russiancoders.ru/static/laugh.gif", ":-D"],
				["https://russiancoders.ru/static/rofl.gif", "[rofl]"],
				["https://russiancoders.ru/static/crazy.gif", "O_o"],
				["https://russiancoders.ru/static/good.gif", "[good]"],
				["https://russiancoders.ru/static/scratch.gif", "[scratch]"],
				["https://russiancoders.ru/static/rtfm.gif", "[rtfm]"],
				["https://russiancoders.ru/static/stop.gif", "[stop]"],
				["https://russiancoders.ru/static/umnik.gif", "[genius]"],
				["https://russiancoders.ru/static/angel.gif", "[angel]"],
				["https://russiancoders.ru/static/love.gif", "[love]"],
				["https://russiancoders.ru/static/idea.gif", "[idea]"],
				["https://russiancoders.ru/static/kill.gif", "[kill]"],
				["https://russiancoders.ru/static/bad.gif", "[bad]"],
				["https://russiancoders.ru/static/smoke.gif", "[smoke]"],
				["https://russiancoders.ru/static/angry.gif", "[angry]"],
				["https://russiancoders.ru/static/devil.gif", "[devil]"],
				["https://russiancoders.ru/static/bomb.gif", "[bomb]"],
				["https://russiancoders.ru/static/yahoo.gif", "[yahoo]"],
				["https://russiancoders.ru/static/dance.gif", "[dance]"],
				["https://russiancoders.ru/static/wall.gif", "[wall]"],
				["https://russiancoders.ru/static/sex.gif", "[sex]"]
			];

			if (showSmiles) {
				var br = document.createElement('br');
				node.appendChild(br);

				for (var i in smiles) {
					createSmileBtn(node, smiles[i][0], smiles[i][1]);
				}
			}
		}
	};

	var initFunc2 = function () {
		var txtAreas = document.getElementsByTagName('textarea');
		if (txtAreas.length > 0) {
			textArea = txtAreas[0];
			var newNode = document.createElement('div');
			createTags(newNode);
			textArea.parentNode.insertBefore(newNode, textArea);
		}
	};

	var initFunc = function () {
		if (document.location.href.indexOf('/topic/') == -1 &&
			document.location.href.indexOf('/preview/') == -1 &&
			document.location.href.indexOf('/sendmessage/') == -1 &&
			document.location.href.indexOf('/createtopic') == -1)
		{
			return;
		}

		var ua = window.navigator.userAgent;
		if (ua.indexOf('Opera') == -1 &&
			ua.indexOf('Firefox') == -1 &&
			ua.indexOf('Chrome') == -1) {

			incorrectBrowserVersion();
			return;
		}
		else if (ua.indexOf('Opera') != -1) {
			browserType = 1;
		}
		else if (ua.indexOf('Firefox') != -1) {
			browserType = 2;
		}
		else {
			browserType = 3;
		}

		var reg, verArr, majorVersion;

		if (isOperaBrowser()) {
			reg = /Opera\/(\d+).(\d+)/;
			verArr = reg.exec(window.navigator.userAgent);
			majorVersion = verArr[1];
			var minorVersion = verArr[2];

			if (majorVersion < 9) {
				incorrectBrowserVersion();
				return;
			}
			else {
				if ((majorVersion == 9) && (minorVersion < 5)) {
					incorrectBrowserVersion();
					return;
				}
			}
		}
		else if (isFirefoxOrChromeBrowser()) {
			if (browserType == 2) {
				reg = /Firefox\/(\d+).(\d+)/;
				verArr = reg.exec(window.navigator.userAgent);
				majorVersion = verArr[1];

				if (majorVersion < 3) {
					incorrectBrowserVersion();
					return;
				}
			}
		}
		else {
			incorrectBrowserVersion();
			return;
		}

		//if (browserType == 1) {
		//	addEventListener('load', function (e) {
				initFunc2();
		//	}, false);
		//}
		//else if (browserType == 2) {
		//	initFunc2();
		//}
	};

	var action = '/doregister.php';
	var formid = '#main-form';
	var checkboxid = '#not-robot';
	var submitButton = '#submit-button';

	$(document).ready(function() {
		$(checkboxid).change(function() {
			if ($(this).is(":checked")) {
				$(formid).attr('action', action);
				$(submitButton).attr('disabled', false);
			} else {
				$(this).prop("checked", true);
			}
		});
	});

	// remove not needed gradient on tracker
	(function() {
		if ($('.tracker-gradient').length) {
			$('.tracker-gradient').each(function(index, value) {
				(function() {
					var gradient = $(value);
					var parent = gradient.parent();
					var row = parent.find('.row');

					if (row.height() <= 800) {
						gradient.remove();
					}
				})(value);
			});
		}
	})();

	// left menu
	$(function() {
		$('.left-navigation-header').click(function() {
			(function(self) {
				let count = $('.left-navigation-header').length - 1;
				let ln = self.attr('id');

				$('.left-navigation-header').each(function(i, v) {
					if ($(v).attr('id') != ln)
					$(v).parent().find('ul').slideUp('fast', function() {
						if (!--count) {
							self.parent().find('ul').slideDown();
						}
					});
				});
			})($(this));

			return true;
		});
	});

	$(function() {
		if ($(window).width() < 800) {
			let zoom = $(window).width() / 800;
			console.log('zoom: ' + zoom);
			$('body').css('zoom', $(window).width() / 800).css('-moz-transform', 'scale(' + zoom + ')').css('-moz-transform-origin', '0 0');
		}
	});

	$(function() {
		$('.cute-post').each(function(i, v) {
			const scissors = $(v);

			(function(sci) {
				sci.click(function() {
					const targetArea = sci.parent().parent().parent().parent().find('.panel-body').find('.col-md-12');
					const selection = window.getSelection();
					const selectedArea = $(selection.baseNode).parent();
					let selectedText = false;

					if (targetArea[0] == selectedArea[0]) {
						selectedText = (selection + '').trim();
					} else {
						selectedText = targetArea.text().trim();
					}

					let parts = selectedText.split('\r\n');
					selectedText = parts;
					parts = [];
					for (let part of selectedText) {
						const p = part.split('\r');

						for (let x of p) {
							parts.push(x.trim());
						}
					}
					selectedText = parts;
					parts = [];
					for (let part of selectedText) {
						const p = part.split('\n');

						for (let x of p) {
							parts.push(x.trim());
						}
					}
					selectedText = parts;
					parts = [];
					for (let part of selectedText) {
						const p = part.split('<br>');

						for (let x of p) {
							parts.push(x.trim());
						}
					}
					selectedText = '';
					for (let part of parts) {
						selectedText += '> ' + part + '\r\n';
					}

					const postHref = sci.parent().parent().parent().parent().find('.panel-heading').find('.col-md-8').find('a').first();
					const userHref = sci.parent().parent().parent().parent().find('.panel-heading').find('.col-md-8').find('a').last();
					const postUrl = postHref.attr('href');
					const userName = userHref.text();

					const result = '[url=' + postUrl + '][b]' + userName + '[/b][/url]\r\n' + selectedText;
					$('textarea').text(result);

					$([document.documentElement, document.body]).animate({
						scrollTop: $('textarea').offset().top
					}, 2000);

					return false;
				});
			})(scissors);
		});
	});

	initFunc();
})(document);
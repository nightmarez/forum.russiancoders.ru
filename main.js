;(function(document) {
	var uuidv4 = function() {
		var timeStampInMs =
			window.performance &&
			window.performance.now &&
			window.performance.timing &&
			window.performance.timing.navigationStart ? window.performance.now() + window.performance.timing.navigationStart : Date.now();

		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
			var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
			return v.toString(16) + '-' + timeStampInMs;
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
					var id = parseInt(self.attr('data-id'));
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
		if (location.href.indexOf('/messages/') !== -1) {
			$('input[type=button]').each(function(idx, button) {
				button = $(button);

				(function(button, id) {
					button.click(function() {
						location.href = '/sendmessage/' + id + '/';
					});
				})(button, button.attr('data-id'));
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
		smileTag.addEventListener('click', function () { insertText("<img src=\"" + smile + "\" alt=\"" + alt + "\" />"); }, false);
		node.appendChild(smileTag);
	};

	var createColorBtn = function(node, color) {
		sizer = "&nbsp;";
		if (!longPalette)
			sizer = new Array(4).join('&nbsp;');

		var colorTag = createATag('<span style="background-color:' + color + ';">' + sizer + '</span>');
		colorTag.addEventListener('click', function () { insertTextD("<span style=\"color: " + color + ";\">", "</span>"); }, false);
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
			["https://gdpanel.nightmarez.net/bold.png", "Жирный шрифт", "[b]", "[/b]"],
			["https://gdpanel.nightmarez.net/strike.png", "Перечёркнутый шрифт", "[s]", "[/s]"],
			["https://gdpanel.nightmarez.net/italic.png", "Наклонный шрифт", "[i]", "[/i]"],
			["https://gdpanel.nightmarez.net/underline.png", "Подчёркнутый шрифт", "[u]", "[/u]"],
			[],
			["https://gdpanel.nightmarez.net/h1.png", "H1", "[h1]", "[/h1]"],
			["https://gdpanel.nightmarez.net/h2.png", "H2", "[h2]", "[/h2]"],
			["https://gdpanel.nightmarez.net/h3.png", "H3", "[h3]", "[/h3]"],
			["https://gdpanel.nightmarez.net/h4.png", "H4", "[h4]", "[/h4]"],
			[],
			["https://gdpanel.nightmarez.net/strong.png", "strong", "[strong]", "[/strong]"],
			["https://gdpanel.nightmarez.net/small.png", "small", "[small]", "[/small]"],
			["https://gdpanel.nightmarez.net/sup.png", "Верхний индекс", "[sup]", "[/sup]"],
			["https://gdpanel.nightmarez.net/sub.png", "Нижний индекс", "[sub]", "[/sub]"],
			["https://gdpanel.nightmarez.net/pre.png", "Отформатированный текст", "[pre]", "[/pre]"],
			["https://gdpanel.nightmarez.net/hr.png", "Разделитель", "[hr]"],
			["https://gdpanel.nightmarez.net/p.png", "Параграф", "[p]", "[/p]"],
			["https://gdpanel.nightmarez.net/br.png", "Перенос строки", "[br]"],
			[],
			["https://gdpanel.nightmarez.net/youtube.png", "YouTube", "[youtube=", "]"],
			["https://gdpanel.nightmarez.net/img.png", "Изображение", "[img=", "]"],
			["https://gdpanel.nightmarez.net/url.png", "Ссылка", "[url=", "]", "[/url]"]
		];
	};

	var createTags = function(node) {
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

				for (var i in hues)
					createColorBtn(node, hues[i]);

				var grayscales = createGrayscaleArray();

				for (var i in grayscales)
					createColorBtn(node, grayscales[i]);
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
				["https://gdpanel.nightmarez.net/smile.gif", ":-)"],
				["https://gdpanel.nightmarez.net/wink.gif", ";-)"],
				["https://gdpanel.nightmarez.net/tongue.gif", ":-P"],
				["https://gdpanel.nightmarez.net/sorrow.gif", ":-("],
				["https://gdpanel.nightmarez.net/cry.gif", ":'-("],
				["https://gdpanel.nightmarez.net/amazement.gif", "O_O"],
				["https://gdpanel.nightmarez.net/laugh.gif", ":-D"],
				["https://gdpanel.nightmarez.net/rofl.gif", "[rofl]"],
				["https://gdpanel.nightmarez.net/crazy.gif", "O_o"],
				["https://gdpanel.nightmarez.net/good.gif", "[good]"],
				["https://gdpanel.nightmarez.net/scratch.gif", "[scratch]"],
				["https://gdpanel.nightmarez.net/rtfm.gif", "[rtfm]"],
				["https://gdpanel.nightmarez.net/stop.gif", "[stop]"],
				["https://gdpanel.nightmarez.net/umnik.gif", "[genius]"],
				["https://gdpanel.nightmarez.net/angel.gif", "[angel]"],
				["https://gdpanel.nightmarez.net/love.gif", "[love]"],
				["https://gdpanel.nightmarez.net/idea.gif", "[idea]"],
				["https://gdpanel.nightmarez.net/kill.gif", "[kill]"],
				["https://gdpanel.nightmarez.net/bad.gif", "[bad]"],
				["https://gdpanel.nightmarez.net/smoke.gif", "[smoke]"],
				["https://gdpanel.nightmarez.net/angry.gif", "[angry]"],
				["https://gdpanel.nightmarez.net/devil.gif", "[devil]"],
				["https://gdpanel.nightmarez.net/bomb.gif", "[bomb]"],
				["https://gdpanel.nightmarez.net/yahoo.gif", "[yahoo]"],
				["https://gdpanel.nightmarez.net/dance.gif", "[dance]"],
				["https://gdpanel.nightmarez.net/wall.gif", "[wall]"],
				["https://gdpanel.nightmarez.net/sex.gif", "[sex]"]
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
		var areatags = document.getElementById('areatags');
		if (areatags)
			areatags.parentNode.removeChild(areatags);

		var txtAreas = document.getElementsTagName('textarea');
		if (txtAreas.length > 0) {
			textArea = txtAreas[0];
			var newNode = document.createElement('div');
			createTags(newNode);
			textArea.parentNode.insertBefore(newNode, textArea);
		}
	};

	var initFunc = function () {
		if (document.location.href.indexOf('/topic/') == -1 &&
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

		if (browserType == 1) {
			addEventListener('load', function (e) {
				initFunc2();
			}, false);
		}
		else if (browserType == 2) {
			initFunc2();
		}
	};

	initFunc();
})(document);
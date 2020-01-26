<?php include_once('head.php'); ?>

<br><br>
<p>Примеры полей:</p>
<br>
<input type="text">
<br><br>
<input type="text">
<br><br>
<input type="text">
<br><br>
<p>JavaScript код:</p>

<pre><code class="js hljs">// вешаем обработчик события на завершение загрузки всей страницы
document.addEventListener("DOMContentLoaded", function() {
	// выбираем все текстовые поля
	var elements = document.querySelectorAll('input[type="text"]');

	// проходим по всем текстовым полям циклом
	for (var i = 0; i < elements.length; ++i) {
		// берём отдельное текстовое поле...
		var element = elements[i];

		// убираем стандартную подсветку элемента
		element.style.outline = "none";

		(function(element) {
			// ...и вешаем на нажатие клавиши клавиатуры в нём обработку
			element.onkeyup = function() {
				// текст в текстовом поле
				var value = this.value;

				// если не получилось из строки сделать число,
				// обводим текстовое поле красной рамкой,
				// иначе - подсвечиваем зелёным
				if (parseInt(value).toString() != value) {
					this.style.border = "1px solid red";
				} else {
					this.style.border = "1px solid green";
				}
			};
		})(element);
	}
});</code></pre>

<script>
document.addEventListener("DOMContentLoaded", function() {
	var elements = document.querySelectorAll('input[type="text"]');

	for (var i = 0; i < elements.length; ++i) {
		var element = elements[i];
		element.style.outline = "none";

		(function(element) {
			element.onkeyup = function() {
				var value = this.value;

				if (parseInt(value).toString() != value) {
					this.style.border = "1px solid red";
				} else {
					this.style.border = "1px solid green";
				}
			};
		})(element);
	}

	document.querySelectorAll('.footer')[0].style.display = 'none';	
});
</script>

<?php include_once('footer.php'); ?>
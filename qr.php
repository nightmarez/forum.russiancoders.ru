<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <title>Генератор QR кодов</title>

  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" rel="stylesheet" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous" defer></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js" integrity="sha256-obZACiHd7gkOk9iIL/pimWMTJ4W/pBsKu+oZnSeBIek=" crossorigin="anonymous" defer></script>

  <style>
  	@font-face {
    font-family: 'zero_5regular';
    src: url('zero5-webfont.woff2') format('woff2'),
         url('zero5-webfont.woff') format('woff'),
         url('zero5-webfont.svg#zero_5regular') format('svg');
    font-weight: normal;
    font-style: normal;

}

    html {
      height: 100%;
      font-family: sans-serif;
    }

    body {
      display: -webkit-flex;
      display: flex;
      -webkit-flex-direction: column;
      flex-direction: column;
      height: calc(100% - 100px);
      margin: 50px 0;
      background-color: #ccc;
    }

    body {
		font-family: 'zero_5regular';
	}

    *, :after, :before {
      box-sizing: border-box;
    }

    main {
      display: -webkit-flex;
      display: flex;
      -webkit-align-items: center;
      align-items: center;
      -webkit-justify-content: center;
      justify-content: center;
      height: 100%;
    }

    main section {
      min-width: 450px;
      max-width: 50%;
      height: 100%;
      text-align: center;
    }

    main img {
      box-shadow: 0 0 10px 5px #666;
    }

    main form {
      padding: 25px 0 50px 0;
      text-align: left;
    }

    main form label {
      display: block;
      margin-top: 10px;
      color: #444;
      font-weight: bold;
    }

    main form input,
    main form select {
      width: 100%;
    }

    main form input:invalid {
      outline: 2px solid #f00;
      color: #f00;
    }
  </style>
</head>
<body>
<main>
  <section>
    <img id="qrious">

    <form autocomplete="off">
      <label>
        Текст на коде:
        <input id="labelText" type="text" name="value" value="Text" spellcheck="false">
      </label>

      <label>
        Текст для кодирования:
        <input id="codeText" type="text" name="value" value="https://google.ru" spellcheck="false">
      </label>
    </form>
  </section>
</main>

<script src="/qrious.js"></script>
<script>
  (function() {
    var qr = window.qr = new QRious({
      element: document.getElementById('qrious'),
      size: 450,
      value: $('#codeText').val(),
      text: $('#labelText').val(),
      level: 'H'
    });

    $('#labelText').keyup(function() {
    	qr.text = $(this).val();
    });

    $('#codeText').keyup(function() {
    	qr.value = $(this).val();
    });
  })();
</script>
</body>
</html>

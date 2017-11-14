<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require 'vendor/phpmailer/phpmailer/src/Exception.php';
	require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
	require 'vendor/phpmailer/phpmailer/src/SMTP.php';

	require_once('utils.php');

	if (!isset($_COOKIE['session'])) {
		echo 'Session not found';
	}

	$session = $_COOKIE['session'];

	if (!isset($_COOKIE['userid'])) {
		echo 'UserID not found';
	}

	$userid = $_COOKIE['userid'];

	if (!preg_match('/^\{?[0-9a-zA-Z]{20}\}?$/', $userid)) {
		echo 'Invalid UserID';
	}

	if ($userid != 'jYzACIND80rGj0XngB3N') {
		echo 'You is not Root';
	}

	date_default_timezone_set('Etc/UTC');
	require 'vendor/autoload.php';
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = 'smtp.jino.ru';
	$mail->Port = 465;
	$mail->SMTPAuth = true;

	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 2;
	$mail->Username = 'nightmarez';
	$mail->Password = SMTP_PASS;
	$mail->setFrom('noreply@russiancoders.ru', 'Mikhail Makarov');
	$mail->addReplyTo('replyto@example.com', 'Mikhail Makarov');
	$mail->addAddress('m.m.makarov@gmail.com', 'John Doe');
	$mail->Subject = 'PHPMailer SMTP test';
	$mail->msgHTML('test content', __DIR__);
	$mail->AltBody = 'This is a plain-text message body';
	//$mail->addAttachment('images/phpmailer_mini.png');

	if (!$mail->send()) {
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
	    echo 'Message sent!';
	}
?>
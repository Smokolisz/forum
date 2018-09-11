<?php

	session_start();
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require __DIR__ . '/vendor/autoload.php';

	/* SQL */
	ini_set("display_errors", 1);
	require_once 'administrator/dbconnect.php';
	$polaczenie = mysqli_connect($host, $user, $password);
	mysqli_query($polaczenie, "SET CHARSET utf8");
	mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $database);
			
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		echo "\n Error: ".$polaczenie->connect_errno." Opis: ".$polaczenie->connect_error;
	}
				
	// WERYFIKACJA EMAILA
	$bytes = random_bytes(100);
	$bytes = bin2hex($bytes);
		
	if (!$polaczenie->query("INSERT INTO email VALUES ('', ".$_SESSION["id"].", NULL, '$bytes')"))
	{
		throw new Exception($polaczenie->error);
	}
	else
	{
		function send_mail($config)
		{
			$mail = new PHPMailer;

			$mail->SMTPDebug = 0;                               // Enable verbose debug output

			$mail->CharSet = 'UTF-8';
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'host';  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = 'username';                 // SMTP username
			$mail->Password = 'password';                           // SMTP password
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 25;                                    // TCP port to connect to

			$mail->setFrom('email', 'forum');
			$mail->addAddress('email', 'forum');     // Add a recipient
			// $mail->addAddress('ellen@example.com');               // Name is optional
			$mail->addReplyTo($config->from_email, $config->from_name);
			// $mail->addCC('cc@example.com');
			// $mail->addBCC('bcc@example.com');

			// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
			$mail->isHTML(true);                                  // Set email format to HTML

			$mail->Subject = $config->mail_subject;
			$mail->Body    = $config->mail_body;

			$html = new \Html2Text\Html2Text($mail->Body);
			$mail->AltBody = $html->getText();

			if(!$mail->send()) {
				echo 'Message could not be sent.';
				echo 'Mailer Error: ' . $mail->ErrorInfo;
			} else {
				echo 'Message has been sent';
			}

		}
		$config = (object) [
			'from_email' => "email",
			'from_name' => "Wolne Forum",
			'mail_subject' => $_SESSION["user"]." Potwierdź swój email - Wolne Forum",
			'mail_body' => 'Kliknij w link werfikacyjny: <a href="http://localhost/forum/weryfikacja.php?id='.$bytes.'" target="_blank" title="Wolne Forum">KLIK</a>',
		];
		send_mail($config); //WYSŁANIE EMAILA
	}

?>
<?php

class mailCtrl {

	function __construct() {
		require('PHP/PHPMailerAutoload.php');
	}

	function send_mail($body,$adress,$name) {
	  	$mail = new PHPMailer();
		$mail->IsSMTP(); // Use SMTP
	  	$mail->Host        = "smtp.live.com"; // Sets SMTP server
	 	//$mail->SMTPDebug   = 2; // 2 to enable SMTP debug information
		$mail->SMTPAuth    = TRUE; // enable SMTP authentication
	    $mail->SMTPSecure  = "tls"; //Secure conection
	    $mail->Port        = 587; // set the SMTP port
	    $mail->Username    = 'pacheco2590@hotmail.com'; // SMTP account username
	    $mail->Password    = '@tlascontodo51'; // SMTP account password
	    $mail->Priority    = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
	    $mail->CharSet     = 'UTF-8';
	    $mail->Encoding    = '8bit';
	    $mail->Subject     = 'Estatus Cambio';
	    $mail->ContentType = 'text/html; charset=utf-8\r\n';
	    $mail->From        = 'administracion@SMS.co.nf';
	    $mail->FromName    = 'Admnistracion';
	    $mail->WordWrap    = 900; // RFC 2822 Compliant for Max 998 characters per line

 		$mail->AddAddress( $adress ); // To:
  		$mail->isHTML( TRUE );
  		$mail->Body    = $body;
 		$mail->AltBody = $body;
 	 	$mail->Send();
  		$mail->SmtpClose();

		if(!$mail->send()) {
		   //echo 'Message could not be sent.';
		   //echo 'Mailer Error: ' . $mail->ErrorInfo;
		   return;
		}

		echo 'Message has been sent';
		return;
	}

}

?>
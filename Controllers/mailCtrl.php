<?php

class mailCtrl {

	function __construct() {
		require('PHP/PHPMailerAutoload.php');
	}

	function send_mail($body,$adress,$name,$subject) {
		require('Models/phpmailer.inc');
	  	$mail = new PHPMailer();
		$mail->IsSMTP(); // Use SMTP
	  	$mail->Host        = "smtp.live.com"; // Sets SMTP server
	 	//$mail->SMTPDebug   = 2; // 2 to enable SMTP debug information
		$mail->SMTPAuth    = TRUE; // enable SMTP authentication
	    $mail->SMTPSecure  = "tls"; //Secure conection
	    $mail->Port        = 587; // set the SMTP port
	    $mail->Username    = $username; // SMTP account username
	    $mail->Password    = $password; // SMTP account password
	    $mail->Priority    = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
	    $mail->CharSet     = 'UTF-8';
	    $mail->Encoding    = '8bit';
	    $mail->Subject     = $subject;
	    $mail->ContentType = 'text/html; charset=utf-8\r\n';
	    $mail->From        = 'administracion@SMS.co.nf';
	    $mail->FromName    = 'Admnistracion';
	    $mail->WordWrap    = 900; // RFC 2822 Compliant for Max 998 characters per line

 		$mail->AddAddress( $adress ); // To:
  		$mail->isHTML( TRUE );
  		$mail->Body    = $body;
 		$mail->AltBody = $body;
 	 	//$mail->Send();
 	 	$mail->clearAddresses();
  		$mail->SmtpClose();
		return;
	}

	function status_change($nombre) {
		$cadena = "
			Saludos $nombre.</br>
			Tu Estatus ha sido modificado!</br>

			Si tiene alguna duda Favor de mandar correo electronico a administracion@SMS.co.nf.</br>

			Este mensaje ha sido enviado automaticamente por el sistema Students Management System.
		";
		return $cadena;
	}

	function registration($nombre,$codigo,$pass) {
		$cadena = "
			Saludos $nombre.</br>
			Tu cuenta ha sido creada!</br></br></br>
			Para poder ingresar al Sistema de Alumnos ir a la direccion http://www.smsystem.co.nf </br>
			Tus datos para ingresar son :</br>
			<ul>
				<li>
					Codigo : $codigo
				</li>
				<li>
					Contrasenha : $pass
				</li>
			</ul>
			Se recomienda cambiar la Contrasenha al ingresar por primera vez.

			Si tiene alguna duda Favor de mandar correo electronico a administracion@SMS.co.nf.</br>

			Este mensaje ha sido enviado automaticamente por el sistema Students Management System.
		";
		return $cadena;
	}

	function rubro_captured($nombre,$rubro,$curso) {
		$cadena = "
			Saludos $nombre.</br>
			Te han subido la calificacion de $rubro de tu curso de $curso!</br></br></br>
			
			Si tiene alguna duda Favor de mandar correo electronico a administracion@SMS.co.nf.</br>

			Este mensaje ha sido enviado automaticamente por el sistema Students Management System.
		";
		return $cadena;
	}

}

?>
<?php require 'mailer/PHPMailerAutoload.php';

$mail = new PHPMailer;
$mail->SMTPDebug = 2;
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;
$mail->SMTPSecure = 'ssl';
$mail->SMTPAuth = true;
$mail->Debugoutput = 'html';
$mail->isSMTP();
$mail->Username = "castingchapman@gmail.com";
$mail->Password = "Cocokai1";
$mail->setFrom('chapmancasting@yahoo.com', 'Chapman Casting');
$mail->AltBody = 'Sorry, your email client cannot display this email.';

<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$user_id = getInt("user_id");
$body = nl2br(get("body"));
$attachment = getFile("attachment");

if ($user_id && $body) {
  $account = $db->query("SELECT email FROM accounts WHERE id=$user_id")->fetch();
  if ($account) {
    require '../../../inc/email.php';
    $mail->addReplyTo($MYACCOUNT['email'], $MYACCOUNT['firstname']." ".$MYACCOUNT['lastname']);
    $mail->addCC($MYACCOUNT['email']);
    $mail->addBCC($account['email']);
    $mail->Subject = "Direct Message From ".$MYACCOUNT['firstname']." ".$MYACCOUNT['lastname'];
    $mail->msgHTML($body."<br><br>");
    if ($attachment) $mail->addAttachment($attachment['tmp_name'], $attachment['name']);

    ok();
    fastcgi_finish_request();
    $mail->send();
  }
} else failed("Please fill in all fields");

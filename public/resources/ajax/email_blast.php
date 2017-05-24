<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$call_id = getInt("call_id");
$body = nl2br(get("body"));
$attachment = getFile("attachment");

if ($call_id && $body) {
  $call = $db->query("SELECT title FROM calls JOIN collaborators ON calls.id=collaborators.call_id WHERE calls.id=$call_id AND collaborators.d_id=".$MYACCOUNT['d_id'])->fetch();
  if ($call) {
    require '../../../inc/email.php';
    $collaborators = $db->query("SELECT email FROM calls JOIN collaborators ON calls.id=collaborators.call_id JOIN accounts ON accounts.d_id=collaborators.d_id WHERE calls.id=$call_id AND collaborators.d_id=".$MYACCOUNT['d_id'])->fetchAll();
    $interested = $db->query("SELECT email FROM interested JOIN characters ON interested.char_id=characters.id JOIN accounts ON interested.a_id=accounts.a_id WHERE characters.call_id=$call_id")->fetchAll();
    foreach ($collaborators as $d) $mail->addCC($d['email']);
    foreach ($interested as $d) $mail->addBCC($d['email']);
    $mail->addReplyTo($MYACCOUNT['email'], $MYACCOUNT['firstname']." ".$MYACCOUNT['lastname']);
    $mail->Subject = $call['title']." Audition";
    $mail->msgHTML($body."<br><br>");
    if ($attachment) $mail->addAttachment($attachment['tmp_name'], $attachment['name']);

    ok();
    fastcgi_finish_request();
    $mail->send();
  }
} else failed("Please fill in all fields");

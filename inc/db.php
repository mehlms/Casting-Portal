<?php
date_default_timezone_set('America/Los_Angeles');

$db_opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$db = new PDO('mysql:host=localhost;dbname=casting;charset=utf8', "root", "", $db_opt);

$MYACCOUNT = null;
$token = isset($_COOKIE["token"]) ? $_COOKIE['token']: null;
$page_id = "";
if ($token) {
  $MYACCOUNT = $db->query("SELECT * FROM accounts WHERE token='$token'")->fetch();
  if ($MYACCOUNT)  {
    $MYACCOUNT['age'] = date_diff(date_create($MYACCOUNT['birthdate']), date_create('now'))->y;
    $MYACCOUNT['bio'] = $MYACCOUNT['mode'] ? $MYACCOUNT['d_bio'] : $MYACCOUNT['a_bio'];
    $page_id = $MYACCOUNT['mode'] ? $MYACCOUNT['d_id'] : $MYACCOUNT['a_id'];
  } else setcookie("token", "", time() - 10000);
}

function post($url, $data) {
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($curl);
  curl_close($curl);
  return $response;
}

function format($date, $format) {
  $d = new DateTime($date);
  return $d->format($format);
}

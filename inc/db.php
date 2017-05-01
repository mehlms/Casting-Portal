<?php
date_default_timezone_set('America/Los_Angeles');

$db_opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$db = new PDO('mysql:host=localhost;dbname=casting;charset=utf8', "root", "", $db_opt);

$MYACCOUNT = null;
$token = isset($_COOKIE["token"]) ? $_COOKIE['token']: null;
if ($token) {
  $MYACCOUNT = $db->query("SELECT * FROM accounts WHERE token='$token'")->fetch();
  if (!$MYACCOUNT) setcookie("token", "", time() - 10000);
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

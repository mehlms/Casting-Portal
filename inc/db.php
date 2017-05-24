<?php
date_default_timezone_set('America/Los_Angeles');
$db_opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false
];

$db = new PDO('mysql:host=localhost;dbname=casting;charset=utf8', "root", "", $db_opt);
$MYACCOUNT = null;
$page_id = null;

if (isset($_COOKIE["id"]) && isset($_COOKIE["token"])) {
  $MYACCOUNT = $db->query("SELECT * FROM accounts WHERE id=".$_COOKIE['id'])->fetch();
  if ($MYACCOUNT && $MYACCOUNT['token'] == $_COOKIE["token"])  {
    setCookie("id", $MYACCOUNT['id'], time()+3600*24*365, "/");
    setCookie("token", $MYACCOUNT['token'], time()+3600*24*365, "/");
    $MYACCOUNT['age'] = date_diff(date_create($MYACCOUNT['birthdate']), date_create('now'))->y;
    $MYACCOUNT['bio'] = $MYACCOUNT['mode'] ? $MYACCOUNT['d_bio'] : $MYACCOUNT['a_bio'];
    $page_id = $MYACCOUNT['mode'] ? $MYACCOUNT['d_id'] : $MYACCOUNT['a_id'];
  } else {
    setcookie("id", "", time() - 10000);
    setcookie("token", "", time() - 10000);
    $MYACCOUNT = null;
  }
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

function ok() { echo json_encode(array("status"=>"ok")); }
function failed($s) { echo json_encode(array("status"=>"failed", "message"=>$s)); }

function get($s) { return isset($_POST[$s]) ? htmlentities(addslashes(trim($_POST[$s]))) : null; }
function getArray($s) { return isset($_POST[$s]) ? json_decode($_POST[$s], true) : array(); }
function getInt($s) { return isset($_POST[$s]) ? intval(trim($_POST[$s])) : null; }
function getDouble($s) { return isset($_POST[$s]) ? doubleval(trim($_POST[$s])) : null; }
function getFile($s) { return isset($_FILES[$s]) ? $_FILES[$s] : null; }
function getDateTime($s) { return isset($_POST[$s]) ? strToDate(trim($_POST[$s])) : null; }
function getCheckbox($s) { return isset($_POST[$s]) ? $_POST[$s] : array(); }
function sortDate($a, $b) {
  $a = new DateTime($a['added']);
  $b = new DateTime($b['added']);
  if ($a == $b) return 0;
  return ($a > $b) ? -1 : 1;
}
function strToDate($s) {
  $date = strtotime($s);
  $date = date('Y-m-d H:i:s', $date);
  return $date != "1969-12-31 16:00:00" ? $date : null;
}

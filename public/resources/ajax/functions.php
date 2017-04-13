<?php include "../../../inc/db.php";

ini_set('client_max_body_size', '4m');

$token = $MYACCOUNT['token'];
$func = get("func");

if ($func == "login") {
  $email = get("email");
  $password = get("password");

  if ($email && $password) {
    $CHAPMANAPI = json_decode(post("http://localhost/resources/ajax/api.php", array("email" => $email, "password" => $password)), true);
    if ($CHAPMANAPI["status"] == "ok") {
      $MYACCOUNT = $db->query("SELECT * FROM accounts WHERE email='$email'")->fetch();
      if ($MYACCOUNT) { // IF THE USER ALREADY HAS AN ACCOUNT
        setCookie("token", $MYACCOUNT['token'], time()+3600*24*365, "/");
      } else { // IF THE USER HAS NEVER LOGGED IN
        $username = explode("@", $email)[0];
        $token = sha1(time().rand());
        $db->query("INSERT INTO accounts VALUES (null, (SELECT UUID_short()), (SELECT UUID_short()), 0, '$token', '$email', null, null, null, null, 0, 0, 0, NOW(), NOW())");
        setCookie("token", $token, time()+3600*24*365, "/");
      }
      echo json_encode(array("status"=>"ok"));
    } else echo json_encode(array("status"=>"failed", "message"=>"Invalid credentials"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == "complete") {
  $firstname = getWords("firstname");
  $lastname = getWords("lastname");
  $gender = getInt("gender");
  $month = getInt("month");
  $day = getInt("day");
  $year = getInt("year");
  $role = getInt("role");

  if ($gender && $firstname && $lastname && $month && $day && $year && $role != -1) {
    if ($month <= 12 && $month >= 0 && $day >= 0 && $day <= 31 && $year >= 0 && $year < 3000) {
      if ($year < 100 && $year >= 17) $year = $year + 1900;
      else if ($year < 100 && $year < 17) $year = $year + 2000;
      $db->query("UPDATE accounts SET firstname='$firstname', lastname='$lastname', gender=$gender, mode='$role', birthdate=STR_TO_DATE('$month $day $year','%m %d %Y') WHERE token='$token'");
      echo json_encode(array("status"=>"ok", "message"=>"Success"));
    } else echo json_encode(array("status"=>"failed", "message"=>"Please enter a valid date of birth"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == "create") {
  $title = getWords("title");
  $type = getInt("type");
  $location = get("audition_location");
  $audition_time = get("audition_time");
  $description = get("description");
  $parts = getArray("parts");

  if ($title && $type && $location && $audition_time && $description && count($parts) > 0) {
    $d_id = intval($MYACCOUNT['d_id']);
    $db->query("INSERT INTO calls VALUES ((SELECT UUID_short()), $d_id, '$title', $type, '$description', '$location', '$audition_time', NOW())");
    $call_id = $db->query("SELECT id FROM calls ORDER BY id DESC")->fetch()['id'];
    foreach ($parts as $part) {
      $char_name = $part["char_name"];
      $char_min = $part["char_min"];
      $char_max = $part["char_max"];
      $char_gender = $part["char_gender"];
      $char_description = $part["char_description"];
      $db->query("INSERT INTO characters VALUES (null, $call_id, '$char_name', '$char_description', $char_min, $char_max, $char_gender)");
    }
    echo json_encode(array("status"=>"ok", "url"=>"/call/".$call_id."/"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == 'interested') {
  try {
    $a_id = $MYACCOUNT['a_id'];
    $d_id = getInt("d_id");
    $char_id = getInt("char_id");
    $existanceCheck = $db->query("SELECT COUNT(id) FROM notifications WHERE type=1 AND a_id=$a_id AND d_id=$d_id AND char_id=$char_id")->fetch()[0];
    if ($existanceCheck) {
      $db->query("DELETE FROM notifications WHERE type=1 AND a_id=$a_id AND d_id=$d_id AND char_id=$char_id");
      echo json_encode(array("status"=>"ok", "message"=>"You have revoked your interest", "interested"=>0));
    } else {
      $db->query("INSERT INTO notifications VALUES (null, 1, $a_id, $d_id, $char_id, NOW())");
      echo json_encode(array("status"=>"ok", "message"=>"The director has been notified", "interested"=>1));
    }
  } catch (Exception $e) {
    echo json_encode(array("status"=>"failed", "message"=>$e));
  }
}

else if ($MYACCOUNT && $func == 'uploadImage') {
  $src = null;
  if ($_FILES["image"]["type"] == "image/png") $src = imagecreatefrompng($_FILES["image"]["tmp_name"]);
  else if ($_FILES["image"]["type"] == "image/jpeg") $src = imagecreatefromjpeg($_FILES["image"]["tmp_name"]);
  else {
    echo json_encode(array("status"=>"failed", "message"=>".JPG and .PNG only please"));
    return;
  }
  list($width, $height) = getimagesize($_FILES['image']['tmp_name']);
  $xPos = 0;
  $yPos = 0;
  if ($width > $height) {
    $xPos = ($width - $height) / 2;
    $width = $height;
  } else if ($height > $width) {
    $yPos = ($height - $width) / 2;
    $height = $width;
  }
  $filename = time().rand().".jpg";
  $dst = imagecreatetruecolor(250, 250);
  imagecopyresampled($dst, $src, 0, 0, $xPos, $yPos, 250, 250, $width, $height);
  imagejpeg($dst, "../assets/profile/".$filename, 100);
  $page_id = $MYACCOUNT['mode'] ? $MYACCOUNT['d_id'] : $MYACCOUNT['a_id'];
  $db->query("INSERT INTO assets VALUES (null, $page_id, '$filename', 1, 1)");
  echo json_encode(array("status"=>"ok", "message"=>"Updated", "filename"=>$filename));
}
else echo json_encode(array("status"=>"failed", "message"=>"That function does not exist"));

function get($s) { return isset($_POST[$s]) ? trim($_POST[$s]) : null; }
function getArray($s) { return isset($_POST[$s]) ? json_decode($_POST[$s], true) : array(); }
function getWords($s) { return isset($_POST[$s]) ? ucwords(trim($_POST[$s])) : null; }
function getInt($s) { return isset($_POST[$s]) ? intval(trim($_POST[$s])) : null; }
function getDouble($s) { return isset($_POST[$s]) ? doubleval(trim($_POST[$s])) : null; }

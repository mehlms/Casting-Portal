<?php include "../../../inc/db.php";

ini_set('client_max_body_size', '4m');

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
        $db->query("INSERT INTO accounts VALUES (null, (SELECT UUID_short()), (SELECT UUID_short()), '', '', 0, '$token', '$email', null, null, null, null, 0, 0, NOW(), NOW())");
        setCookie("token", $token, time()+3600*24*365, "/");
      }
      echo json_encode(array("status"=>"ok"));
    } else echo json_encode(array("status"=>"failed", "message"=>"Invalid credentials"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == "complete") {
  $role = getInt("role");
  $gender = getInt("gender");
  $firstname = getWords("firstname");
  $lastname = getWords("lastname");
  $birthdate = getDateTime("birthdate");

  if ($role != -1 && $gender && $firstname && $lastname && $birthdate) {
    $db->query("UPDATE accounts SET firstname='$firstname', lastname='$lastname', gender=$gender, mode='$role', birthdate='$birthdate' WHERE token='$token'");
    echo json_encode(array("status"=>"ok", "message"=>"Success"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == "updateInfo") {
  $firstname = getWords("firstname");
  $lastname = getWords("lastname");
  $birthdate = getDateTime("birthdate");
  $bio = get("bio");
  if ($firstname && $lastname && $birthdate) {
    if ($MYACCOUNT['mode']) $db->query("UPDATE accounts SET d_bio='$bio', firstname='$firstname', lastname='$lastname', birthdate='$birthdate' WHERE token='$token'");
    else $db->query("UPDATE accounts SET a_bio='$bio', firstname='$firstname', lastname='$lastname', birthdate='$birthdate' WHERE token='$token'");
    echo json_encode(array("status"=>"ok", "message"=>"Success"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == "create") {
  $title = getWords("title");
  $type = getInt("type");
  $description = get("description");
  $auditions = getArray("auditions");
  $shootings = getArray("shootings");
  $characters = getArray("characters");

  if ($title && $type && $description && count($auditions) > 0 && count($shootings) > 0 && count($characters) > 0) {
    $db->query("INSERT INTO calls VALUES ((SELECT UUID_short()), $page_id, '$title', $type, '$description', NOW())");
    $call_id = $db->query("SELECT id FROM calls ORDER BY id DESC")->fetch()['id'];
    foreach ($auditions as $audition) {
      $audition_time = strToDate($audition["time"]);
      $audition_place = $audition["place"];
      $db->query("INSERT INTO auditions VALUES (null, $call_id, $audition_time, $audition_place)");
    }
    foreach ($shootings as $shooting) {
      $shooting_from = strToDate($shooting["from"]);
      $shooting_to = strToDate($shooting["to"]);
      $db->query("INSERT INTO shootings VALUES (null, $call_id, $shooting_from, $shooting_to)");
    }
    foreach ($characters as $char) {
      $char_name = $char["char_name"];
      $char_min = $char["char_min"];
      $char_max = $char["char_max"];
      $char_gender = $char["char_gender"];
      $char_description = $char["char_description"];
      $db->query("INSERT INTO characters VALUES (null, $call_id, '$char_name', '$char_description', $char_min, $char_max, $char_gender)");
    }
    echo json_encode(array("status"=>"ok", "url"=>"/call/".$call_id."/"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == 'interested') {
  $a_id = $MYACCOUNT['a_id'];
  $call_id = getInt("call_id");
  $char_id = getInt("char_id");
  $existanceCheck = $db->query("SELECT id FROM interested WHERE a_id=$a_id AND call_id=$call_id AND char_id=$char_id")->fetch();
  if ($existanceCheck) {
    $db->query("DELETE FROM interested WHERE a_id=$a_id AND call_id=$call_id AND char_id=$char_id");
    echo json_encode(array("status"=>"ok", "message"=>"You have revoked your interest", "interested"=>0));
  } else {
    $db->query("INSERT INTO notifications VALUES (null, $a_id, $call_id, $char_id, NOW())");
    echo json_encode(array("status"=>"ok", "message"=>"The casting team has been notified", "interested"=>1));
  }
}

else if ($MYACCOUNT && $func == 'uploadProfilePic') {
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
  if ($width > $height) $xPos = ($width - $height) / 2;
  else $yPos = ($height - $width) / 2;

  $dst = imagecreatetruecolor(204, 204);
  imagecopyresampled($dst, $src, 0, 0, $xPos, $yPos, 204, 204, min($width, $height), min($width, $height));

  $filename = $page_id.".jpg";
  $existenceCheck = $db->query("SELECT url FROM assets WHERE page_id=$page_id AND type=1")->fetch();
  if ($existenceCheck) {
    $filename = $existenceCheck['url'];
    imagejpeg($dst, "../assets/profile/".$filename, 90);
  } else {
    imagejpeg($dst, "../assets/profile/".$filename, 90);
    $db->query("INSERT INTO assets VALUES (null, $page_id, '', '$filename', 1, NOW())");
  }
  echo json_encode(array("status"=>"ok", "message"=>getConfirmation()));
}

else if ($MYACCOUNT && $func == 'uploadPic') {
  $src = null;
  $photoCount = $db->query("SELECT COUNT(id) FROM assets WHERE page_id=$page_id AND type=2")->fetch();
  if ($photoCount[0] >= 15) {
    echo json_encode(array("status"=>"failed", "message"=>"Only 15 pictures allowed"));
    return;
  } else if ($_FILES["image"]["type"] == "image/png") $src = imagecreatefrompng($_FILES["image"]["tmp_name"]);
  else if ($_FILES["image"]["type"] == "image/jpeg") $src = imagecreatefromjpeg($_FILES["image"]["tmp_name"]);
  else {
    echo json_encode(array("status"=>"failed", "message"=>".JPG and .PNG only please"));
    return;
  }

  list($width, $height) = getimagesize($_FILES['image']['tmp_name']);
  $xPos = 0;
  $yPos = 0;
  $wAspect = 1.0;
  $hAspect = 1.0;
  if ($width > $height) {
    $hAspect = $height / $width;
    $xPos = ($width - $height) / 2;
  } else {
    $wAspect = $width / $height;
    $yPos = ($height - $width) / 2;
  }

  $dst = imagecreatetruecolor(176, 176);
  imagecopyresampled($dst, $src, 0, 0, $xPos, $yPos, 176, 176, min($width, $height), min($width, $height));
  $dst_large = imagecreatetruecolor(550*$wAspect, 550*$hAspect);
  imagecopyresampled($dst_large, $src, 0, 0, 0, 0, 550*$wAspect, 550*$hAspect, $width, $height);

  $filename = time().rand(1000,9999).".jpg";
  imagejpeg($dst, "../assets/photos/".$filename, 90);
  imagejpeg($dst_large, "../assets/photos_large/".$filename, 90);
  $db->query("INSERT INTO assets VALUES (null, $page_id, '', '$filename', 2, NOW())");
  echo json_encode(array("status"=>"ok", "message"=>getConfirmation()));
}

else if ($MYACCOUNT && $func == 'addVideo') {
  $title = get("title");
  $youtubeLink = get('youtubeLink');
  $vimeoLink = get('vimeoLink');
  if ($title && ($youtubeLink || $vimeoLink)) {
    if ($youtubeLink) {
      preg_match('/v=([^&]*)/', $youtubeLink, $matches);
      if ($matches) {
        $link = $matches[1];
        $db->query("INSERT INTO assets VALUES (null, $page_id, '$title', '$link', 3, NOW())");
        echo json_encode(array("status"=>"ok", "message"=>"Added Video"));
      } else echo json_encode(array("status"=>"failed", "message"=>"Invalid URL"));
    } else if ($vimeoLink) {
      preg_match('/vimeo\.com\/(.*)/', $vimeoLink, $matches);
      if ($matches) {
        $link = $matches[1];
        $db->query("INSERT INTO assets VALUES (null, $page_id, '$title', '$link', 4, NOW())");
        echo json_encode(array("status"=>"ok", "message"=>"Added Video"));
      } else echo json_encode(array("status"=>"failed", "message"=>"Invalid URL"));
    }
  } else {
    echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
  }
}

else if ($MYACCOUNT && $func == 'deleteAsset') {
  $id = getInt('id');
  if ($id) {
    $db->query("DELETE FROM assets WHERE id=$id AND page_id=$page_id");
    if ($db->query("SELECT ROW_COUNT()")->fetch()[0] == 1) echo json_encode(array("status"=>"ok"));
    else echo json_encode(array("status"=>"failed", "message"=>"H4CKing is a lyfestyle"));
  }
}

else echo json_encode(array("status"=>"failed", "message"=>"That function does not exist", "func"=>$func));

function getConfirmation() {
  $confirmations = array("Nice one :)", "It's lit!", "Dope pic", "10/10 would recommend", "Good taste!", "You look beautiful :)", "Gorgeus Face", "Damn shawty get low");
  return $confirmations[rand(0, count($confirmations)-1)];
}
function get($s) { return isset($_POST[$s]) ? addslashes(trim($_POST[$s])) : null; }
function getArray($s) { return isset($_POST[$s]) ? json_decode($_POST[$s], true) : array(); }
function getWords($s) { return isset($_POST[$s]) ? addslashes(ucwords(trim($_POST[$s]))) : null; }
function getInt($s) { return isset($_POST[$s]) ? addslashes(intval(trim($_POST[$s]))) : null; }
function getDouble($s) { return isset($_POST[$s]) ? addslashes(doubleval(trim($_POST[$s]))) : null; }
function getDateTime($s) { return isset($_POST[$s]) ? strToDate(trim($_POST[$s])) : null; }
function strToDate($s) {
  $date = strtotime($s);
  $date = date('Y-m-d H:i:s', $date);
  return $date != "1969-12-31 16:00:00" ? $date : null;
}

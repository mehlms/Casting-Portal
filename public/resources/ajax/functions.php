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
    $db->query("UPDATE accounts SET firstname='$firstname', lastname='$lastname', gender=$gender, mode=$role, birthdate='$birthdate' WHERE token='$token'");
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

else if ($MYACCOUNT && $func == "getCall") {
  $id = getInt("id");
  $call = $db->query("SELECT calls.*, class FROM calls JOIN classes ON calls.type=classes.id WHERE calls.id=$id")->fetch();
  if ($call) {
    $collaborators = $db->query("SELECT accounts.d_id, firstname, lastname FROM collaborators JOIN accounts ON collaborators.d_id=accounts.d_id WHERE call_id=$id ORDER BY collaborators.added")->fetchAll();
    $auditions = $db->query("SELECT *, DATE_FORMAT(audition_time, '%l:%i %p, %b %D') as audition_time FROM auditions WHERE call_id=$id")->fetchAll();
    $shootings = $db->query("SELECT DATE_FORMAT(shooting_from, '%b %D') as shooting_from, DATE_FORMAT(shooting_to, '%b %D') as shooting_to FROM shootings WHERE call_id=$id")->fetchAll();
    $characters = $db->query("SELECT id, name, min, max, gender, description, (SELECT COUNT(*) FROM interested WHERE char_id=characters.id AND a_id=$page_id) as interested, (SELECT COUNT(*) FROM characters as c2 WHERE ".$MYACCOUNT['mode']."=0 AND characters.id=c2.id AND ".$MYACCOUNT['age'].">=min AND ".$MYACCOUNT['age']."<=max AND (gender=3 OR gender=".$MYACCOUNT['gender'].")) as can_interested FROM characters WHERE call_id=$id ORDER BY id ASC")->fetchAll();
    echo json_encode(array("status"=>"ok", "call"=>$call, "collaborators"=>$collaborators, "auditions"=>$auditions, "shootings"=>$shootings, "characters"=>$characters));
  } else echo json_encode(array("status"=>"failed", "message"=>"That call does not exist"));
}

else if ($MYACCOUNT && $func == "postCall") {
  $title = getWords("title");
  $type = getInt("type");
  $storyline = get("storyline");
  $auditions = getArray("auditions");
  $shootings = getArray("shootings");
  $characters = getArray("characters");

  $db->beginTransaction();
  try {
    if (!$title || !$type || !$storyline || !count($auditions) || !count($shootings) && !count($characters)) throw new Exception();
    $db->query("INSERT INTO calls VALUES ((SELECT UUID_short()), '$title', $type, '$storyline', NOW())");
    $call_id = $db->query("SELECT id FROM calls ORDER BY id DESC")->fetch()['id'];
    $db->query("INSERT INTO collaborators VALUES (null, $call_id, ".$MYACCOUNT['d_id'].", NOW())");

    foreach ($auditions as $d) {
      $time = strToDate($d["time"]);
      $place = addslashes($d["place"]);
      if (!$time || !$place) throw new Exception();
      $db->query("INSERT INTO auditions VALUES (null, $call_id, '$time', '$place')");
    }
    foreach ($shootings as $d) {
      $from = strToDate($d["from"]);
      $to = strToDate($d["to"]);
      if (!$from || !$to) throw new Exception();
      $db->query("INSERT INTO shootings VALUES (null, $call_id, '$from', '$to')");
    }
    foreach ($characters as $d) {
      $name = addslashes($d["name"]);
      $min = intval($d["min"]);
      $max = intval($d["max"]);
      $gender = intval($d["gender"]);
      $description = addslashes($d["description"]);
      if (!$name || !$min || !$max || !$gender || !$description) throw new Exception();
      $db->query("INSERT INTO characters VALUES (null, $call_id, '$name', $min, $max, $gender, '$description')");
    }
    echo json_encode(array("status"=>"ok"));
    $db->commit();
  } catch (Exception $e) {
    echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
  }
}

else if ($MYACCOUNT && $func == 'interested') {
  $char_id = getInt("char_id");

  $check = $db->query("SELECT COUNT(*) as interested, (SELECT COUNT(*) FROM assets WHERE page_id=".$MYACCOUNT['a_id']." AND type=1) as profile_pic, (SELECT COUNT(*) FROM characters WHERE characters.id=$char_id AND ".$MYACCOUNT['age'].">=min AND ".$MYACCOUNT['age']."<=max AND (gender=3 OR gender=".$MYACCOUNT['gender'].")) as can_interested FROM interested WHERE a_id=".$MYACCOUNT['a_id']." AND char_id=$char_id")->fetch();
  if ($check['interested']) {
    $db->query("DELETE FROM interested WHERE a_id=".$MYACCOUNT['a_id']." AND char_id=$char_id");
    echo json_encode(array("status"=>"ok", "message"=>"Revoked interest", "interested"=>0));
  } else if ($check['can_interested'] && $check['profile_pic']) {
    $db->query("INSERT INTO interested VALUES (null, ".$MYACCOUNT['a_id'].", $char_id, NOW())");
    echo json_encode(array("status"=>"ok", "message"=>"Submitted interest", "interested"=>1));
  } else {
    echo json_encode(array("status"=>"failed", "message"=>"You must first upload a profile picture"));
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
  $photoCount = $db->query("SELECT COUNT(*) FROM assets WHERE page_id=$page_id AND type=2")->fetch()["COUNT(*)"];
  if ($photoCount >= 15) {
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
    echo json_encode(array("status"=>"ok"));
  }
}

else if ($MYACCOUNT && $func == "praise") {
  $praise_to = getInt("praise_to");
  $heart = getInt("heart");
  $comment = get("comment");

  if ($praise_to && $heart || $comment) {
    if ($heart) {
      $id = $db->query("SELECT id FROM praise WHERE praise_from=$page_id AND praise_to=$praise_to AND heart=1")->fetch()['id'];
      if ($id) $db->query("DELETE FROM praise WHERE id=$id");
      else $db->query("INSERT INTO praise VALUES (null, $page_id, $praise_to, 1, null, NOW())");
      echo json_encode(array("status"=>"ok", "heart"=>1));
      return;
    } else $db->query("INSERT INTO praise VALUES (null, $page_id, $praise_to, 0, '$comment', NOW())");
    echo json_encode(array("status"=>"ok"));
  } else {
    echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
  }
}

else if ($MYACCOUNT && $func == "follow") {
  $follow_to = getInt("follow_to");

  if ($follow_to) {
    $id = $db->query("SELECT id FROM follow WHERE follow_from=$page_id AND follow_to=$follow_to")->fetch()['id'];
    if ($id) $db->query("DELETE FROM follow WHERE id=$id");
    else $db->query("INSERT INTO follow VALUES (null, $page_id, $follow_to, NOW())");
    echo json_encode(array("status"=>"ok"));
  } else {
    echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
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

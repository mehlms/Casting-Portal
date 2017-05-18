<?php include "../../../inc/db.php";

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
        $token = sha1(time().rand());
        $db->query("INSERT INTO accounts VALUES (null, SUBSTRING((SELECT UUID_short()), 9), SUBSTRING((SELECT UUID_short()), 9), '', '', 0, '$token', '$email', null, null, null, null, 0, 0, NOW(), NOW())");
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
    $age = date_diff(date_create($birthdate), date_create('now'))->y;
    $db->query("UPDATE accounts SET firstname='$firstname', lastname='$lastname', gender=$gender, mode=$role, birthdate='$birthdate', looks_min='$age', looks_max='$age' WHERE token='$token'");
    echo json_encode(array("status"=>"ok", "message"=>"Success"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == "updateInfo") {
  $firstname = getWords("firstname");
  $lastname = getWords("lastname");
  $birthdate = getDateTime("birthdate");
  $gender = getInt("gender");
  $bio = nl2br(get("bio"));
  if ($firstname && $lastname && $birthdate && $gender) {
    if ($MYACCOUNT['mode']) $db->query("UPDATE accounts SET d_bio='$bio', firstname='$firstname', lastname='$lastname', gender=$gender, birthdate='$birthdate' WHERE token='$token'");
    else $db->query("UPDATE accounts SET a_bio='$bio', firstname='$firstname', lastname='$lastname', gender=$gender, birthdate='$birthdate' WHERE token='$token'");
    echo json_encode(array("status"=>"ok", "message"=>"Success"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == "getCall") {
  $id = getInt("id");
  $call = $db->query("SELECT calls.*, class, (SELECT genre FROM genres WHERE calls.genre=genres.id) as g1, (SELECT genre FROM genres WHERE calls.genre2=genres.id) as g2, (SELECT url FROM assets WHERE page_id=calls.id AND type=6) as script, (SELECT url FROM assets WHERE page_id=calls.id AND type=5) as poster FROM calls JOIN classes ON calls.type=classes.id WHERE calls.id=$id")->fetch();
  if ($call) {
    $collaborators = $db->query("SELECT collaborators.d_id, firstname, lastname FROM collaborators JOIN accounts ON collaborators.d_id=accounts.d_id WHERE call_id=$id ORDER BY collaborators.added")->fetchAll();
    $auditions = $db->query("SELECT *, DATE_FORMAT(audition_time, '%M %D %l:%i%p') as audition_time FROM auditions WHERE call_id=$id")->fetchAll();
    $shootings = $db->query("SELECT DATE_FORMAT(shooting_from, '%M %D') as shooting_from, DATE_FORMAT(shooting_to, '%M %D') as shooting_to FROM shootings WHERE call_id=$id")->fetchAll();
    $characters = $db->query("SELECT id, name, min, max, gender, description, (SELECT COUNT(*) FROM interested WHERE char_id=characters.id AND a_id=$page_id) as interested, (SELECT COUNT(*) FROM characters as c2 WHERE ".$MYACCOUNT['mode']."=0 AND characters.id=c2.id AND ".$MYACCOUNT['looks_max'].">=min AND ".$MYACCOUNT['looks_min']."<=max AND (gender=3 OR gender=".$MYACCOUNT['gender'].")) as can_interested FROM characters WHERE call_id=$id ORDER BY id ASC")->fetchAll();
    echo json_encode(array("status"=>"ok", "call"=>$call, "collaborators"=>$collaborators, "auditions"=>$auditions, "shootings"=>$shootings, "characters"=>$characters));
  } else echo json_encode(array("status"=>"failed", "message"=>"That call does not exist"));
}

else if ($MYACCOUNT && $func == "postCall") {
  $title = getWords("title");
  $type = getInt("type");
  $genre = getInt("genre");
  $genre2 = getInt("genre2");
  $auditions = getArray("auditions");
  $shootings = getArray("shootings");
  $storyline = get("storyline");
  $characters = getArray("characters");
  $script = getFile('script');
  $poster = getFile('poster');

  $db->beginTransaction();
  try {
    if (!$title || !$genre || !$type || !$storyline || !count($auditions) || !count($shootings) || !count($characters)) throw new Exception();
    if ($genre == $genre2) {
      echo json_encode(array("status"=>"failed", "message"=>"Please choose different genres."));
      return;
    }
    $db->query("INSERT INTO calls VALUES (SUBSTRING((SELECT UUID_short()), 9), '$title', $type, $genre, $genre2, '$storyline', NOW())");
    $call_id = $db->query("SELECT id FROM calls ORDER BY id DESC")->fetch()['id'];
    $db->query("INSERT INTO collaborators VALUES (null, $call_id, ".$MYACCOUNT['d_id'].", NOW())");

    foreach ($auditions as $d) {
      $time = strToDate($d["time"]);
      $place = htmlentities(addslashes($d["place"]));
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
      $name = htmlentities(addslashes($d["name"]));
      $min = intval($d["min"]);
      $max = intval($d["max"]);
      $gender = intval($d["gender"]);
      $description = htmlentities(addslashes($d["description"]));
      if (!$name || !$min || !$max || !$gender || !$description) throw new Exception();
      $db->query("INSERT INTO characters VALUES (null, $call_id, '$name', $min, $max, $gender, '$description')");
    }

    if ($poster) {
      $src = null;
      if ($poster["type"] == "image/png") $src = imagecreatefrompng($poster["tmp_name"]);
      else if ($poster["type"] == "image/jpeg") $src = imagecreatefromjpeg($poster["tmp_name"]);
      else {
        echo json_encode(array("status"=>"failed", "message"=>".JPG or .PNG posters please"));
        return;
      }
      list($width, $height) = getimagesize($poster['tmp_name']);
      $dst = imagecreatetruecolor(154, 235);
      imagecopyresampled($dst, $src, 0, 0, 0, 0, 154, 205, $width, $width*(235.0/154.0));
      $filename = $call_id.".jpg";
      imagejpeg($dst, "../assets/posters/".$filename, 90);
      $db->query("INSERT INTO assets VALUES (null, $call_id, '', '$filename', 5, NOW())");
    }
    if ($script) {
      $ext = pathinfo($script['name'])['extension'];
      $filename = $call_id.".".$ext;
      move_uploaded_file($script['tmp_name'], "../assets/scripts/".$filename);
      $db->query("INSERT INTO assets VALUES (null, $call_id, '', '$filename', 6, NOW())");
    }

    $db->commit();
    echo json_encode(array("status"=>"ok"));
  } catch (Exception $e) {
    echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
  }
}

else if ($MYACCOUNT && $func == 'interested') {
  $char_id = getInt("char_id");

  $check = $db->query("SELECT COUNT(*) as interested, (SELECT COUNT(*) FROM assets WHERE page_id=".$MYACCOUNT['a_id']." AND type=1) as profile_pic, (SELECT COUNT(*) FROM characters WHERE characters.id=$char_id AND ".$MYACCOUNT['looks_max'].">=min AND ".$MYACCOUNT['looks_min']."<=max AND (gender=3 OR gender=".$MYACCOUNT['gender'].")) as can_interested FROM interested WHERE a_id=".$MYACCOUNT['a_id']." AND char_id=$char_id")->fetch();
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
        $db->query("INSERT INTO assets VALUES (null, $page_id, '$title', '".$matches[1]."', 3, NOW())");
        echo json_encode(array("status"=>"ok"));
      } else echo json_encode(array("status"=>"failed", "message"=>"Invalid URL"));
    } else if ($vimeoLink) {
      preg_match('/vimeo\.com\/(.*)/', $vimeoLink, $matches);
      if ($matches) {
        $db->query("INSERT INTO assets VALUES (null, $page_id, '$title', '".$matches[1]."', 4, NOW())");
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

else if ($MYACCOUNT && $func == "emailBlast") {
  $call_id = getInt("call_id");
  $call = $db->query("SELECT title FROM calls JOIN collaborators ON calls.id=collaborators.call_id WHERE calls.id=$call_id AND collaborators.d_id=".$MYACCOUNT['d_id']."")->fetch();
  if ($call) {
    $body = nl2br(get("body"));
    $attachment = getFile("attachment");
    if ($body) {
      $collaborators = $db->query("SELECT email FROM calls JOIN collaborators ON calls.id=collaborators.call_id JOIN accounts ON accounts.d_id=collaborators.d_id WHERE calls.id=$call_id AND collaborators.d_id=".$MYACCOUNT['d_id'])->fetchAll();
      $interested = $db->query("SELECT email FROM interested JOIN characters ON interested.char_id=characters.id JOIN accounts ON interested.a_id=accounts.a_id WHERE characters.call_id=$call_id")->fetchAll();

      require '../../../inc/mailer/PHPMailerAutoload.php';
      $mail = new PHPMailer;
      $mail->isSMTP();
      // $mail->SMTPDebug = 2;
      // $mail->Debugoutput = 'html';
      $mail->Host = 'smtp.gmail.com';
      $mail->Port = 465;
      $mail->SMTPSecure = 'ssl';
      $mail->SMTPAuth = true;
      $mail->Username = "helms107@mail.chapman.edu";
      $mail->Password = "Cocokai1";
      $mail->setFrom('helms107@mail.chapman.edu', 'Chapman Casting');
      $mail->addReplyTo($MYACCOUNT['email']);

      foreach ($collaborators as $d) $mail->addCC($d['email']);
      foreach ($interested as $d) $mail->addBCC($d['email']);

      $mail->Subject = $call['title']." Audition";
      $mail->msgHTML($body."<br><br>");
      $mail->AltBody = 'Sorry, your email client cannot display this email.';
      if ($attachment) $mail->addAttachment($attachment['tmp_name'], $attachment['name']);

      echo json_encode(array("status"=>"ok"));

      fastcgi_finish_request();
      $mail->send();
    } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
  } else echo json_encode(array("status"=>"failed", "message"=>"You are not a collaborator on this call"));
}

else if ($MYACCOUNT && $func == "email") {
  $user_id = getInt("user_id");
  $email = $db->query("SELECT email FROM accounts WHERE id=$user_id")->fetch();
  if ($email) {
    $body = nl2br(get("body"));
    $attachment = getFile("attachment");
    if ($body) {
      require '../../../inc/mailer/PHPMailerAutoload.php';
      $mail = new PHPMailer;
      $mail->isSMTP();
      // $mail->SMTPDebug = 2;
      // $mail->Debugoutput = 'html';
      $mail->Host = 'smtp.gmail.com';
      $mail->Port = 465;
      $mail->SMTPSecure = 'ssl';
      $mail->SMTPAuth = true;
      $mail->Username = "helms107@mail.chapman.edu";
      $mail->Password = "Cocokai1";
      $mail->setFrom('helms107@mail.chapman.edu', 'Chapman Casting');
      $mail->addReplyTo($MYACCOUNT['email']);
      $mail->addCC($MYACCOUNT['email']);
      $mail->addBCC($email['email']);

      $mail->Subject = "Direct Message From ".$MYACCOUNT['firstname']." ".$MYACCOUNT['lastname'];
      $mail->msgHTML($body."<br><br>");
      $mail->AltBody = 'Sorry, your email client cannot display this email.';
      if ($attachment) $mail->addAttachment($attachment['tmp_name'], $attachment['name']);

      echo json_encode(array("status"=>"ok"));

      fastcgi_finish_request();
      $mail->send();
    } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
  } else echo json_encode(array("status"=>"failed", "message"=>"This user does not exist"));
}

else if ($MYACCOUNT && $func == 'updateMatch') {
  $looks_min = getInt("looks_min");
  $looks_max = getInt("looks_max");

  if ($looks_min && $looks_max && $looks_max >= $looks_min) {
    $db->query("UPDATE accounts SET looks_min=$looks_min, looks_max=$looks_max WHERE token='$token'");
    echo json_encode(array("status"=>"ok"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == 'updateFilter') {
  $genresFilter = getCheckbox('genresFilter');
  $classesFilter = getCheckbox('classesFilter');

  $db->beginTransaction();
  $db->query("DELETE FROM classesFilter WHERE a_id=".$MYACCOUNT['a_id']);
  $db->query("DELETE FROM genresFilter WHERE a_id=".$MYACCOUNT['a_id']);
  foreach($genresFilter as $key=>$d) if (!$d) $db->query("INSERT INTO genresFilter VALUES (null, ".$MYACCOUNT['a_id'].", $key)");
  foreach($classesFilter as $key=>$d) if (!$d)  $db->query("INSERT INTO classesFilter VALUES (null, ".$MYACCOUNT['a_id'].", $key)");
  $db->commit();
  echo json_encode(array("status"=>"ok"));
}

else if ($MYACCOUNT && $func == 'getNotifications') {
  $notifications = array();
  if ($MYACCOUNT['mode']) {
    $praise = $db->query("SELECT accounts.a_id as id, heart, comment, firstname, lastname, praise.added, (SELECT 1) as type FROM praise JOIN accounts ON praise_from=accounts.a_id WHERE praise_to=".$MYACCOUNT['d_id']." LIMIT 30")->fetchAll();
    $interested = $db->query("SELECT accounts.d_id as id, firstname, lastname, name, interested.added, (SELECT 2) as type FROM interested JOIN accounts ON interested.a_id=accounts.a_id JOIN characters ON interested.char_id=characters.id JOIN calls ON characters.call_id=calls.id JOIN collaborators ON collaborators.d_id=".$MYACCOUNT['d_id']." LIMIT 30")->fetchAll();
    $notifications = array_merge($praise, $interested);
    usort($notifications, 'sortDate');
  } else {
    $praise = $db->query("SELECT accounts.d_id as id, heart, comment, firstname, lastname, praise.added, (SELECT 1) as type FROM praise JOIN accounts ON praise_from=accounts.d_id WHERE praise_to=".$MYACCOUNT['a_id']." LIMIT 30")->fetchAll();
    $matches = $db->query("SELECT calls.id, title, calls.added, (SELECT 3) as type FROM calls
                            JOIN (SELECT call_id, COUNT(*) as char_count FROM characters WHERE min<=".$MYACCOUNT['looks_max']." AND max>=".$MYACCOUNT['looks_min']." AND (gender=3 OR gender=".$MYACCOUNT['gender'].") GROUP BY call_id) as t2
                            ON t2.call_id=calls.id
                            WHERE calls.type NOT IN (SELECT class_id FROM classesFilter WHERE a_id=".$MYACCOUNT['a_id'].") AND
                            calls.genre NOT IN (SELECT genre_id FROM genresFilter WHERE a_id=".$MYACCOUNT['a_id'].") AND
                            calls.genre2 NOT IN (SELECT genre_id FROM genresFilter WHERE a_id=".$MYACCOUNT['a_id'].")")->fetchAll();
    $following = $db->query("SELECT collaborators.call_id as id, calls.title, firstname, lastname, follow.added, (SELECT 4) as type FROM follow JOIN accounts ON follow_to=accounts.d_id JOIN collaborators ON accounts.d_id=collaborators.d_id JOIN calls ON collaborators.call_id=calls.id WHERE follow_from=".$MYACCOUNT['a_id']." LIMIT 30")->fetchAll();
    $notifications = array_merge($praise, $matches);
    $notifications = array_merge($notifications, $following);
    usort($notifications, 'sortDate');
  }
  echo json_encode(array("status"=>"ok", "notifications"=>array_splice($notifications, 0, 30)));
}

else if ($MYACCOUNT && $func == 'search') {
  $type = getInt('type');
  $query = get('query');
  $results = array();
  if ($query) {
    $calls = $db->query("SELECT calls.id, calls.title, url, (SELECT 1) as type FROM calls LEFT JOIN assets ON page_id=calls.id WHERE calls.title LIKE '%$query%' LIMIT 2")->fetchAll();
    $directors = $db->query("SELECT d_id, (SELECT CONCAT(firstname, ' ', lastname)) as title, url, (SELECT 2) as type FROM accounts LEFT JOIN assets ON page_id=accounts.d_id WHERE (SELECT CONCAT(firstname, ' ', lastname)) LIKE '%$query%' LIMIT 2")->fetchAll();
    $talent = $db->query("SELECT a_id, (SELECT CONCAT(firstname, ' ', lastname)) as title, url, (SELECT 3) as type FROM accounts LEFT JOIN assets ON page_id=accounts.a_id WHERE (SELECT CONCAT(firstname, ' ', lastname)) LIKE '%$query%' LIMIT 2")->fetchAll();
    $results = array_merge($calls, $directors, $talent);
  }
  echo json_encode(array("status"=>"ok", "results"=>$results));
}

else if ($MYACCOUNT && $func == 'editCall') {
  $call_id = getInt('call_id');
  $title = getWords("title");
  $type = getInt("type");
  $genre = getInt("genre");
  $genre2 = getInt("genre2");
  $storyline = get("storyline");
  $script = getFile('script');
  $poster = getFile('poster');

  $db->beginTransaction();
  try {
    $check = $db->query("SELECT id FROM collaborators WHERE d_id=".$MYACCOUNT['d_id']." AND call_id=$call_id")->fetch();
    if (!$title || !$genre || !$type || !$storyline || !$check) throw new Exception();
    if ($genre == $genre2) {
      echo json_encode(array("status"=>"failed", "message"=>"Please choose different genres."));
      return;
    }
    $db->query("UPDATE calls SET title='$title', type=$type, genre=$genre, genre2=$genre2, storyline='$storyline' WHERE id=$call_id");

    if ($poster) {
      $src = null;
      if ($poster["type"] == "image/png") $src = imagecreatefrompng($poster["tmp_name"]);
      else if ($poster["type"] == "image/jpeg") $src = imagecreatefromjpeg($poster["tmp_name"]);
      else {
        echo json_encode(array("status"=>"failed", "message"=>".JPG or .PNG posters please"));
        return;
      }
      list($width, $height) = getimagesize($poster['tmp_name']);
      $dst = imagecreatetruecolor(154, 235);
      imagecopyresampled($dst, $src, 0, 0, 0, 0, 154, 235, $width, $width*(235.0/154.0));
      $filename = $call_id.".jpg";
      imagejpeg($dst, "../assets/posters/".$filename, 90);
      $db->query("DELETE FROM assets WHERE page_id=$call_id AND type=5");
      $db->query("INSERT INTO assets VALUES (null, $call_id, '', '$filename', 5, NOW())");
    }
    if ($script) {
      $ext = pathinfo($script['name'])['extension'];
      $filename = $call_id.".".$ext;
      move_uploaded_file($script['tmp_name'], "../assets/scripts/".$filename);
      $db->query("DELETE FROM assets WHERE page_id=$call_id AND type=6");
      $db->query("INSERT INTO assets VALUES (null, $call_id, '', '$filename', 6, NOW())");
    }

    $db->commit();
    echo json_encode(array("status"=>"ok"));
  } catch (Exception $e) {
    echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields."));
  }
}

else if ($MYACCOUNT && $func == 'addToCall') {
  $call_id = getInt('call_id');
  $auditions = getArray("auditions");
  $shootings = getArray("shootings");
  $characters = getArray("characters");
  $collaborators = getArray("collaborators");

  $db->beginTransaction();
  try {
    $check = $db->query("SELECT id FROM collaborators WHERE d_id=".$MYACCOUNT['d_id']." AND call_id=$call_id")->fetch();
    if (!$check) throw new Exception();
    foreach ($auditions as $d) {
      $time = strToDate($d["time"]);
      $place = htmlentities(addslashes(trim($d["place"])));
      if (!$time || !$place) throw new Exception();
      $db->query("INSERT INTO auditions VALUES (null, $call_id, '$time', '$place')");
    }
    foreach ($shootings as $d) {
      $from = strToDate($d["from"]);
      $to = strToDate($d["to"]);
      if (!$from || !$to) throw new Exception();
      $db->query("INSERT INTO shootings VALUES (null, $call_id, '$from', '$to')");
    }
    foreach ($collaborators as $d) {
      $name = htmlentities(addslashes(trim($d["name"])));
      $check = $db->query("SELECT d_id, (SELECT COUNT(*) FROM collaborators WHERE collaborators.d_id=accounts.d_id AND call_id=".$call_id.") as duplicate FROM accounts WHERE SUBSTRING(email, 1, 8)='$name'")->fetch();
      if (!$check) {
        echo json_encode(array("status"=>"failed", "message"=>$name." does not exist"));
        return;
      } else if ($check['duplicate']) {
        echo json_encode(array("status"=>"failed", "message"=>$name." is already a collaborator"));
        return;
      }
      $db->query("INSERT INTO collaborators VALUES (null, $call_id, ".$check['d_id'].", NOW())");
    }
    foreach ($characters as $d) {
      $name = htmlentities(addslashes($d["name"]));
      $min = intval($d["min"]);
      $max = intval($d["max"]);
      $gender = intval($d["gender"]);
      $description = htmlentities(addslashes(trim($d["description"])));
      if (!$name || !$min || !$max || !$gender || !$description) throw new Exception();
      $db->query("INSERT INTO characters VALUES (null, $call_id, '$name', $min, $max, $gender, '$description')");
    }
    $db->commit();
    echo json_encode(array("status"=>"ok"));
  } catch (Exception $e) {
    echo json_encode(array("status"=>"failed", "message"=>$e->getMessage()));
  }
}

else if ($MYACCOUNT && $func == 'remove') {
  $type = getInt('type');
  $id = getInt('id');

  if ($type == 1) {
    $count = $db->query("SELECT COUNT(*) FROM auditions, (SELECT call_id FROM auditions WHERE id=".$id." LIMIT 1) as t2 JOIN collaborators ON t2.call_id=collaborators.call_id WHERE collaborators.d_id=".$MYACCOUNT['d_id']." AND auditions.call_id=t2.call_id GROUP BY auditions.call_id")->fetch()["COUNT(*)"];
    if ($count > 1) $db->query("DELETE FROM auditions WHERE id=".$id);
    else {
      echo json_encode(array("status"=>"failed", "message"=>"You must have at least one audition time."));
      return;
    }
  } else if ($type == 2) {
    $count = $db->query("SELECT COUNT(*) FROM shootings, (SELECT call_id FROM shootings WHERE id=".$id." LIMIT 1) as t2 JOIN collaborators ON t2.call_id=collaborators.call_id WHERE collaborators.d_id=".$MYACCOUNT['d_id']." AND shootings.call_id=t2.call_id GROUP BY shootings.call_id")->fetch()["COUNT(*)"];
    if ($count > 1) $db->query("DELETE FROM shootings WHERE id=".$id);
    else {
      echo json_encode(array("status"=>"failed", "message"=>"You must have at least one shooting date."));
      return;
    }
  } else if ($type == 3) {
    $count = $db->query("SELECT COUNT(*) FROM characters, (SELECT call_id FROM characters WHERE id=".$id." LIMIT 1) as t2 JOIN collaborators ON t2.call_id=collaborators.call_id WHERE collaborators.d_id=".$MYACCOUNT['d_id']." AND characters.call_id=t2.call_id GROUP BY characters.call_id")->fetch()["COUNT(*)"];
    if ($count > 1) {
      $db->query("DELETE FROM interested WHERE char_id=".$id);
      $db->query("DELETE FROM characters WHERE id=".$id);
    } else {
      echo json_encode(array("status"=>"failed", "message"=>"You must have at least one character."));
      return;
    }
  } else if ($type == 4) {
    $count = $db->query("SELECT COUNT(*) FROM collaborators, (SELECT call_id FROM collaborators WHERE id=".$id." LIMIT 1) as t2 WHERE collaborators.d_id=".$MYACCOUNT['d_id']." AND collaborators.call_id=t2.call_id GROUP BY collaborators.call_id")->fetch()["COUNT(*)"];
    if ($count > 1) $db->query("DELETE FROM collaborators WHERE id=".$id);
    else {
      echo json_encode(array("status"=>"failed", "message"=>"You must have at least one collaborator."));
      return;
    }
  }
  echo json_encode(array("status"=>"ok"));
}

else if ($MYACCOUNT && $func == 'closeCall') {
  $call_id = getInt('call_id');

  $db->beginTransaction();
  $count = $db->query("SELECT COUNT(*) FROM calls JOIN collaborators ON calls.id=collaborators.call_id WHERE calls.id=$call_id AND collaborators.d_id=".$MYACCOUNT['d_id'])->fetch()["COUNT(*)"];
  if ($count > 0) {
    $db->query("DELETE FROM calls WHERE id=".$call_id);
    $db->query("DELETE interested, characters FROM interested JOIN characters ON interested.char_id=characters.id AND call_id=".$call_id);
    $db->query("DELETE FROM auditions WHERE call_id=".$call_id);
    $db->query("DELETE FROM shootings WHERE call_id=".$call_id);
    $db->query("DELETE FROM assets WHERE page_id=".$call_id);
    $db->commit();
  } else {
    echo json_encode(array("status"=>"failed", "message"=>"You do not belong to this call"));
    return;
  }
  echo json_encode(array("status"=>"ok"));
}

else echo json_encode(array("status"=>"failed", "message"=>"That function does not exist", "func"=>$func));

function getConfirmation() {
  $confirmations = array("Nice one :)", "It's lit!", "Dope pic", "10/10 would recommend", "Good taste!", "You look beautiful :)", "Gorgeus Face", "Damn shawty get low");
  return $confirmations[rand(0, count($confirmations)-1)];
}
function get($s) { return isset($_POST[$s]) ? htmlentities(addslashes(trim($_POST[$s]))) : null; }
function getArray($s) { return isset($_POST[$s]) ? json_decode($_POST[$s], true) : array(); }
function getWords($s) { return isset($_POST[$s]) ? addslashes(ucwords(trim($_POST[$s]))) : null; }
function getInt($s) { return isset($_POST[$s]) ? addslashes(intval(trim($_POST[$s]))) : null; }
function getDouble($s) { return isset($_POST[$s]) ? addslashes(doubleval(trim($_POST[$s]))) : null; }
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

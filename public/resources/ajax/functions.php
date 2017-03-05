<?php include "../../../inc/db.php";

// ACCOUNTS SCHEMA id INT PRIMARY KEY AUTO_INCREMENT, token VARCHAR(40), username VARCHAR(40), email VARCHAR(40), firstname VARCHAR(40), lastname VARCHAR(40), role INT, gender INT, dob DATETIME, created DATETIME, last_access DATETIME
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
        $db->query("INSERT INTO accounts VALUES (null, '$token', '$username', '$email', null, null, null, null, null, NOW(), NOW())");
        setCookie("token", $token, time()+3600*24*365, "/");
      }
      echo json_encode(array("status"=>"ok", "message"=>"You logged in"));
    } else echo json_encode(array("status"=>"failed", "message"=>"Invalid credentials"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == "complete") {
  $firstname = getWords("firstname");
  $lastname = getWords("lastname");
  $role = getInt("role");
  $gender = getInt("gender");
  $month = getInt("month");
  $day = getInt("day");
  $year = getInt("year");

  if ($role && $gender && $firstname && $lastname && $month && $day && $year) {
    if ($month <= 12 && $month >= 0 && $day >= 0 && $day <= 31 && $year >= 0 && $year < 3000) {
      if ($year < 100 && $year >= 17) $year = $year + 1900;
      else if ($year < 100 && $year < 17) $year = $year + 2000;
      $db->query("UPDATE accounts SET firstname='$firstname', lastname='$lastname', role=$role, gender=$gender, dob=STR_TO_DATE('$month $day $year','%m %d %Y') WHERE token='$token'");
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
  $gender_c1 = getInt("gender_c1");
  $name_c1 = get("name_c1");
  $min_age_c1 = getInt("min_age_c1");
  $max_age_c1 = getInt("max_age_c1");
  $description_c1 = get("description_c1");

  if ($title && $type && $location && $audition_time && $description && $name_c1 && $gender_c1 && $min_age_c1 && $max_age_c1 && $description_c1) {
    // try {
    //   $director_id = intval($MYACCOUNT['id']);
    //   // echo json_encode(array("status"=>"ok", "message"=>"[".$time."]"));
    //   // return;
    //   $db->query("INSERT INTO calls VALUES ((SELECT UUID_short()), $director_id, '$title', $type, '$description', '$location', '$audition_time', NOW())");
    //   $call_id = $db->query("SELECT id FROM calls ORDER BY id DESC")->fetch()['id'];
    //   $db->query("INSERT INTO characters VALUES (null, $call_id, '$name_c1', '$description_c1', $min_age_c1, $max_age_c1, $gender_c1)");
    //   echo json_encode(array("status"=>"ok", "message"=>"OMZ Woah Woah"));
    // } catch (Exception $e) {
    //   echo json_encode(array("status"=>"ok", "message"=>$e->getMessage()));
    // }
    // return;

    $director_id = intval($MYACCOUNT['id']);
    $db->query("INSERT INTO calls VALUES ((SELECT UUID_short()), $director_id, '$title', $type, '$description', '$location', '$audition_time', NOW())");
    $call_id = $db->query("SELECT id FROM calls ORDER BY id DESC")->fetch()['id'];
    $db->query("INSERT INTO characters VALUES (null, $call_id, '$name_c1', '$description_c1', $min_age_c1, $max_age_c1, $gender_c1)");
    echo json_encode(array("status"=>"ok", "message"=>"It Worked! Check the database Mr. Partida ;)"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else echo json_encode(array("status"=>"failed", "message"=>"That function does not exist"));


function get($s) { return isset($_POST[$s]) ? trim($_POST[$s]) : null; }
function getWords($s) { return isset($_POST[$s]) ? ucwords(trim($_POST[$s])) : null; }
function getInt($s) { return isset($_POST[$s]) ? intval(trim($_POST[$s])) : null; }
function getDouble($s) { return isset($_POST[$s]) ? doubleval(trim($_POST[$s])) : null; }

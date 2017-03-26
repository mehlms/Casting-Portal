<?php include "../../../inc/db.php";

$token = "";
if ($MYACCOUNT) $token = $MYACCOUNT['token'];
$func = get("func");

if ($func == "login") {
  $email = get("email");
  $password = get("password");

  if ($email && $password) {
    $CHAPMANAPI = json_decode(post("http://localhost:8080/resources/ajax/api.php", array("email" => $email, "password" => $password)), true);
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
      $url = "";
      if ($MYACCOUNT && $MYACCOUNT['mode']) $url .= "/director/".$MYACCOUNT['d_id'];
      else if ($MYACCOUNT && !$MYACCOUNT['mode']) $url .= "/actor/".$MYACCOUNT['a_id'];
      else $url .= "/complete/";
      echo json_encode(array("status"=>"ok", "url"=>$url));
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

  if ($gender && $firstname && $lastname && $month && $day && $year) {
    if ($month <= 12 && $month >= 0 && $day >= 0 && $day <= 31 && $year >= 0 && $year < 3000) {
      if ($year < 100 && $year >= 17) $year = $year + 1900;
      else if ($year < 100 && $year < 17) $year = $year + 2000;
      $db->query("UPDATE accounts SET firstname='$firstname', lastname='$lastname', gender=$gender, birthdate=STR_TO_DATE('$month $day $year','%m %d %Y') WHERE token='$token'");
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
    $director_id = intval($MYACCOUNT['id']);
    $db->query("INSERT INTO calls VALUES ((SELECT UUID_short()), $director_id, '$title', $type, '$description', '$location', '$audition_time', NOW())");
    $call_id = $db->query("SELECT id FROM calls ORDER BY id DESC")->fetch()['id'];
    $db->query("INSERT INTO characters VALUES (null, $call_id, '$name_c1', '$description_c1', $min_age_c1, $max_age_c1, $gender_c1)");
    echo json_encode(array("status"=>"ok", "message"=>"It Worked! Check the database Mr. Partida ;)"));
  } else echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

else if ($MYACCOUNT && $func == "toggleMode") {
  $mode = getInt("mode");
  $db->query("UPDATE accounts SET mode=$mode WHERE token='$token'");
}
else echo json_encode(array("status"=>"failed", "message"=>"That function does not exist"));


function get($s) { return isset($_POST[$s]) ? trim($_POST[$s]) : null; }
function getWords($s) { return isset($_POST[$s]) ? ucwords(trim($_POST[$s])) : null; }
function getInt($s) { return isset($_POST[$s]) ? intval(trim($_POST[$s])) : null; }
function getDouble($s) { return isset($_POST[$s]) ? doubleval(trim($_POST[$s])) : null; }

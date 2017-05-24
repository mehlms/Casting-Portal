<?php include "../../../inc/db.php";

$email = get("email");
$password = get("password");

if ($email && $password) {
  $api = json_decode(post("http://localhost/resources/ajax/api.php", array("email" => $email, "password" => $password)), true);
  if ($api["status"] == "ok") {
    $MYACCOUNT = $db->query("SELECT * FROM accounts WHERE email='$email'")->fetch();
    if (!$MYACCOUNT) {
      $db->query("INSERT INTO accounts VALUES (null, SUBSTRING((SELECT UUID_short()), 9), SUBSTRING((SELECT UUID_short()), 9), '', '', 0, '".sha1(time().rand())."', '$email', null, null, null, null, 0, 0, NOW(), NOW())");
      $MYACCOUNT = $db->query("SELECT * FROM accounts WHERE email='$email'")->fetch();
    }
    setCookie("id", $MYACCOUNT['id'], time()+3600*24*365, "/");
    setCookie("token", $MYACCOUNT['token'], time()+3600*24*365, "/");
    ok();
  } else failed("Invalid credentials");
} else failed("Please fill in all fields");

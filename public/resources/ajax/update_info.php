<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$firstname = ucwords(get("firstname"));
$lastname = ucwords(get("lastname"));
$birthdate = getDateTime("birthdate");
$gender = getInt("gender");
$bio = nl2br(get("bio"));

if ($firstname && $lastname && $birthdate && $gender) {
  $db->query("UPDATE accounts SET ".($MYACCOUNT['mode'] ? "d_bio" : "a_bio")."='$bio', firstname='$firstname', lastname='$lastname', gender=$gender, birthdate='$birthdate' WHERE id=".$MYACCOUNT['id']);
  ok();
} else failed("Please fill in all fields");

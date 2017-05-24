<?php include "../../../inc/db.php";

$role = getInt("role");
$gender = getInt("gender");
$firstname = ucwords(get("firstname"));
$lastname = ucwords(get("lastname"));
$birthdate = getDateTime("birthdate");

if ($MYACCOUNT && $role != -1 && $gender && $firstname && $lastname && $birthdate) {
  $MYACCOUNT['age'] = date_diff(date_create($birthdate), date_create('now'))->y;
  $db->query("UPDATE accounts SET firstname='$firstname', lastname='$lastname', gender=$gender, mode=$role, birthdate='$birthdate', looks_min=".$MYACCOUNT['age'].", looks_max=".$MYACCOUNT['age']." WHERE id=".$MYACCOUNT['id']);
  ok();
} else failed("Please fill in all fields");

<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

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
      failed($name." does not exist");
      return;
    } else if ($check['duplicate']) {
      failed($name." is already a collaborator");
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
  ok();
} catch (Exception $e) {
  echo $e->getMessage();
}

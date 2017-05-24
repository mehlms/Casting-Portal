<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$call_id = getInt('id');

$db->beginTransaction();
$count = $db->query("SELECT COUNT(*) FROM calls JOIN collaborators ON calls.id=collaborators.call_id WHERE calls.id=$call_id AND collaborators.d_id=".$MYACCOUNT['d_id'])->fetch()["COUNT(*)"];
if ($MYACCOUNT && $count > 0) {
  $db->query("DELETE FROM calls WHERE id=".$call_id);
  $db->query("DELETE interested, characters FROM interested JOIN characters ON interested.char_id=characters.id AND call_id=".$call_id);
  $db->query("DELETE FROM auditions WHERE call_id=".$call_id);
  $db->query("DELETE FROM shootings WHERE call_id=".$call_id);
  $db->query("DELETE FROM assets WHERE page_id=".$call_id);
  $db->commit();
  ok();
} else echo json_encode(array("status"=>"failed", "message"=>"You do not belong to this call"));

<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$char_id = getInt("char_id");
$check = $db->query("SELECT COUNT(*) as interested, (SELECT COUNT(*) FROM assets WHERE page_id=".$MYACCOUNT['a_id']." AND type=1) as profile_pic, (SELECT COUNT(*) FROM characters WHERE characters.id=$char_id AND ".$MYACCOUNT['looks_max'].">=min AND ".$MYACCOUNT['looks_min']."<=max AND (gender=3 OR gender=".$MYACCOUNT['gender'].")) as can_interested FROM interested WHERE a_id=".$MYACCOUNT['a_id']." AND char_id=$char_id")->fetch();
if ($check['interested']) {
  $db->query("DELETE FROM interested WHERE a_id=".$MYACCOUNT['a_id']." AND char_id=$char_id");
  echo json_encode(array("status"=>"ok", "message"=>"Revoked interest", "interested"=>0));
} else if ($check['can_interested'] && $check['profile_pic']) {
  $db->query("INSERT INTO interested VALUES (null, ".$MYACCOUNT['a_id'].", $char_id, NOW())");
  echo json_encode(array("status"=>"ok", "interested"=>1));
} else failed("You must first upload a profile picture");

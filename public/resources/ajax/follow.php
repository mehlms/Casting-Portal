<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$follow_to = getInt("follow_to");
if ($follow_to) {
  $id = $db->query("SELECT id FROM follow WHERE follow_from=$page_id AND follow_to=$follow_to")->fetch()['id'];
  if ($id) $db->query("DELETE FROM follow WHERE id=$id");
  else $db->query("INSERT INTO follow VALUES (null, $page_id, $follow_to, NOW())");
  ok();
} else failed("Please fill in all fields");

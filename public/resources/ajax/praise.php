<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$praise_to = getInt("praise_to");
$heart = getInt("heart");
$comment = get("comment");

if ($praise_to && $heart || $comment) {
  if ($heart) {
    $id = $db->query("SELECT id FROM praise WHERE praise_from=$page_id AND praise_to=$praise_to AND heart=1")->fetch()['id'];
    if ($id) $db->query("DELETE FROM praise WHERE id=$id");
    else $db->query("INSERT INTO praise VALUES (null, $page_id, $praise_to, 1, null, NOW())");
    echo json_encode(array("status"=>"ok", "heart"=>1));
  } else {
    $db->query("INSERT INTO praise VALUES (null, $page_id, $praise_to, 0, '$comment', NOW())");
    echo json_encode(array("status"=>"ok", "heart"=>0));
  }
} else {
  failed("Please fill in all fields");
}

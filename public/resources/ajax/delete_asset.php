<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$id = getInt('id');
if ($id) {
  $asset = $db->query("SELECT * FROM assets WHERE id=$id AND page_id=$page_id")->fetch();
  if ($asset && $asset['type'] == 2) {
    unlink('../assets/photos/'.$asset['url']);
    unlink('../assets/photos_large/'.$asset['url']);
  }
  $db->query("DELETE FROM assets WHERE id=$id AND page_id=$page_id");
  ok();
}

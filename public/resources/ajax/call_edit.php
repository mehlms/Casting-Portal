<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$call_id = getInt('call_id');
$title = ucwords(get("title"));
$type = getInt("type");
$genre = getInt("genre");
$genre2 = getInt("genre2");
$storyline = get("storyline");
$script = getFile('script');
$poster = getFile('poster');

$db->beginTransaction();
try {
  $check = $db->query("SELECT id FROM collaborators WHERE d_id=".$MYACCOUNT['d_id']." AND call_id=$call_id")->fetch();
  if (!$MYACCOUNT || !$title || !$genre || !$type || !$storyline || !$check) throw new Exception();
  if ($genre == $genre2) {
    failed("Please choose different genres");
    return;
  }
  $db->query("UPDATE calls SET title='$title', type=$type, genre=$genre, genre2=$genre2, storyline='$storyline' WHERE id=$call_id");

  if ($poster) {
    $src = null;
    if ($poster["type"] == "image/png") $src = imagecreatefrompng($poster["tmp_name"]);
    else if ($poster["type"] == "image/jpeg") $src = imagecreatefromjpeg($poster["tmp_name"]);
    else {
      failed(".JPG or .PNG posters please");
      return;
    }
    list($width, $height) = getimagesize($poster['tmp_name']);
    $dst = imagecreatetruecolor(154, 235);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, 154, 235, $width, $width*(235.0/154.0));
    $filename = $call_id.".jpg";
    imagejpeg($dst, "../assets/posters/".$filename, 90);
    $db->query("DELETE FROM assets WHERE page_id=$call_id AND type=5");
    $db->query("INSERT INTO assets VALUES (null, $call_id, '', '$filename', 5, NOW())");
  }
  if ($script) {
    $ext = pathinfo($script['name'])['extension'];
    $filename = $call_id.".".$ext;
    move_uploaded_file($script['tmp_name'], "../assets/scripts/".$filename);
    $db->query("DELETE FROM assets WHERE page_id=$call_id AND type=6");
    $db->query("INSERT INTO assets VALUES (null, $call_id, '', '$filename', 6, NOW())");
  }

  $db->commit();
  ok();
} catch (Exception $e) {
  failed("Please fill in all fields.");
}

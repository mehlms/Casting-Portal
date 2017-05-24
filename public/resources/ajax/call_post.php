<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$title = ucwords(get("title"));
$type = getInt("type");
$genre = getInt("genre");
$genre2 = getInt("genre2");
$auditions = getArray("auditions");
$shootings = getArray("shootings");
$storyline = get("storyline");
$characters = getArray("characters");
$script = getFile('script');
$poster = getFile('poster');

$db->beginTransaction();
try {
  if (!$title || !$genre || !$type || !$storyline || !count($auditions) || !count($shootings) || !count($characters)) throw new Exception();
  if ($genre == $genre2) {
    echo json_encode(array("status"=>"failed", "message"=>"Please choose different genres."));
    return;
  }
  $db->query("INSERT INTO calls VALUES (SUBSTRING((SELECT UUID_short()), 9), '$title', $type, $genre, $genre2, '$storyline', NOW())");
  $call_id = $db->query("SELECT id FROM calls ORDER BY id DESC")->fetch()['id'];
  $db->query("INSERT INTO collaborators VALUES (null, $call_id, ".$MYACCOUNT['d_id'].", NOW())");

  foreach ($auditions as $d) {
    $time = strToDate($d["time"]);
    $place = htmlentities(addslashes($d["place"]));
    if (!$time || !$place) throw new Exception();
    $db->query("INSERT INTO auditions VALUES (null, $call_id, '$time', '$place')");
  }
  foreach ($shootings as $d) {
    $from = strToDate($d["from"]);
    $to = strToDate($d["to"]);
    if (!$from || !$to) throw new Exception();
    $db->query("INSERT INTO shootings VALUES (null, $call_id, '$from', '$to')");
  }
  foreach ($characters as $d) {
    $name = htmlentities(addslashes($d["name"]));
    $min = intval($d["min"]);
    $max = intval($d["max"]);
    $gender = intval($d["gender"]);
    $description = htmlentities(addslashes($d["description"]));
    if (!$name || !$min || !$max || !$gender || !$description) throw new Exception();
    $db->query("INSERT INTO characters VALUES (null, $call_id, '$name', $min, $max, $gender, '$description')");
  }

  if ($poster) {
    $src = null;
    if ($poster["type"] == "image/png") $src = imagecreatefrompng($poster["tmp_name"]);
    else if ($poster["type"] == "image/jpeg") $src = imagecreatefromjpeg($poster["tmp_name"]);
    else {
      echo json_encode(array("status"=>"failed", "message"=>".JPG or .PNG posters please"));
      return;
    }
    list($width, $height) = getimagesize($poster['tmp_name']);
    $dst = imagecreatetruecolor(154, 235);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, 154, 235, $width, $width*(235.0/154.0));
    $filename = $call_id.".jpg";
    imagejpeg($dst, "../assets/posters/".$filename, 90);
    $db->query("INSERT INTO assets VALUES (null, $call_id, '', '$filename', 5, NOW())");
  }
  if ($script) {
    $ext = pathinfo($script['name'])['extension'];
    $filename = $call_id.".".$ext;
    move_uploaded_file($script['tmp_name'], "../assets/scripts/".$filename);
    $db->query("INSERT INTO assets VALUES (null, $call_id, '', '$filename', 6, NOW())");
  }

  $db->commit();
  echo json_encode(array("status"=>"ok"));
} catch (Exception $e) {
  echo json_encode(array("status"=>"failed", "message"=>"Please fill in all fields"));
}

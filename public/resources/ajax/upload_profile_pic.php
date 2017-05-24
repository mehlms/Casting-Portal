<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$pic = getFile('pic');
$src = null;
if ($pic["type"] == "image/png") $src = imagecreatefrompng($pic["tmp_name"]);
else if ($pic["type"] == "image/jpeg") $src = imagecreatefromjpeg($pic["tmp_name"]);
else {
  failed(".JPG and .PNG only please");
  return;
}

list($width, $height) = getimagesize($pic['tmp_name']);
$xPos = 0;
$yPos = 0;
if ($width > $height) $xPos = ($width - $height) / 2;
else $yPos = ($height - $width) / 2;

$dst = imagecreatetruecolor(204, 204);
imagecopyresampled($dst, $src, 0, 0, $xPos, $yPos, 204, 204, min($width, $height), min($width, $height));

$filename = $page_id.".jpg";
$existenceCheck = $db->query("SELECT url FROM assets WHERE page_id=$page_id AND type=1")->fetch();
if ($existenceCheck) {
  $filename = $existenceCheck['url'];
  imagejpeg($dst, "../assets/profile/".$filename, 90);
} else {
  imagejpeg($dst, "../assets/profile/".$filename, 90);
  $db->query("INSERT INTO assets VALUES (null, $page_id, '', '$filename', 1, NOW())");
}
ok();

<?php include "../../../inc/db.php";

$pic = getFile('pic');

if (!$pic || $MYACCOUNT == null) {
  failed("You are not logged in");
  return;
}

$photoCount = $db->query("SELECT COUNT(*) FROM assets WHERE page_id=$page_id AND type=2")->fetch()["COUNT(*)"];
if ($photoCount >= 15) {
  failed("Only 15 pictures allowed");
  return;
}

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
$wAspect = 1.0;
$hAspect = 1.0;
if ($width > $height) {
  $hAspect = $height / $width;
  $xPos = ($width - $height) / 2;
} else {
  $wAspect = $width / $height;
  $yPos = ($height - $width) / 2;
}

$dst = imagecreatetruecolor(176, 176);
imagecopyresampled($dst, $src, 0, 0, $xPos, $yPos, 176, 176, min($width, $height), min($width, $height));
$dst_large = imagecreatetruecolor(550*$wAspect, 550*$hAspect);
imagecopyresampled($dst_large, $src, 0, 0, 0, 0, 550*$wAspect, 550*$hAspect, $width, $height);

$filename = time().rand(1000,9999).".jpg";
imagejpeg($dst, "../assets/photos/".$filename, 90);
imagejpeg($dst_large, "../assets/photos_large/".$filename, 90);
$db->query("INSERT INTO assets VALUES (null, $page_id, '', '$filename', 2, NOW())");
ok();

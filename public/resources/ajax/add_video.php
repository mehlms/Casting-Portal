<?php include "../../../inc/db.php";

$title = get("title");
$youtubeLink = get('youtubeLink');
$vimeoLink = get('vimeoLink');

if ($MYACCOUNT && $title && ($youtubeLink || $vimeoLink)) {
  if ($youtubeLink) {
    preg_match('/v=([^&]*)/', $youtubeLink, $matches);
    if ($matches) {
      $db->query("INSERT INTO assets VALUES (null, $page_id, '$title', '".$matches[1]."', 3, NOW())");
      ok();
    } else failed("Invalid URL Format");
  } else if ($vimeoLink) {
    preg_match('/vimeo\.com\/(.*)/', $vimeoLink, $matches);
    if ($matches) {
      $db->query("INSERT INTO assets VALUES (null, $page_id, '$title', '".$matches[1]."', 4, NOW())");
      ok();
    } else failed("Invalid URL Format");
  }
} else failed("Please fill in all fields.");

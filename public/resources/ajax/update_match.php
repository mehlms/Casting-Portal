<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$looks_min = getInt("looks_min");
$looks_max = getInt("looks_max");

if ($looks_min && $looks_max && $looks_max >= $looks_min) {
  $db->query("UPDATE accounts SET looks_min=$looks_min, looks_max=$looks_max WHERE id=".$MYACCOUNT['id']);
  ok();
} else failed("Please fill in all fields");

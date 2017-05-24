<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$type = getInt('type');
$id = getInt('id');

if ($type == 1) {
  $count = $db->query("SELECT COUNT(*) FROM auditions, (SELECT call_id FROM auditions WHERE id=".$id." LIMIT 1) as t2 JOIN collaborators ON t2.call_id=collaborators.call_id WHERE collaborators.d_id=".$MYACCOUNT['d_id']." AND auditions.call_id=t2.call_id GROUP BY auditions.call_id")->fetch()["COUNT(*)"];
  if ($count > 1) $db->query("DELETE FROM auditions WHERE id=".$id);
  else {
    failed("You must have at least one audition time");
    return;
  }
} else if ($type == 2) {
  $count = $db->query("SELECT COUNT(*) FROM shootings, (SELECT call_id FROM shootings WHERE id=".$id." LIMIT 1) as t2 JOIN collaborators ON t2.call_id=collaborators.call_id WHERE collaborators.d_id=".$MYACCOUNT['d_id']." AND shootings.call_id=t2.call_id GROUP BY shootings.call_id")->fetch()["COUNT(*)"];
  if ($count > 1) $db->query("DELETE FROM shootings WHERE id=".$id);
  else {
    failed("You must have at least one shooting date");
    return;
  }
} else if ($type == 3) {
  $count = $db->query("SELECT COUNT(*) FROM characters, (SELECT call_id FROM characters WHERE id=".$id." LIMIT 1) as t2 JOIN collaborators ON t2.call_id=collaborators.call_id WHERE collaborators.d_id=".$MYACCOUNT['d_id']." AND characters.call_id=t2.call_id GROUP BY characters.call_id")->fetch()["COUNT(*)"];
  if ($count > 1) {
    $db->query("DELETE FROM interested WHERE char_id=".$id);
    $db->query("DELETE FROM characters WHERE id=".$id);
  } else {
    failed("You must have at least one character");
    return;
  }
} else if ($type == 4) {
  $count = $db->query("SELECT COUNT(*) FROM collaborators, (SELECT call_id FROM collaborators WHERE collaborators.d_id=".$MYACCOUNT['d_id']." AND id=$id LIMIT 1) as t2 WHERE collaborators.call_id=t2.call_id GROUP BY collaborators.call_id")->fetch()["COUNT(*)"];
  if ($count > 1) $db->query("DELETE FROM collaborators WHERE id=".$id);
  else {
    failed("You must have at least one collaborator");
    return;
  }
}
ok();

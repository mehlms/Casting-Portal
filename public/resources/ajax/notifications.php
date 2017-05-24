<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$notifications = array();
if ($MYACCOUNT['mode']) {
  $praise = $db->query("SELECT accounts.a_id as id, heart, comment, firstname, lastname, praise.added, (SELECT 1) as type FROM praise JOIN accounts ON praise_from=accounts.a_id WHERE praise_to=".$MYACCOUNT['d_id']." LIMIT 30")->fetchAll();
  $interested = $db->query("SELECT accounts.a_id as id, firstname, lastname, name, interested.added, (SELECT 2) as type FROM interested JOIN accounts ON interested.a_id=accounts.a_id JOIN characters ON interested.char_id=characters.id JOIN calls ON characters.call_id=calls.id JOIN collaborators ON collaborators.d_id=".$MYACCOUNT['d_id']." LIMIT 30")->fetchAll();
  $notifications = array_merge($praise, $interested);
  usort($notifications, 'sortDate');
} else {
  $praise = $db->query("SELECT accounts.d_id as id, heart, comment, firstname, lastname, praise.added, (SELECT 1) as type FROM praise JOIN accounts ON praise_from=accounts.d_id WHERE praise_to=".$MYACCOUNT['a_id']." LIMIT 30")->fetchAll();
  $matches = $db->query("SELECT calls.id, title, calls.added, (SELECT 3) as type FROM calls
                          JOIN (SELECT call_id, COUNT(*) as char_count FROM characters WHERE min<=".$MYACCOUNT['looks_max']." AND max>=".$MYACCOUNT['looks_min']." AND (gender=3 OR gender=".$MYACCOUNT['gender'].") GROUP BY call_id) as t2
                          ON t2.call_id=calls.id
                          WHERE calls.type NOT IN (SELECT class_id FROM classesFilter WHERE a_id=".$MYACCOUNT['a_id'].") AND
                          calls.genre NOT IN (SELECT genre_id FROM genresFilter WHERE a_id=".$MYACCOUNT['a_id'].") AND
                          calls.genre2 NOT IN (SELECT genre_id FROM genresFilter WHERE a_id=".$MYACCOUNT['a_id'].")")->fetchAll();
  $following = $db->query("SELECT collaborators.call_id as id, calls.title, firstname, lastname, follow.added, (SELECT 4) as type FROM follow JOIN accounts ON follow_to=accounts.d_id JOIN collaborators ON accounts.d_id=collaborators.d_id JOIN calls ON collaborators.call_id=calls.id WHERE follow_from=".$MYACCOUNT['a_id']." LIMIT 30")->fetchAll();
  $notifications = array_merge($praise, $matches);
  $notifications = array_merge($notifications, $following);
  usort($notifications, 'sortDate');
}
echo json_encode(array("status"=>"ok", "notifications"=>array_splice($notifications, 0, 30)));

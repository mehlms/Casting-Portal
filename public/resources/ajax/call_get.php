<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$id = getInt("id");
$call = $db->query("SELECT calls.*, class, (SELECT genre FROM genres WHERE calls.genre=genres.id) as g1, (SELECT genre FROM genres WHERE calls.genre2=genres.id) as g2, (SELECT url FROM assets WHERE page_id=calls.id AND type=6) as script, (SELECT url FROM assets WHERE page_id=calls.id AND type=5) as poster FROM calls JOIN classes ON calls.type=classes.id WHERE calls.id=$id")->fetch();
if ($call) {
  $collaborators = $db->query("SELECT collaborators.d_id, firstname, lastname FROM collaborators JOIN accounts ON collaborators.d_id=accounts.d_id WHERE call_id=$id ORDER BY collaborators.added")->fetchAll();
  $auditions = $db->query("SELECT *, DATE_FORMAT(audition_time, '%M %D %l:%i%p') as audition_time FROM auditions WHERE call_id=$id")->fetchAll();
  $shootings = $db->query("SELECT DATE_FORMAT(shooting_from, '%M %D') as shooting_from, DATE_FORMAT(shooting_to, '%M %D') as shooting_to FROM shootings WHERE call_id=$id")->fetchAll();
  $characters = $db->query("SELECT id, name, min, max, gender, description, (SELECT COUNT(*) FROM interested WHERE char_id=characters.id AND a_id=$page_id) as interested, (SELECT COUNT(*) FROM characters as c2 WHERE ".$MYACCOUNT['mode']."=0 AND characters.id=c2.id AND ".$MYACCOUNT['looks_max'].">=min AND ".$MYACCOUNT['looks_min']."<=max AND (gender=3 OR gender=".$MYACCOUNT['gender'].")) as can_interested FROM characters WHERE call_id=$id ORDER BY id ASC")->fetchAll();
  echo json_encode(array("status"=>"ok", "call"=>$call, "collaborators"=>$collaborators, "auditions"=>$auditions, "shootings"=>$shootings, "characters"=>$characters));
} else failed("That call does not exist");

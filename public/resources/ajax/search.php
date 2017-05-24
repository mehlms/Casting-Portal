<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$type = getInt('type');
$query = get('query');
$results = array();
if ($query) {
  $calls = $db->query("SELECT calls.id, calls.title, url, (SELECT 1) as type FROM calls LEFT JOIN assets ON page_id=calls.id WHERE calls.title LIKE '%$query%' LIMIT 2")->fetchAll();
  $directors = $db->query("SELECT d_id, (SELECT CONCAT(firstname, ' ', lastname)) as title, url, (SELECT 2) as type FROM accounts LEFT JOIN assets ON page_id=accounts.d_id WHERE (SELECT CONCAT(firstname, ' ', lastname)) LIKE '%$query%' LIMIT 2")->fetchAll();
  $talent = $db->query("SELECT a_id, (SELECT CONCAT(firstname, ' ', lastname)) as title, url, (SELECT 3) as type FROM accounts LEFT JOIN assets ON page_id=accounts.a_id WHERE (SELECT CONCAT(firstname, ' ', lastname)) LIKE '%$query%' LIMIT 2")->fetchAll();
  $results = array_merge($calls, $directors, $talent);
}
echo json_encode(array("status"=>"ok", "results"=>$results));

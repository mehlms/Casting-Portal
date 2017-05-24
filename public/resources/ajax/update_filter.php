<?php include "../../../inc/db.php";
if (!$MYACCOUNT) return;

$genresFilter = getCheckbox('genresFilter');
$classesFilter = getCheckbox('classesFilter');

$db->beginTransaction();
$db->query("DELETE FROM classesFilter WHERE a_id=".$MYACCOUNT['a_id']);
$db->query("DELETE FROM genresFilter WHERE a_id=".$MYACCOUNT['a_id']);
foreach($genresFilter as $key=>$d) if (!$d) $db->query("INSERT INTO genresFilter VALUES (null, ".$MYACCOUNT['a_id'].", $key)");
foreach($classesFilter as $key=>$d) if (!$d)  $db->query("INSERT INTO classesFilter VALUES (null, ".$MYACCOUNT['a_id'].", $key)");
$db->commit();
ok();

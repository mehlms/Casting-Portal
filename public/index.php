<?php include "../inc/header.php"; ?>

<h1 class='underline'>Recently Posted Calls</h1>

<?php
$CALLS = $db->query("SELECT calls.id, calls.d_id, title, firstname, lastname FROM calls JOIN accounts ON calls.d_id=accounts.d_id ORDER BY calls.created_date DESC");
foreach ($CALLS->fetchAll() as $call) {
  echo "<h2><a href='/call/".$call['id']."/'>".$call['title']."</a> by <a href='/director/".$call['d_id']."/'>".$call['firstname']." ".$call['lastname']."</a></h2>";
}
?>

<?php include "../inc/footer.php" ?>

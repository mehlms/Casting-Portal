<?php include "../inc/header.php"; ?>

<?php
$d_id = $MYACCOUNT['d_id'];
$CALLS = $db->query("SELECT id, title FROM calls WHERE d_id=$d_id");
foreach ($CALLS->fetchAll() as $call) {
  $call_id = $call['id'];
  echo "<h1 class='underline'><a href='/call/".$call_id."'>".$call['title']."</a></h1>";
  $CHARACTERS = $db->query("SELECT * FROM characters WHERE call_id=$call_id");
  foreach ($CHARACTERS->fetchAll() as $character) {
    $char_id = $character['id'];
    echo "<h2>Interested in ".$character['name']."</h2>";
    $NOTIFICATIONS = $db->query("SELECT notifications.a_id, firstname, lastname, email FROM notifications JOIN accounts ON notifications.a_id=accounts.a_id WHERE char_id=$char_id");
    foreach ($NOTIFICATIONS->fetchAll() as $notification) {
      echo "Name: <b>".$notification['firstname']." ".$notification['lastname']."</b><br> Email: <b>".$notification['email']."</b>";
    }
  }
  //  = $db->query("SELECT * FROM notifications WHERE type=1 AND d_id=$d_id");
  // foreach ($CHARACTERS->fetchAll() as $call) {

  // }
}
?>

<script>
  function interested(sender, d_id, char_id) {
    post("/resources/ajax/functions.php", {'func': 'interested', 'd_id': d_id, 'char_id': char_id}, function(r) {
      console.log(r)
      r = JSON.parse(r)
      if (r['status'] == 'ok' && r['interested']) sender.value = "INTERESTED"
      if (r['status'] == 'ok' && !r['interested']) sender.value = "+ I'M INTERESTED"
      addAlert(r["message"])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

<?php include "../inc/header.php"; ?>

<?php
$d_id = $MYACCOUNT['d_id'];
$CALLS = $db->query("SELECT id, title FROM calls WHERE d_id=$d_id");
$count = 0;
foreach ($CALLS->fetchAll() as $call) {
  $count += 1;
  $call_id = $call['id'];
  echo "<h1 class='underline'><a href='/call/".$call_id."'>".$call['title']."</a></h1>";
  $CHARACTERS = $db->query("SELECT * FROM characters WHERE call_id=$call_id");
  foreach ($CHARACTERS->fetchAll() as $character) {
    $char_id = $character['id'];
    echo "<h2>Interested in <b>".$character['name']."</b></h2>";
    $NOTIFICATIONS = $db->query("SELECT notifications.a_id, firstname, lastname, email FROM notifications JOIN accounts ON notifications.a_id=accounts.a_id WHERE char_id=$char_id")->fetchAll();
    if (count($NOTIFICATIONS) > 0) {
      foreach ($NOTIFICATIONS as $notification) {
        echo "<b><a href='/actor/".$notification['a_id']."/'>".$notification['firstname']." ".$notification['lastname']."</a> - ".$notification['email']."</b>";
      }
    } else {
      echo "No one yet.";
    }
  }
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

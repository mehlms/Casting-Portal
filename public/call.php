<?php include "../inc/header.php";
$CALL = $db->query("SELECT calls.id, title, description, firstname, lastname, calls.d_id, audition_time, audition_location FROM calls JOIN accounts ON calls.d_id=accounts.d_id WHERE calls.id='$data'")->fetch();
?>

<h1>Casting Call for <a href='/call/<?php echo $CALL['id'] ?>/'><?php echo $CALL['title'] ?></a></h1>
<h2>Created By <a href='/director/<?php echo $CALL['d_id'] ?>/'><?php echo $CALL['firstname']." ".$CALL['lastname'] ?></a></h2>
<b>Storyline:</b> <?php echo $CALL['description'] ?> <br>
<b>Audition:</b> <?php echo $CALL['audition_time'] ?> <br><br>
<h1 class='underline'>Characters</h1>
<?php
$CHARACTERS = $db->query("SELECT id, name, description, min_age, max_age, gender FROM characters WHERE call_id='$data'");
foreach ($CHARACTERS->fetchAll() as $character) {
  $interested = $db->query("SELECT id FROM notifications WHERE type=1 AND d_id=".$CALL['d_id']." AND char_id=".$character['id']." AND a_id=".$MYACCOUNT['a_id'])->fetch() ? "INTERESTED" : "+ I'M INTERESTED";
  $gender = ($character['gender'] == 1) ? "Male" : "Female";
  echo "<h1>".$character['name']." <input type='button' value=\"".$interested."\" class='subscribe' onclick='interested(this, \"".$CALL['d_id']."\", \"".$character['id']."\")'></h1>
  <b>Description:</b> ".$character['description']."<br>
  <b>Gender:</b> ".$gender." <br>
  <b>Minimum Age:</b> ".$character['min_age']."<br>
  <b>Maximum Age:</b> ".$character['max_age']."<br><br>";
}
?>

<script>
  function interested(sender, d_id, char_id) {
    post("/resources/ajax/functions.php", {'func': 'interested', 'd_id': d_id, 'char_id': char_id}, function(r) {
      r = JSON.parse(r)
      if (r['status'] == 'ok' && r['interested']) sender.value = "INTERESTED"
      if (r['status'] == 'ok' && !r['interested']) sender.value = "+ I'M INTERESTED"
      addAlert(r["message"])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

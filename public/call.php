<?php include "../inc/header.php";
$CALL = $db->query("SELECT calls.id, title, description, firstname, lastname, calls.d_id, audition_dates, shooting_dates, type FROM calls JOIN accounts ON calls.d_id=accounts.d_id WHERE calls.id='$data'")->fetch();
$type = array("Undergraduate Visual Storytelling (FTV 130)",
"Undergraduate Directing 2 (FP 338)",
"Undergraduate Directing 3 (FP 438)",
"Undergraduate Intermediate Production (FP 280)",
"Undergraduate Advanced Production (FP 331)",
"Undergraduate Senior Thesis (FP 497-498)",
"Undergraduate Byte-sized Television (TWP 313)",
"Undergraduate Television Pilots (TWP 398)",
"Undergraduate Digital Arts Project",
"Undergraduate Independent Study",
"Graduate Fundamentals of Directing 1 (FP 538)",
"Graduate Fundamentals of Directing 2 (FP 539)",
"Graduate Intermediate Directing (FP 664)",
"Graduate Advanced Directing (FP 665)",
"Graduate Master Class in Directing (FP 638)",
"Graduate Production Workshop 1 (FP 531)",
"Graduate Production Workshop 2 (FP 532)",
"Graduate Production Workshop 3 (FP 577)",
"Graduate Production Workshop 4 (FP 631)",
"Graduate Thesis (FP 698)",
"Graduate Filmmakers and Actors Workshop (FP 507)",
"Graduate Independent Study",
"Other"
)[$CALL['type']];

?>

<div class="call-header">
  <div class="c1">MOVIE CASTING CALL</div>
  <div class="c2"><?php echo $CALL['title'] ?></div>
</div>
<div class='call-items'>
  <div class='c1'>Director:</div><div class='c2'><a href='/director/<?php echo $CALL['d_id'] ?>/'><?php echo $CALL['firstname']." ".$CALL['lastname'] ?></a></div><br>
  <div class='c1'>Level:</div><div class='c2'><?php echo $type ?></div><br>
  <div class='c1'>Shooting:</div><div class='c2'><?php echo $CALL['shooting_dates'] ?></div><br>
  <div class='c1'>Audition:</div><div class='c2'><?php echo $CALL['audition_dates'] ?></div><br>
  <div class='c1'>Storyline:</div><div class='c2'><?php echo $CALL['description'] ?></div><br>
</div>

<div class='call-header'>
  <div class='c1'>Parts</div>
</div>
<?php
$CHARACTERS = $db->query("SELECT id, name, description, min_age, max_age, gender FROM characters WHERE call_id='$data'");
foreach ($CHARACTERS->fetchAll() as $character) {
  $isInterestedString = "- REVOKE INTEREST";
  $isNotInterestedString = "+ SUBMIT INTEREST FOR ".$character['name'];
  $interested = $db->query("SELECT id FROM notifications WHERE type=1 AND d_id=".$CALL['d_id']." AND char_id=".$character['id']." AND a_id=".$MYACCOUNT['a_id'])->fetch() ? $isInterestedString : $isNotInterestedString;
  $gender = ($character['gender'] == 1) ? "Male" : "Female";
  if ($MYACCOUNT['mode'] == 0) {
    echo "
    <div class='call-part'>
      <div class='c1'>
        ".$character['name']."
        <div class='c2'>
          ".$character['min_age']."-".$character['max_age']." years<br>
          ".$gender."
        </div>
      </div>
      <div class='c3'>
        ".$character['description']."
      </div>
      <input type='button' value=\"".$interested."\" class='subscribe' data-interested='".$isNotInterestedString."' onclick='interested(this, \"".$CALL['d_id']."\", \"".$character['id']."\")'>
    </div>
    ";
  } else {
    echo "
    <div class='call-part'>
      <div class='c1'>
        ".$character['name']."
        <div class='c2'>
          ".$character['min_age']."-".$character['max_age']." years<br>
          ".$gender."
        </div>
      </div>
      <div class='c3'>
        ".$character['description']."
      </div>
    </div>
    ";
  }
}
?>

<script>
  function interested(sender, d_id, char_id) {
    post("/resources/ajax/functions.php", {'func': 'interested', 'd_id': d_id, 'char_id': char_id}, function(r) {
      r = JSON.parse(r)
      if (r['status'] == 'ok' && r['interested']) sender.value = "- REVOKE INTEREST"
      if (r['status'] == 'ok' && !r['interested']) sender.value = sender.getAttribute("data-interested")
      addAlert(r["message"])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

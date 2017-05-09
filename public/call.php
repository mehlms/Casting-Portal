<?php include "../inc/header.php";

$classes = $db->query("SELECT * FROM classes")->fetchAll();
$call = $db->query("SELECT calls.*, class FROM calls JOIN classes ON calls.type=classes.id WHERE calls.id=$data")->fetch();
$collaborators = $db->query("SELECT accounts.d_id, firstname, lastname FROM collaborators JOIN accounts ON collaborators.d_id=accounts.d_id WHERE call_id=$data ORDER BY collaborators.added")->fetchAll();
$auditions = $db->query("SELECT * FROM auditions WHERE call_id=$data")->fetchAll();
$shootings = $db->query("SELECT * FROM shootings WHERE call_id=$data")->fetchAll();
$characters = $db->query("SELECT id, name, min, max, gender, description, (SELECT COUNT(*) FROM interested WHERE char_id=characters.id AND a_id=$page_id) as interested, (SELECT COUNT(*) FROM characters as c2 WHERE ".$MYACCOUNT['mode']."=0 AND characters.id=c2.id AND ".$MYACCOUNT['age'].">=min AND ".$MYACCOUNT['age']."<=max AND (gender=3 OR gender=".$MYACCOUNT['gender'].")) as can_interested FROM characters WHERE call_id=$data ORDER BY id ASC")->fetchAll();
?>

<!-- POPUPS -->

<!-- <div id="popup_call" class='popup'>
  <div class="card scroll">
    <form onsubmit="postCall(this); return false">
      <input type="hidden" name="func" value="postCall">
      <h1>Edit Call</h1>
      <div class="label">
        <p>Title</p>
        <input type='text' name='title'  spellcheck='false' autocomplete='off' maxlength='100' placeholder="Guardians of the Galaxy"><br>
      </div>
      <div class="label">
        <p>Class</p>
        <select name="type">
        </select>
      </div>
      <div id="auditions">
        <div class='row'>
          <div class="label">
            <p>Audition Time</p>
          </div>
          <div class="label">
            <p>Audition Place</p>
          </div>
        </div>
      </div>
      <div id="shootings">
        <div class='row'>
          <div class="label">
            <p>Shooting From</p>
          </div>
          <div class="label">
            <p>To</p>
          </div>
        </div>
      </div>
      <div class="label">
        <p>Storyline</p>
        <textarea name='storyline' rows='3' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Brash space adventurer Peter Quill (Chris Pratt) finds himself the quarry of relentless bounty hunters after he steals an orb coveted by Ronan, a powerful villain."></textarea>
      </div>
      <h2>CHARACTERS</h2><hr>
      <div id="characters">
        <div class='row'>
          <div class="label" style="width:161px">
            <p>Name</p>
          </div>
          <div class="label" style="width:88px">
            <p>Min Age</p>
          </div>
          <div class="label" style="width:88px">
            <p>Max Age</p>
          </div>
          <div class="label" style="width:161px">
            <p>Gender</p>
          </div>
        </div>
      </div>
      <input type='submit' value='Update Casting Call'><input type='button' value='Upload Script'>
    </form>
  </div>
</div> -->

<!-- CARD -->

<div class='card'>
  <?php
  if (empty($call)) {
    echo "<h1>Oh No</h1>
          <p>That call does not exist.</p>";
  } else {
    echo "<input type='button' class='c_edit' value='edit' onclick=\"togglePopup(document.getElementById('popup_call'))\">
          <h1>".$call['title']." · Casting Call</h1>
          <div class='c_left'>
            <b>Collaborators</b><br>";
    foreach ($collaborators as $d) echo "<a href='/user/".$d['d_id']."/'>".$d['firstname']." ".$d['lastname']."</a><br>";
    echo "<b>Level</b><br>
          ".$call['class']."<br>
          <b>Audition Dates</b><br>";
    foreach ($auditions as $d) echo "<a href='https://www.google.com/calendar/render?action=TEMPLATE&text=".$call['title']." Audition&dates=20140127T224000Z/20140320T221500Z&details=".$call['storyline']."&location=".$d['audition_place']."&sf=true&output=xml' target='_blank' rel='nofollow'>".format($d['audition_time'], "g:ia D, F jS")." · ".$d['audition_place']."</a><br>";
    echo "<b>Shooting Dates</b><br>";
    foreach ($shootings as $d) echo "<a href='https://www.google.com/calendar/render?action=TEMPLATE&text=".$call['title']." Audition&dates=20140127T224000Z/20140320T221500Z&details=".$call['storyline']."&location=".$d['shooting_from']."&sf=true&output=xml' target='_blank' rel='nofollow'>".format($d['shooting_from'], "F jS")." - ".format($d['shooting_to'], "F jS, Y")."</a><br>";
    echo "<b>Storyline</b><br>
          ".$call['storyline']."<br>
          <b>Characters</b><br>";
    foreach ($characters as $d) {
        echo "<div class='c_prospect'>
                <b>".$d["name"]."</b> · Age ".$d["min"]."-".$d["max"]." · ".($d["gender"] == 1 ? "Male" : ($d["gender"] == 2 ? "Female" : "Any Gender"))."<br>
                ".$d["description"]."<br>
                <input type='button' class='".($d['interested'] ? 'interested' : '')."' value='interested' onclick='interested(this, ".$d['id'].")' ".($d['can_interested'] ? '' : 'disabled').">
              </div>";
    }
    echo "</div>
          <input type='button' value='download script'>";
  }
  ?>
</div>

<!-- SCRIPTS -->

<script>
  function postCall(form) {
    data = parse(form)
    data["auditions"] = parseArray('[data-audition]')
    data["shootings"] = parseArray('[data-shooting]')
    data["characters"] = parseArray('[data-character]')
    post("/resources/ajax/functions.php", data, function(r) {
      r = JSON.parse(r)
      if (r["status"] == "ok") {
        togglePopup(currentPopup)
        setTimeout(function() {
          window.location.href = "/"
        },300)
      } else addAlert(r['message'])
    })
  }

  function interested(sender, char_id) {
    post("/resources/ajax/functions.php", {"func": "interested", "char_id": char_id}, function(r) {
      console.log(r)
      r = JSON.parse(r)
      if (r['status'] == 'ok' && r['interested']) sender.className = "interested"
      else if (r['status'] == 'ok' && !r['interested']) sender.className = ""
      else addAlert(r['message'])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

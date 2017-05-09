<?php include "../inc/header.php";

if ($MYACCOUNT['mode']) echo "<script>window.location.href = '/'</script>";

$MYACCOUNT['age'] = date_diff(date_create($MYACCOUNT['birthdate']), date_create('now'))->y;
$auditions = $db->query("SELECT calls.*, class, (SELECT COUNT(*) FROM characters WHERE ".$MYACCOUNT['age'].">=min AND ".$MYACCOUNT['age']."<=max AND (gender=3 OR gender=".$MYACCOUNT['gender'].") AND call_id=calls.id) as char_count FROM calls JOIN classes ON calls.type=classes.id")->fetchAll();
?>

<!-- POPUPS -->

<div id="popup_call" class='popup'>
  <input type='button' class='c_edit' value="close" onclick="togglePopup(currentPopup)">
  <div class="card">
    <h1>Casting Call · <span data-title></span></h1>
    <div class="label">
      <p>Level</p>
      <div class='c_text' data-level></div>
    </div>
    <div class="label">
      <p>Collaborators</p>
      <div data-collaborators></div>
    </div>
    <div class='row'>
      <div class="label">
        <p>Audition Time</p>
      </div>
      <div class="label">
        <p>Place</p>
      </div>
    </div>
    <div data-auditions></div>
    <div class='row'>
      <div class="label">
        <p>Shooting Dates From</p>
      </div>
      <div class="label">
        <p>To</p>
      </div>
    </div>
    <div data-shootings></div>
    <div class="label">
      <p>Storyline</p>
      <div class='c_text' data-storyline></div>
    </div>
    <h2>CHARACTERS</h2>
    <hr>
    <div class='row'>
      <div class="label" style="width:170px">
        <p>Name</p>
      </div>
      <div class="label" style="width:75px">
        <p>Min Age</p>
      </div>
      <div class="label" style="width:75px">
        <p>Max Age</p>
      </div>
      <div class="label" style="width:170px">
        <p>Gender</p>
      </div>
    </div>
    <div data-characters></div>
    <input type='button' value='download script'><input type='button' value='print preview' onclick='window.print()'>
  </div>
</div>

<!-- PROFILE HEADER -->

<div class='card'>
  <h1>Casting Calls You Match</h1>
  <div class='row'>
    <div class="label" style='width:30%'>
      <p>Title</p>
    </div>
    <div class="label" style='width:40%'>
      <p>Type</p>
    </div>
    <div class="label" style='width:20%'>
      <p>Character Matches</p>
    </div>
  </div>
  <div data-auditions></div>
  <?php
  if (empty($auditions)) {
    echo "<p>There are no upcoming auditions for you.</p>";
  } else {
    foreach ($auditions as $a) {
      echo "
      <div class='row'>
        <div class='label' style='width:30%'>
          <div class='c_text'><a onclick=\"getCall('".$a['id']."')\">".$a['title']."</a></div>
        </div>
        <div class='label' style='width:40%'>
          <div class='c_text'>".$a['class']."</div>
        </div>
        <div class='label' style='width:20%'>
          <div class='c_text'>".$a['char_count']."</div>
        </div>
      </div>
      ";
    }
  }
  ?>
</div>

<!-- SCRIPTS -->

<script>
function getCall(id) {
  post("/resources/ajax/functions.php", {"func": "getCall", "id": id}, function(r) {
    r = JSON.parse(r)
    if (r["status"] == "ok") {
      var popup = document.getElementById("popup_call")
      popup.querySelector("[data-title]").innerHTML = r['call']['title']
      popup.querySelector("[data-collaborators]").innerHTML = ""
      r['collaborators'].forEach(function(d) {
        popup.querySelector("[data-collaborators]").innerHTML += "<div class='c_text'><a href='/user/"+d['d_id']+"/'>"+d['firstname']+" "+d['lastname']+"</a></div>"
      })
      popup.querySelector("[data-level]").innerHTML = r['call']['class']
      popup.querySelector("[data-auditions]").innerHTML = ""
      r['auditions'].forEach(function(d) {
        popup.querySelector("[data-auditions]").innerHTML += "<div class='row'> \
                                                                <div class='label'> \
                                                                  <div class='c_text'>"+d['audition_time']+"</div> \
                                                                </div> \
                                                                <div class='label'> \
                                                                  <div class='c_text'>"+d['audition_place']+"</div> \
                                                                </div> \
                                                              </div>"
      })
      popup.querySelector("[data-shootings]").innerHTML = ""
      r['shootings'].forEach(function(d) {
        popup.querySelector("[data-shootings]").innerHTML += "<div class='row'> \
                                                                <div class='label'> \
                                                                  <div class='c_text'>"+d['shooting_from']+"</div> \
                                                                </div> \
                                                                <div class='label'> \
                                                                  <div class='c_text'>"+d['shooting_to']+"</div> \
                                                                </div> \
                                                              </div>"
      })
      popup.querySelector("[data-storyline]").innerHTML = r['call']['storyline']
      popup.querySelector("[data-characters]").innerHTML = ""
      r['characters'].forEach(function(d) {
        popup.querySelector("[data-characters]").innerHTML += "<div class='row'> \
                                                                <div class='label' style='width:170px'> \
                                                                  <div class='c_text'><b>"+d['name']+"</b></div> \
                                                                </div> \
                                                                <div class='label' style='width:75px'> \
                                                                  <div class='c_text'>"+d['min']+"</div> \
                                                                </div> \
                                                                <div class='label' style='width:75px'> \
                                                                  <div class='c_text'>"+d['max']+"</div> \
                                                                </div> \
                                                                <div class='label' style='width:170px; position: relative'> \
                                                                  <input type='button' style='top: -11px' class='c_edit "+(d['interested'] ? 'interested' : '')+"' value='interested' onclick='interested(this, "+d['id']+")' "+(d['can_interested'] ? '' : 'disabled')+"> \
                                                                  <div class='c_text'>"+(d['gender']==1?"Male":d['gender']==2?"Female":"Any Gender")+"</div> \
                                                                </div> \
                                                              </div> \
                                                              <div class='c_text'><p style='width: 430px'>"+d['description']+"</p></div>"
      })
      togglePopup(popup)
    } else addAlert(r['message'])
  })
}

function interested(sender, char_id) {
  post("/resources/ajax/functions.php", {"func": "interested", "char_id": char_id}, function(r) {
    r = JSON.parse(r)
    if (r['status'] == 'ok' && r['interested']) sender.className = "c_edit interested"
    else if (r['status'] == 'ok' && !r['interested']) sender.className = "c_edit"
    else addAlert(r['message'])
  })
}
</script>

<?php include "../inc/footer.php" ?>

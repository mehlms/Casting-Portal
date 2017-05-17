<?php include "../inc/header.php";

if ($MYACCOUNT['mode']) echo "<script>window.location.href = '/'</script>";

$asset_profile = "/resources/images/placeholder.png";
$asset = $db->query("SELECT url FROM assets WHERE page_id=$page_id AND type=1")->fetch();
if ($asset) $asset_profile = "/resources/assets/profile/".$asset['url'];

$calls = $db->query("SELECT calls.id, calls.title, class, (SELECT genre FROM genres WHERE calls.genre=genres.id) as g1, (SELECT genre FROM genres WHERE calls.genre2=genres.id) as g2
                    FROM calls
                    JOIN (SELECT call_id, COUNT(*) as char_count FROM characters WHERE min<=".$MYACCOUNT['looks_max']." AND max>=".$MYACCOUNT['looks_min']." AND (gender=3 OR gender=".$MYACCOUNT['gender'].") GROUP BY call_id) as t2
                    ON t2.call_id=calls.id
                    JOIN classes
                    ON calls.type=classes.id
                    WHERE calls.type NOT IN (SELECT class_id FROM classesFilter WHERE a_id=".$MYACCOUNT['a_id'].") AND
                    calls.genre NOT IN (SELECT genre_id FROM genresFilter WHERE a_id=".$MYACCOUNT['a_id'].") AND
                    calls.genre2 NOT IN (SELECT genre_id FROM genresFilter WHERE a_id=".$MYACCOUNT['a_id'].")
                    ORDER BY calls.added DESC")->fetchAll();
?>

<!-- POPUPS -->

<div id="popup_match" class='popup'>
  <div class='card'>
    <form onsubmit='updateMatch(this); return false'>
      <input type="hidden" name="func" value="updateMatch">
      <h1>Match Settings</h1>
      <div class='row'>
        <div class="label">
          <p>I Look Between (Min Age)</p>
          <input type='text' name='looks_min' value="<?php echo $MYACCOUNT['looks_min'] ?>" spellcheck='false' autocomplete='off' maxlength='2'>
        </div>
        <div class="label">
          <p>(Max Age)</p>
          <input type='text' name='looks_max' value="<?php echo $MYACCOUNT['looks_max'] ?>" spellcheck='false' autocomplete='off' maxlength='2'>
        </div>
      </div>
      <input type='submit' value='Update'>
    </form>
  </div>
</div>
<div id="popup_filter" class='popup'>
  <div class='card'>
    <form onsubmit='updateFilter(this); return false'>
      <input type="hidden" name="func" value="updateFilter">
      <h1>Filter</h1>
      <div class="label" style='text-align:center'>
        <p>Include These Genres</p>
        <?php
        $genres = $db->query("SELECT * FROM genres LEFT JOIN (SELECT genre_id, COUNT(*) as checked FROM genresFilter WHERE a_id=".$MYACCOUNT['a_id']." GROUP BY genre_id) as t2 ON genres.id=t2.genre_id ORDER BY genres.id ASC")->fetchAll();
        foreach ($genres as $d) echo "<input type='checkbox' id='genresFilter".$d['id']."' name='genresFilter[".$d['id']."]' ".($d['checked']?"":"checked")."><label for='genresFilter".$d['id']."'><input type='button' value='".$d['genre']."'></label> ";
        ?>
      </div>
      <div class="label" style='text-align:center'>
        <p>Include These Classes</p>
        <?php
        $classes = $db->query("SELECT * FROM classes LEFT JOIN (SELECT class_id, COUNT(*) as checked FROM classesFilter WHERE a_id=".$MYACCOUNT['a_id']." GROUP BY class_id) as t2 ON classes.id=t2.class_id ORDER BY classes.id ASC")->fetchAll();
        foreach ($classes as $d) echo "<input type='checkbox' id='classesFilter".$d['id']."' name='classesFilter[".$d['id']."]' ".($d['checked']?"":"checked")."><label for='classesFilter".$d['id']."'><input type='button' value='".str_replace("Graduate ", "", str_replace("Undergraduate ", "", $d['class']))."'></label> ";
        ?>
      </div>
      <input type='submit' value='Save'>
    </form>
  </div>
</div>

<!-- PROFILE -->

<div id='profile'>
  <input type='file' id="profile_pic_file" onchange="uploadProfilePic(this)" style="display:none" accept="image/x-png,image/jpeg">
  <div class='c_pic' onclick="document.getElementById('profile_pic_file').click()" style="background-image: url('<?php echo $asset_profile; ?>')"></div>
  <div class='card'>
    <h1 id="name"><?php echo $MYACCOUNT['firstname']." ".$MYACCOUNT['lastname'] ?></h1>
    <p><b><?php echo ($MYACCOUNT["gender"] == 1) ? "Male" : "Female"; ?> · Looks Between <?php echo $MYACCOUNT['looks_min']."-".$MYACCOUNT['looks_max'] ?></b></p>
  </div>
  <div class='c_buttons'>
    <input type='button' value='Match Settings' onclick="togglePopup(document.getElementById('popup_match'))"><br>
  </div>
</div>

<!-- RESULTS -->

<div class='card'>
  <h1>Your Matches</h1>
  <input type='button' value='Filter' onclick="togglePopup(document.getElementById('popup_filter'))" class='c_edit'>
  <div class='row'>
    <div class="label" style='width:30%'>
      <p>Title</p>
    </div>
    <div class="label" style='width:40%'>
      <p>Type</p>
    </div>
    <div class="label" style='width:25%'>
      <p>Genre</p>
    </div>
  </div>
  <?php
  if (empty($calls)) {
    echo "<p>There are no upcoming auditions for you.</p>";
  } else {
    foreach ($calls as $d) {
      echo "
      <div class='row'>
        <a onclick=\"getCall('".$d['id']."')\">
          <div class='label' style='width:30%'>
            ".$d['title']."
          </div>
          <div class='label' style='width:40%'>
            ".$d['class']."
          </div>
          <div class='label' style='width:25%'>
            ".($d['g1'].($d['g2']?", ".$d['g2']: ""))."
          </div>
        </a>
      </div>
      ";
    }
  }
  ?>
</div>

<script>
  function updateMatch(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      r = JSON.parse(r)
      if (r["status"] == "ok") {
        togglePopup(currentPopup)
        setTimeout(function() {
          window.location.href = window.location.href
        }, 300)
      } else addAlert(r['message'])
    })
  }

  function updateFilter(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      r = JSON.parse(r)
      if (r["status"] == "ok") {
        togglePopup(currentPopup)
        setTimeout(function() {
          window.location.href = window.location.href
        },300)
      } else addAlert(r['message'])
    })
  }

  function uploadProfilePic(input) {
    if (input.files[0].size <= 999999) {
      post("/resources/ajax/functions.php", {"func": "uploadProfilePic", "image": input.files[0]}, function(r) {
        r = JSON.parse(r)
        if (r['status'] == 'ok') window.location.href = window.location.href
        addAlert(r['message'])
      })
    } else addAlert("That file is larger than 500kb")
  }
</script>

<?php include "../inc/footer.php" ?>

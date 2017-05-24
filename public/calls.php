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
    <form onsubmit="sendFormPopup('update_match', this); return false">
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
    <form onsubmit="sendFormPopup('update_filter', this); return false">
      <h1>Filter</h1>
      <div class="label" style='text-align:center; white-space: normal'>
        <p>Include These Genres</p>
        <?php
        $genres = $db->query("SELECT * FROM genres LEFT JOIN (SELECT genre_id, COUNT(*) as checked FROM genresFilter WHERE a_id=".$MYACCOUNT['a_id']." GROUP BY genre_id) as t2 ON genres.id=t2.genre_id ORDER BY genres.id ASC")->fetchAll();
        foreach ($genres as $d) echo "<label><input type='checkbox' name='genresFilter[".$d['id']."]' ".($d['checked']?"":"checked")."><div class='checkbox'>".$d['genre']."</div></label> ";
        ?>
      </div><br>
      <div class="label" style='text-align:center; white-space: normal'>
        <p>Include These Classes</p>
        <?php
        $classes = $db->query("SELECT * FROM classes LEFT JOIN (SELECT class_id, COUNT(*) as checked FROM classesFilter WHERE a_id=".$MYACCOUNT['a_id']." GROUP BY class_id) as t2 ON classes.id=t2.class_id ORDER BY classes.id ASC")->fetchAll();
        foreach ($classes as $d) echo "<label><input type='checkbox' name='classesFilter[".$d['id']."]' ".($d['checked']?"":"checked")."><div class='checkbox'>".str_replace("Graduate ", "", str_replace("Undergraduate ", "", $d['class']))."</div></label> ";
        ?>
      </div>
      <input type='submit' value='Save'>
    </form>
  </div>
</div>

<!-- PROFILE -->

<div id='profile'>
  <form><input type='file' name='pic' id="profile_pic_file" onchange="(this.files[0].size > 999999) ? addAlert('File is larger than 1mb') : sendForm('upload_profile_pic', this.parentElement)" style="display:none" accept="image/x-png,image/jpeg"></form>
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

<?php include "../inc/footer.php" ?>

<?php include "../inc/header.php";

$page_id = $MYACCOUNT['mode'] ? $MYACCOUNT['d_id'] : $MYACCOUNT['a_id'];

$bio = $MYACCOUNT['mode'] ? $MYACCOUNT['d_bio'] : $MYACCOUNT['a_bio'];

$asset_profile = "/resources/images/placeholder.png";
$asset_videos = array();
$asset_photos = array();
$assets = $db->query("SELECT id, url, title, type FROM assets WHERE page_id='$page_id' ORDER BY added DESC");
foreach ($assets->fetchAll() as $asset) {
  if ($asset['type'] == 1) $asset_profile = "/resources/assets/profile/".$asset['url'];
  else if ($asset['type'] == 2) array_push($asset_photos, $asset);
  else if ($asset['type'] == 3 || $asset['type'] == 4) array_push($asset_videos, $asset);
}
?>

<!-- POPUPS -->

<div id="popup_basic" class='popup'>
  <div class='card'>
    <form class='popup_form' onsubmit='updateInfo(this); return false'>
      <input type="hidden" name="func" value="updateInfo">
      <h1>Update Your Information</h1>
      <div class="label">
        <p>Firstname</p>
        <input type='text' name='firstname' value="<?php echo $MYACCOUNT['firstname'] ?>" spellcheck='false' autocomplete='off' maxlength='20'>
      </div>
      <div class="label">
        <p>Lastname</p>
        <input type='text' name='lastname' value="<?php echo $MYACCOUNT['lastname'] ?>" spellcheck='false' autocomplete='off' maxlength='20'>
      </div>
      <div class="label">
        <p>Date of Birth</p>
        <input type='text' name='birthdate' spellcheck='false' autocomplete='off' maxlength='19' placeholder='mm/dd/YYYY' value="<?php $birthdate = new DateTime($MYACCOUNT['birthdate']); echo $birthdate->format('m/d/Y'); ?>" onkeyup="checkDate(event, this)">
      </div>
      <div class="label">
        <p>Bio</p>
        <textarea id="input_bio" rows='3' spellcheck='false' autocomplete='off' maxlength='1000' name="bio" placeholder="Profile pictures speak 1,000 words but your's speaks 'cutey pie' so why not fill in the rest."><?php echo $bio ?></textarea>
      </div>
      <input type='submit' value='Update Information'>
    </form>
  </div>
</div>
<div id="popup_notifications" class='popup'>
  <div class='card'>
    <form>
      <h1>Edit Notification Settings</h1>
      <div class="label">
        <p>Actor Follows You</p>
        <select name="gender">
          <option value="1">In Site</option>
          <option value="2">Email</option>
          <option value="3">In Site + Email</option>
        </select>
      </div>
      <div class="label">
        <p>Actor is Interested</p>
        <select name="gender">
          <option value="1">In Site</option>
          <option value="2">Email</option>
          <option value="3">In Site + Email</option>
        </select>
      </div>
      <div class="label">
        <p>Actor Writes You Recommendation</p>
        <select name="gender">
          <option value="1">In Site</option>
          <option value="2">Email</option>
          <option value="3" selected>In Site + Email</option>
        </select>
      </div>
      <br>
      <input type='submit' value='Update Settings'>
    </form>
  </div>
</div>
<div id="popup_call" class='popup' data-fixed=true>
  <div class="card scroll">
    <form>
      <h1>Create Call</h1>
      <div class="label">
        <p>Title</p>
        <input type='text' name='title' spellcheck='false' autocomplete='off' maxlength='100' placeholder="Guardians of the Galaxy"><br>
      </div>
      <div class="label">
        <p>Type</p>
        <select name="type">
          <option value="1">Undergraduate Visual Storytelling (FTV 130)</option>
          <option value="2">Undergraduate Directing 2 (FP 338)</option>
          <option value="3">Undergraduate Directing 3 (FP 438)</option>
          <option value="4">Undergraduate Intermediate Production (FP 280)</option>
          <option value="5">Undergraduate Advanced Production (FP 331)</option>
          <option value="6">Undergraduate Senior Thesis (FP 497-498)</option>
          <option value="7">Undergraduate Byte-sized Television (TWP 313)</option>
          <option value="8">Undergraduate Television Pilots (TWP 398)</option>
          <option value="9">Undergraduate Digital Arts Project</option>
          <option value="10">Undergraduate Independent Study</option>
          <option value="11">Graduate Fundamentals of Directing 1 (FP 538)</option>
          <option value="12">Graduate Fundamentals of Directing 2 (FP 539)</option>
          <option value="13">Graduate Intermediate Directing (FP 664)</option>
          <option value="14">Graduate Advanced Directing (FP 665)</option>
          <option value="15">Graduate Master Class in Directing (FP 638)</option>
          <option value="16">Graduate Production Workshop 1 (FP 531)</option>
          <option value="17">Graduate Production Workshop 2 (FP 532)</option>
          <option value="18">Graduate Production Workshop 3 (FP 577)</option>
          <option value="19">Graduate Production Workshop 4 (FP 631)</option>
          <option value="20">Graduate Thesis (FP 698)</option>
          <option value="21">Graduate Filmmakers and Actors Workshop (FP 507)</option>
          <option value="22">Graduate Independent Study</option>
          <option value="23">Other</option>
        </select>
      </div>
      <div id="auditions">
        <div class='row'>
          <div class="label">
            <p>Audition Time</p>
            <input type='text' name='audition_time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='mm/dd/YYYY hh:mm am/pm' onkeyup="checkDateTime(event, this)">
          </div>
          <div class="label">
            <p>Place</p>
            <div class='c_add' onclick='addAudition()'>+</div>
            <input type='text' name='audition_time' spellcheck='false' autocomplete='off' maxlength='19' placeholder="Dodge Rm 100">
          </div>
        </div>
      </div>
      <div id="shootings">
        <div class='row'>
          <div class="label">
            <p>Shooting Dates From</p>
            <input type='text' name='audition_time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='mm/dd/YYYY' onkeyup="checkDate(event, this)">
          </div>
          <div class="label">
            <p>To</p>
            <div class='c_add' onclick='addShooting()'>+</div>
            <input type='text' name='audition_time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='mm/dd/YYYY' onkeyup="checkDate(event, this)">
          </div>
        </div>
      </div>
      <div class="label">
        <p>Storyline</p>
        <textarea rows='3' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Brash space adventurer Peter Quill (Chris Pratt) finds himself the quarry of relentless bounty hunters after he steals an orb coveted by Ronan, a powerful villain."></textarea>
      </div>
      <h2>CHARACTERS</h2><hr>
      <div id="characters">
        <div>
          <div class='row'>
            <div class="label" style="width:161px">
              <p>Name</p>
              <input type='text' name='audition_time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='Peter Quill'>
            </div>
            <div class="label" style="width:88px">
              <p>Min Age</p>
              <input type='text' name='audition_time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='24' >
            </div>
            <div class="label" style="width:88px">
              <p>Max Age</p>
              <input type='text' name='audition_time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='35'>
            </div>
            <div class="label" style="width:161px">
              <p>Gender</p>
              <div class='c_add' onclick='addCharacter()'>+</div>
              <select name="gender">
                <option value="1">Either</option>
                <option value="2">Male</option>
                <option value="3">Female</option>
              </select>
            </div>
          </div>
          <textarea rows='2' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Any more additional information pertaining to this actor or character?"></textarea>
        </div>
      </div>
      <input type='submit' value='Create Casting Call'><input type='button' value='Upload Script'>
    </form>
  </div>
</div>
<div id="popup_video" class='popup'>
  <div class='card'>
    <form class='popup_form' onsubmit='addVideo(this); return false'>
      <input type="hidden" name="func" value="addVideo">
      <h1>Add a Video</h1>
      <div class="label">
        <p>Title</p>
        <input type='text' name='title' spellcheck='false' autocomplete='off' maxlength='40' placeholder="My Demo Reel">
      </div>
      <div class='row'>
        <div class="label">
          <p>Youtube Link ...</p>
          <input type='text' name='youtubeLink' spellcheck='false' autocomplete='off' maxlength='40' placeholder="https://www.youtube.com/watch?v=5mF0le5Y96M">
        </div>
        <div class="label">
          <p>or Vimeo Link</p>
          <input type='text' name='vimeoLink' spellcheck='false' autocomplete='off' maxlength='40' placeholder="https://vimeo.com/67790369">
        </div>
      </div>
      <input type='submit' value='Add Video'>
    </form>
  </div>
</div>
<div id="popup_photo" class='popup popup_photo'></div>

<!-- PROFILE HEADER -->

<div id='profile'>
  <input type='file' id="profile_pic_file" onchange="uploadProfilePic(this)" style="display:none" accept="image/x-png,image/jpeg">
  <div class='c_pic' onclick="document.getElementById('profile_pic_file').click()" style="background-image: url('<?php echo $asset_profile; ?>')"></div>
  <div class='card'>
    <h1 id="name"><?php echo $MYACCOUNT['firstname']." ".$MYACCOUNT['lastname'] ?></h1>
    <p><b><?php echo $MYACCOUNT['mode'] ? "Director" : "Talent" ?> · <?php echo ($MYACCOUNT["gender"] == 1) ? "Male" : "Female"; ?> · Age <?php echo date_diff(date_create($MYACCOUNT['birthdate']), date_create('now'))->y ?></b></p>
  </div>
  <div class='c_buttons'>
    <input type='button' value='Toggle to <?php echo $MYACCOUNT['mode'] ? "Talent" : "Director" ?> Mode' onclick="window.location.href='/toggle/'"><br>
    <input type='button' value='Update Basic Info' onclick="togglePopup(document.getElementById('popup_basic'))"><br>
    <input type='button' value='Edit Notifications' onclick="togglePopup(document.getElementById('popup_notifications'))">
  </div>
</div>

<!-- PROFILE CARDS -->

<div class='card'>
  <input type='button' class='c_edit c_add' value="+" onclick="togglePopup(document.getElementById('popup_call'))">
  <h1>Calls</h1>
  <p>You have no casting calls. <a onclick="togglePopup(document.getElementById('popup_call'))">Create Call?</a></p>
</div>
<div class='card_column_left'>
  <div class='card'>
    <input type='button' class='c_edit' value="edit" onclick="togglePopup(document.getElementById('popup_basic'))">
    <h1>Bio</h1>
    <p><?php echo $bio != "" ? $bio : "You have no bio. <a onclick=\"togglePopup(document.getElementById('popup_basic'))\">Add a Bio?</a>" ?></p>
  </div>
  <div class='card'>
    <h1>Praise</h1>
    <p>You have no praise.</p>
  </div>
  <div class='card'>
    <h1>Followers</h1>
    <p>You have no followers.</p>
  </div>
</div>
<div class='card_column_right'>
  <div class='card'>
    <input type='button' class='c_edit c_add' value="+" onclick="togglePopup(document.getElementById('popup_video'))">
    <h1>Videos</h1>
    <?php
    if (empty($asset_videos)) {
      echo "<p>You have no videos. <a onclick=\"togglePopup(document.getElementById('popup_video'))\">Add a Video?</a></p>";
    } else {
      echo "<div class='c_assets'>";
      foreach ($asset_videos as $asset) {
        if ($asset['type'] == 3) {
          echo "
            <div class='label'>
              <input type='button' class='c_edit c_delete card_delete_video' value='-' onclick=\"deleteAsset(".$asset['id'].")\">
              <p>".$asset['title']."</p>
              <iframe src='https://www.youtube.com/embed/".$asset['url']."' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            </div>";
        } else if ($asset['type'] == 4) {
          echo "
            <div class='label'>
              <input type='button' class='c_edit c_delete c_delete_video' value='-' onclick=\"deleteAsset(".$asset['id'].")\">
              <p>".$asset['title']."</p>
              <iframe src='https://player.vimeo.com/video/".$asset['url']."' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            </div>";
        }
      }
      echo "</div>";
    }
    ?>
  </div>
  <div class='card'>
    <input type='file' id="upload_pic" onchange="uploadPic(this)" style="display:none" accept="image/x-png,image/jpeg">
    <input type='button' class='c_edit c_add' value="+" onclick="document.getElementById('upload_pic').click()">
    <h1>Photos</h1>
    <?php
    if (empty($asset_photos)) {
      echo "<p>You have no photos. <a onclick=\"document.getElementById('upload_pic').click()\">Add a Photo?</a></p>";
    } else {
      echo "<div class='c_assets'>";
      foreach ($asset_photos as $asset) {
        $url = "/resources/assets/photos/".$asset['url'];
        $url_large = "/resources/assets/photos_large/".$asset['url'];
        echo "<div class='c_photo' style=\"background-image: url('".$url."')\" onclick=\"document.getElementById('popup_photo').style.backgroundImage='url(".$url_large.")'; togglePopup(document.getElementById('popup_photo'))\">
              <input type='button' class='c_edit c_delete' value='-' onclick=\"deleteAsset(".$asset['id'].")\"></div>";
      }
      echo "</div>";
    }
    ?>
  </div>
</div>

<!-- SCRIPTS -->

<script>
  function uploadProfilePic(input) {
    if (input.files[0].size <= 500000) {
      post("/resources/ajax/functions.php", {"func": "uploadProfilePic", "image": input.files[0]}, function(r) {
        r = JSON.parse(r)
        if (r['status'] == 'ok') window.location.href = "/"
        addAlert(r['message'])
      })
    } else addAlert("That file is larger than 500kb")
  }

  function uploadPic(input) {
    if (input.files[0].size <= 500000) {
      post("/resources/ajax/functions.php", {"func": "uploadPic", "image": input.files[0]}, function(r) {
        console.log(r)
        r = JSON.parse(r)
        if (r['status'] == 'ok') window.location.href = "/"
        addAlert(r['message'])
      })
    } else addAlert("That file is larger than 500kb")
  }

  function updateInfo(form) {
    form = parse(form)
    post("/resources/ajax/functions.php", form, function(r) {
      console.log(r)

      r = JSON.parse(r)
      if (r["status"] == "ok") {
        togglePopup(currentPopup)
        setTimeout(function() {
          window.location.href = "/"
        },300)
      } else addAlert(r['message'])
    })
  }

  function addVideo(form) {
    form = parse(form)
    post("/resources/ajax/functions.php", form, function(r) {
      console.log(r)
      r = JSON.parse(r)
      if (r["status"] == "ok") {
        togglePopup(currentPopup)
        setTimeout(function() {
          window.location.href = "/"
        },300)
      } else addAlert(r['message'])
    })
  }

  function deleteAsset(id) {
    post("/resources/ajax/functions.php", {"func": "deleteAsset", "id": id}, function(r) {
      console.log(r)
      r = JSON.parse(r)
      if (r["status"] == "ok") {
        window.location.href = "/"
      } else addAlert(r['message'])
    })
  }

  function addAudition() {
    document.getElementById("auditions").innerHTML +=
    "<div class='row'> \
      <div class='label'> \
        <input type='text' name='audition_time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='mm/dd/YYYY hh:mm am/pm' onkeyup='checkDateTime(event, this)'> \
      </div> \
      <div class='label'> \
        <div class='c_add' onclick=\"document.getElementById('auditions').removeChild(this.parentElement.parentElement)\">-</div> \
        <input type='text' name='audition_time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='Dodge Rm 100'> \
      </div> \
    </div>"
  }

  function addShooting() {
    document.getElementById("shootings").innerHTML +=
    "<div class='row'> \
      <div class='label'> \
        <input type='text' spellcheck='false' autocomplete='off' maxlength='10' placeholder='mm/dd/YYYY' onkeyup='checkDate(event, this)'> \
      </div> \
      <div class='label'> \
        <div class='c_add' onclick=\"document.getElementById('shootings').removeChild(this.parentElement.parentElement)\">-</div> \
        <input type='text' spellcheck='false' autocomplete='off' maxlength='10' placeholder='mm/dd/YYYY' onkeyup='checkDate(event, this)'> \
      </div> \
    </div>"
  }

  function addCharacter() {
    document.getElementById("characters").innerHTML +=
    "<div> \
      <div class='row'> \
        <div class='label' style='width:161px'> \
          <input type='text' spellcheck='false' autocomplete='off' maxlength='19' placeholder='Peter Quill'> \
        </div> \
        <div class='label' style='width:88px'> \
          <input type='text' spellcheck='false' autocomplete='off' maxlength='19' placeholder='24'> \
        </div> \
        <div class='label' style='width:88px'> \
          <input type='text' spellcheck='false' autocomplete='off' maxlength='19' placeholder='35'> \
        </div> \
        <div class='label' style='width:161px'> \
          <div class='c_add' onclick=\"document.getElementById('characters').removeChild(this.parentElement.parentElement.parentElement)\">-</div> \
          <select> \
            <option value='1'>Either</option> \
            <option value='2'>Male</option> \
            <option value='3'>Female</option> \
          </select> \
        </div> \
      </div> \
      <textarea rows='2' spellcheck='false' autocomplete='off' maxlength='1000' placeholder='Any more additional information pertaining to this actor or character?'></textarea> \
    </div>"
  }
</script>

<?php include "../inc/footer.php" ?>

<?php include "../inc/header.php";

$asset_profile = "/resources/images/placeholder.png";
$asset_videos = array();
$asset_photos = array();
$assets = $db->query("SELECT id, url, title, type FROM assets WHERE page_id=$page_id ORDER BY added DESC")->fetchAll();
foreach ($assets as $asset) {
  if ($asset['type'] == 1) $asset_profile = "/resources/assets/profile/".$asset['url'];
  else if ($asset['type'] == 2) array_push($asset_photos, $asset);
  else if ($asset['type'] == 3 || $asset['type'] == 4) array_push($asset_videos, $asset);
}

function getPhotos() {
  global $asset_photos;
  if (empty($asset_photos)) {
    echo "<p>You have no photos.</p>";
  } else {
    echo "<div class='c_assets'>";
    foreach ($asset_photos as $d) {
      echo "<div class='c_photo' style=\"background-image: url(/resources/assets/photos/".$d['url'].")\" onclick=\"document.getElementById('popup_photo').style.backgroundImage='url(/resources/assets/photos_large/".$d['url'].")'; togglePopup(document.getElementById('popup_photo'))\">
            <input type='button' class='c_edit c_delete' value='-' onclick=\"deleteAsset(".$d['id'].")\"></div>";
    }
    echo "</div>";
  }
}

function getVideos() {
  global $asset_videos;
  if (empty($asset_videos)) {
    echo "<p>You have no videos.</p>";
  } else {
    echo "<div class='c_assets'>";
    foreach ($asset_videos as $d) {
      if ($d['type'] == 3) {
        echo "<div class='label'>
                <input type='button' class='c_edit c_delete c_delete_video' value='-' onclick=\"deleteAsset(".$d['id'].")\">
                <p>".$d['title']."</p>
                <iframe src='https://www.youtube.com/embed/".$d['url']."' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
              </div>";
      } else if ($d['type'] == 4) {
        echo "<div class='label'>
                <input type='button' class='c_edit c_delete c_delete_video' value='-' onclick=\"deleteAsset(".$asset['id'].")\">
                <p>".$d['title']."</p>
                <iframe src='https://player.vimeo.com/video/".$d['url']."' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
              </div>";
      }
    }
    echo "</div>";
  }
}

function getDirectorCard() {
  global $db;
  global $page_id;
  $calls = $db->query("SELECT calls.id, title, type, storyline, calls.added FROM calls JOIN collaborators ON calls.id=collaborators.call_id WHERE d_id='$page_id'")->fetchAll();
  echo "
  <input type='button' class='c_edit' value='Create Call' onclick=\"togglePopup(document.getElementById('popup_post'))\">
  <h1>Your Calls</h1>";
  if (count($calls) == 0) {
    echo "You have no calls.";
  } else {
    foreach ($calls as $call) {
      $characters = $db->query("SELECT * FROM characters LEFT JOIN (SELECT char_id, COUNT(*) as cnt FROM interested GROUP BY char_id) t2 ON char_id=id WHERE call_id=".$call['id']." ORDER BY cnt DESC")->fetchAll();
      $interested = $db->query("SELECT char_id, a_id, url, type FROM interested JOIN characters ON interested.char_id=characters.id JOIN assets ON interested.a_id=assets.page_id WHERE assets.type=1")->fetchAll();
      $auditions = $db->query("SELECT * FROM auditions WHERE call_id=".$call['id'])->fetchAll();
      echo "
      <div class='c_item' style='position: relative'>
        <input type='button' class='c_edit' value='Email' onclick=\"document.getElementById('email_call_id').value='".$call['id']."';togglePopup(document.getElementById('popup_email'))\">
        <input type='button' class='c_edit' value='Edit' onclick=\"getEditCall(".$call['id'].")\" style='right: 61px'>
          <b><a onclick=\"getCall('".$call['id']."')\">".$call["title"]."</a></b><br>";
      foreach ($auditions as $d) echo "<a href='https://www.google.com/calendar/render?action=TEMPLATE&text=".$call['title']." Audition&dates=20140127T224000Z/20140320T221500Z&details=Audition Time&location=".$d['audition_place']."&sf=true&output=xml' target='_blank' rel='nofollow'>".format($d['audition_time'], "g:ia D, M jS")."</a><br>";
      foreach ($characters as $c) {
        echo "<div class='c_text' style='text-align:center'>
        <a><b>".$c["name"]."</b></a> · ".($c["cnt"] ? $c["cnt"]:"No one")." interested<br>";
        foreach ($interested as $i) {
          if ($i['char_id'] == $c['id']) {
            echo "<div class='c_photo' onclick=\"window.location.href='/user/".$i['a_id']."/'\" style='background-image: url(/resources/assets/profile/".$i['url'].")'></div>";
          }
        }
        echo "</div>";
      }
      echo "</div>";
    }
  }
}

function getTalentCard() {
  global $db;
  global $MYACCOUNT;
  $characters = $db->query("SELECT characters.*, calls.title, calls.type, class, (SELECT url FROM assets WHERE page_id=calls.id AND type=6) as script FROM interested JOIN characters ON interested.char_id=characters.id JOIN calls ON characters.call_id=calls.id JOIN classes ON classes.id=calls.type WHERE interested.a_id=".$MYACCOUNT['a_id']." ORDER BY interested.added DESC")->fetchAll();
  echo "
  <input type='button' class='c_edit' value='Find Auditions' onclick=\"window.location.href='/calls/'\">
  <h1>Your Auditions</h1>";
  if (empty($characters)) {
    echo "<p>You have no upcoming auditions.</p>";
  } else {
    foreach ($characters as $c) {
      echo "<div class='c_text'>
              <b>".$c["name"]." · <a onclick=\"getCall('".$c['call_id']."')\">".$c['title']."</a></b><br>
              ".$c['class']."<br>";
      $auditions = $db->query("SELECT * FROM auditions WHERE call_id=".$c['call_id']."")->fetchAll();
      foreach ($auditions as $d) echo "<a href='https://www.google.com/calendar/render?action=TEMPLATE&text=".$c['title']." Audition&dates=20140127T224000Z/20140320T221500Z&details=Auditioning for ".$c['name'].": ".$c['description']."&location=".$d['audition_place']."&sf=true&output=xml' target='_blank' rel='nofollow'>".format($d['audition_time'], "g:ia D, M jS")."</a><br>";
      echo "</div>
            <div class='c_text'>
              <input type='button' class='interested' value='Interested' onclick='interestedConfirm(this, ".$c['id'].")' style='float:none'>
              <input type='button' value='Download Script' style='float:none' onclick=\"window.open('/resources/assets/scripts/".$c['script']."', '_blank')\" ".($c['script'] ? "":"disabled").">
            </div>";
    }
  }
}

function getPraise() {
  global $db;
  global $page_id;
  global $MYACCOUNT;
  $praise = $db->query("SELECT praise.*, accounts.firstname, accounts.lastname FROM praise, accounts WHERE praise_to=$page_id AND (praise_from=a_id OR praise_from=d_id) ORDER BY added DESC")->fetchAll();
  if (count($praise) == 0) echo "<p>".($MYACCOUNT['mode'] ? "Actors" : "Directors")." can recommend you.</p>";
  else {
    foreach ($praise as $d) {
      if ($d['heart']) echo "<p><a href='/user/".$d['praise_from']."/'>".$d['firstname']." ".$d['lastname']."</a> recommends you</p>";
      else echo "<p>\"".$d['comment']."\" <nobr><a href='/user/".$d['praise_from']."/'>-".$d['firstname']." ".$d['lastname']."</a></nobr>";
    }
  }
}

function getFollowerCard() {
  global $db;
  global $MYACCOUNT;
  $followers = $db->query("SELECT follow.*, accounts.firstname, accounts.lastname FROM follow, accounts WHERE follow_from=a_id AND follow_to=".$MYACCOUNT['d_id']." ORDER BY added DESC")->fetchAll();
  if ($MYACCOUNT['mode']) {
    echo "
    <div class='card'>
      <h1>Followers</h1>";
    if (count($followers) == 0) echo "<p>Actors can follow you.</p>";
    else {
      foreach ($followers as $d) {
        echo "<p><a href='/user/".$d['follow_from']."/'>".$d['firstname']." ".$d['lastname']."</a></p>";
      }
    }
    echo "</div>";
  }
}
?>

<!-- POPUPS -->

<div id="popup_post" class='popup'>
  <div class="card">
    <form onsubmit="postCall(this); return false">
      <input type="hidden" name="func" value="postCall">
      <input type='file' id="script_file" name='script' onchange="(this.files[0].size > 999999) ? addAlert('File is larger than 1mb') : document.getElementById('script_input').value='Script: '+this.value.substr(12, 9)+'..'" style="display:none">
      <input type='file' id="poster_file" name='poster' onchange="(this.files[0].size > 999999) ? addAlert('File is larger than 1mb') : document.getElementById('poster_input').value='Poster: '+this.value.substr(12, 9)+'..'" style="display:none" accept="image/x-png,image/jpeg">

      <h1>Create Call</h1>
      <div class='row'>
        <div class="label">
          <p>Title</p>
          <input type='text' name='title' spellcheck='false' autocomplete='off' maxlength='100' placeholder="Guardians of the Galaxy"><br>
        </div>
        <div class="label">
          <p>Class</p>
          <select name="type">
            <option value='0'>Choose Class</option>
            <?php
            $classes = $db->query("SELECT * FROM classes")->fetchAll();
            foreach ($classes as $d) {
              echo "<option value='".$d['id']."'>".$d['class']."</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <div class='row'>
        <div class='label'>
          <p>Genre</p>
          <select name='genre'>
            <option value='0'>Choose Genre</option>
            <?php
            $genres = $db->query("SELECT * FROM genres")->fetchAll();
            foreach ($genres as $d) {
              echo "<option value='".$d['id']."'>".$d['genre']."</option>";
            }
            ?>
          </select>
        </div>
        <div class='label'>
          <p>Genre</p>
          <select name='genre2'>
            <option value='0'>Choose Genre · Optional</option>
            <?php
            foreach ($genres as $d) {
              echo "<option value='".$d['id']."'>".$d['genre']."</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <div id="auditions">
        <div class='row'>
          <div class="label">
            <p>Audition Time</p>
          </div>
          <div class="label">
            <p>Place</p>
          </div>
        </div>
        <div class='row' data-row>
          <div class="label">
            <input type='text' data-input='time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='9/23 5:30pm'>
          </div>
          <div class="label">
            <div class='c_add' data-add onclick="addElement('auditions')">+</div>
            <input type='text' data-input='place' spellcheck='false' autocomplete='off' maxlength='19' placeholder="Dodge Rm 100">
          </div>
        </div>
      </div>
      <div id="shootings">
        <div class='row'>
          <div class="label">
            <p>Shooting Dates From</p>
          </div>
          <div class="label">
            <p>To</p>
          </div>
        </div>
        <div class='row' data-row>
          <div class="label">
            <input type='text' data-input='from' spellcheck='false' autocomplete='off' maxlength='19' placeholder='11/4'>
          </div>
          <div class="label">
            <div class='c_add' data-add onclick="addElement('shootings')">+</div>
            <input type='text' data-input='to' spellcheck='false' autocomplete='off' maxlength='19' placeholder='11/6'>
          </div>
        </div>
      </div>
      <div class="label">
        <p>Storyline</p>
        <textarea name='storyline' rows='3' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Brash space adventurer Peter Quill (Chris Pratt) finds himself the quarry of relentless bounty hunters after he steals an orb coveted by Ronan, a powerful villain."></textarea>
      </div>
      <h2>CHARACTERS</h2>
      <hr>
      <div id="characters">
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
        <div data-row>
          <div class='row'>
            <div class="label" style="width:170px">
              <input type='text' data-input='name' spellcheck='false' autocomplete='off' maxlength='19' placeholder='Peter Quill'>
            </div>
            <div class="label" style="width:75px">
              <input type='text' data-input='min' spellcheck='false' autocomplete='off' maxlength='19' placeholder='24' >
            </div>
            <div class="label" style="width:75px">
              <input type='text' data-input='max' spellcheck='false' autocomplete='off' maxlength='19' placeholder='35'>
            </div>
            <div class="label" style="width:170px">
              <div class='c_add' data-add onclick="addElement('characters')">+</div>
              <select data-input='gender'>
                <option value="3">Either</option>
                <option value="1">Male</option>
                <option value="2">Female</option>
              </select>
            </div>
          </div>
          <textarea rows='2' data-input='description' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Peter Quill is an interstellar adventurer who was abducted from Earth at a young age. He is the comedic hero of this galactic adventure."></textarea>
        </div>
      </div>
      <input type='submit' value='Create Casting Call'><input type='button' id='script_input' value='Upload Script' onclick="document.getElementById('script_file').click()"><input type='button' id='poster_input' value='Upload Poster' onclick="document.getElementById('poster_file').click()">
    </form>
  </div>
</div>
<div id="popup_edit" class='popup'>
  <div class="card">
    <form onsubmit="editCall(this); return false">
      <input type="hidden" name="func" value="editCall">
      <input type="hidden" name="call_id" id="edit_call_id">
      <input type='file' id="script_file" name='script' onchange="(this.files[0].size > 999999) ? addAlert('File is larger than 1mb') : document.getElementById('script_input').value='Script: '+this.value.substr(12, 9)+'..'" style="display:none">
      <input type='file' id="poster_file" name='poster' onchange="(this.files[0].size > 999999) ? addAlert('File is larger than 1mb') : document.getElementById('poster_input').value='Poster: '+this.value.substr(12, 9)+'..'" style="display:none" accept="image/x-png,image/jpeg">

      <h1>Edit Call</h1>
      <div class='row'>
        <div class="label">
          <p>Title</p>
          <input type='text' name='title' spellcheck='false' autocomplete='off' maxlength='100' placeholder="Guardians of the Galaxy"><br>
        </div>
        <div class="label">
          <p>Class</p>
          <select name="type">
            <option value='0'>Choose Class</option>
            <?php
            $classes = $db->query("SELECT * FROM classes")->fetchAll();
            foreach ($classes as $d) {
              echo "<option value='".$d['id']."'>".$d['class']."</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <div class='row'>
        <div class='label'>
          <p>Genre</p>
          <select name='genre'>
            <option value='0'>Choose Genre</option>
            <?php
            $genres = $db->query("SELECT * FROM genres")->fetchAll();
            foreach ($genres as $d) {
              echo "<option value='".$d['id']."'>".$d['genre']."</option>";
            }
            ?>
          </select>
        </div>
        <div class='label'>
          <p>Genre</p>
          <select name='genre2'>
            <option value='0'>Choose Genre · Optional</option>
            <?php
            foreach ($genres as $d) {
              echo "<option value='".$d['id']."'>".$d['genre']."</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <div class='row'>
        <div class="label">
          <p>Additional Audition Time</p>
        </div>
        <div class="label">
          <p>Place</p>
        </div>
      </div>
      <div id="edit_auditions">
        <div class='row' data-row>
          <div class="label">
            <input type='text' data-input='time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='9/23 5:30pm'>
          </div>
          <div class="label">
            <div class='c_add' data-add onclick="addElement('edit_auditions')">+</div>
            <input type='text' data-input='place' spellcheck='false' autocomplete='off' maxlength='19' placeholder="Dodge Rm 100">
          </div>
        </div>
      </div>
      <div class='row'>
        <div class="label">
          <p>Additional Shooting Dates From</p>
        </div>
        <div class="label">
          <p>To</p>
        </div>
      </div>
      <div id="edit_shootings">
        <div class='row' data-row>
          <div class="label">
            <input type='text' data-input='from' spellcheck='false' autocomplete='off' maxlength='19' placeholder='11/4'>
          </div>
          <div class="label">
            <div class='c_add' data-add onclick="addElement('edit_shootings')">+</div>
            <input type='text' data-input='to' spellcheck='false' autocomplete='off' maxlength='19' placeholder='11/6'>
          </div>
        </div>
      </div>
      <div class="label">
        <p>Storyline</p>
        <textarea name='storyline' rows='3' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Brash space adventurer Peter Quill (Chris Pratt) finds himself the quarry of relentless bounty hunters after he steals an orb coveted by Ronan, a powerful villain."></textarea>
      </div>
      <h2>ADDITIONAL CHARACTERS</h2>
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
      <div id="edit_characters">
        <div data-row>
          <div class='row'>
            <div class="label" style="width:170px">
              <input type='text' data-input='name' spellcheck='false' autocomplete='off' maxlength='19' placeholder='Peter Quill'>
            </div>
            <div class="label" style="width:75px">
              <input type='text' data-input='min' spellcheck='false' autocomplete='off' maxlength='19' placeholder='24' >
            </div>
            <div class="label" style="width:75px">
              <input type='text' data-input='max' spellcheck='false' autocomplete='off' maxlength='19' placeholder='35'>
            </div>
            <div class="label" style="width:170px">
              <div class='c_add' data-add onclick="addElement('edit_characters')">+</div>
              <select data-input='gender'>
                <option value="3">Either</option>
                <option value="1">Male</option>
                <option value="2">Female</option>
              </select>
            </div>
          </div>
          <textarea rows='2' data-input='description' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Peter Quill is an interstellar adventurer who was abducted from Earth at a young age. He is the comedic hero of this galactic adventure."></textarea>
        </div>
      </div>
      <input type='submit' value='Update Casting Call'><input type='button' id='script_input' value='Update Script' onclick="document.getElementById('script_file').click()"><input type='button' id='poster_input' value='Update Poster' onclick="document.getElementById('poster_file').click()">
    </form>
  </div>
</div>
<div id="popup_settings" class='popup'>
  <div class='card'>
    <form onsubmit='updateInfo(this); return false'>
      <input type="hidden" name="func" value="updateInfo">
      <h1>Update Your Information</h1>
      <div class='row'>
        <div class="label">
          <p>Firstname</p>
          <input type='text' name='firstname' value="<?php echo $MYACCOUNT['firstname'] ?>" spellcheck='false' autocomplete='off' maxlength='20'>
        </div>
        <div class="label">
          <p>Lastname</p>
          <input type='text' name='lastname' value="<?php echo $MYACCOUNT['lastname'] ?>" spellcheck='false' autocomplete='off' maxlength='20'>
        </div>
      </div>
      <div class='row'>
        <div class="label">
          <p>Date of Birth</p>
          <input type='text' name='birthdate' spellcheck='false' autocomplete='off' maxlength='19' placeholder='mm/dd/YYYY' value="<?php echo format($MYACCOUNT['birthdate'], 'n/j/Y') ?>" onkeyup="checkDate(event, this)">
        </div>
        <div class='label'>
          <p>Gender</p>
          <select name="gender">
            <option value="0">Select</option>
            <option value="1" <?php echo $MYACCOUNT['gender']==1?"selected":"" ?>>Male</option>
            <option value="2" <?php echo $MYACCOUNT['gender']==2?"selected":"" ?>>Female</option>
          </select>
        </div>
      </div>
      <div class="label">
        <p>Bio</p>
        <textarea id="input_bio" rows='2' spellcheck='false' autocomplete='off' maxlength='1000' name="bio" placeholder="Profile pictures speak 1,000 words. Biographies speak like 50 tops."><?php echo str_replace("<br />", "", $MYACCOUNT['bio']) ?></textarea>
      </div>
      <input type='submit' value='Update Information'><input type='button' value='Logout' onclick="window.location.href='/logout/'">
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
          <input type='text' name='youtubeLink' spellcheck='false' autocomplete='off' maxlength='40' placeholder="youtube.com/watch?v=5mF0le5Y96M">
        </div>
        <div class="label">
          <p>or Vimeo Link</p>
          <input type='text' name='vimeoLink' spellcheck='false' autocomplete='off' maxlength='40' placeholder="vimeo.com/67790369">
        </div>
      </div>
      <input type='submit' value='Add Video'>
    </form>
  </div>
</div>
<div id="popup_photo" class='popup popup_photo'></div>
<div id="popup_confirm_interest" class='popup'>
  <div class='card' style='text-align:center'>
    <h1>Confirm</h1>
    <p>Are you sure you want to revoke your interest?</p>
    <input type='button' value='Revoke Interest' id='confirm_yes'>
  </div>
</div>
<div id="popup_email" class='popup'>
  <div class='card'>
    <form onsubmit='email(this); return false'>
      <input type="hidden" name="func" value="emailBlast">
      <input type='file' id="attachment_file" name='attachment' onchange="(this.files[0].size > 999999) ? addAlert('File is larger than 1mb') : document.getElementById('attachment_input').value='File: '+this.value.substr(12, 9)+'..'" style="display:none">
      <input type="hidden" name="call_id" value="0" id='email_call_id'>
      <h1>Email Blast</h1>
      <div class="label">
        <textarea rows='5' name='body' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="This will reach all those currently interested in the casting call."></textarea>
      </div>
      <input type='submit' value='Send Email'><input type='button' id='attachment_input' value='Add Attachment' onclick="document.getElementById('attachment_file').click()">
    </form>
  </div>
</div>


<!-- PROFILE HEADER -->

<div id='profile'>
  <input type='file' id="profile_pic_file" onchange="uploadProfilePic(this)" style="display:none" accept="image/x-png,image/jpeg">
  <div class='c_pic' onclick="document.getElementById('profile_pic_file').click()" style="background-image: url('<?php echo $asset_profile; ?>')"></div>
  <div class='card'>
    <input type='button' class='c_edit <?php echo $MYACCOUNT['mode'] ? "c_director" : "c_talent" ?>' onclick="window.location.href='/toggle/'">
    <h1 id="name"><?php echo $MYACCOUNT['firstname']." ".$MYACCOUNT['lastname'] ?></h1>
    <p><b><?php echo $MYACCOUNT['mode'] ? "Director" : "Talent" ?> · <?php echo ($MYACCOUNT["gender"] == 1) ? "Male" : "Female"; ?> · Age <?php echo $MYACCOUNT['age'] ?></b></p>
  </div>
  <div class='c_buttons'>
    <input type='button' value='<?php echo $MYACCOUNT['mode'] ? "Director" : "Talent" ?> Settings' onclick="togglePopup(document.getElementById('popup_settings'))"><br>
  </div>
</div>

<!-- PROFILE CARDS -->
<div class='card_column_left'>
  <div class='card'>
    <input type='button' class='c_edit' value="Edit" onclick="togglePopup(document.getElementById('popup_settings'))">
    <h1>Bio</h1>
    <p><?php echo $MYACCOUNT['bio'] != "" ? $MYACCOUNT['bio'] : "You have no bio." ?></p>
  </div>
  <div class='card'>
    <h1>Praise</h1>
    <?php getPraise() ?>
  </div>
  <?php getFollowerCard() ?>
</div>
<div class='card_column_right'>
  <div class='card'>
  <?php
  if ($MYACCOUNT['mode']) getDirectorCard();
  else getTalentCard();
  ?>
  </div>
  <div class='card'>
    <input type='button' class='c_edit c_add' value="+" onclick="togglePopup(document.getElementById('popup_video'))">
    <h1>Videos</h1>
    <?php getVideos() ?>
  </div>
  <div class='card'>
    <input type='file' id="upload_pic" onchange="uploadPic(this)" style="display:none" accept="image/x-png,image/jpeg">
    <input type='button' class='c_edit c_add' value="+" onclick="document.getElementById('upload_pic').click()">
    <h1>Photos</h1>
    <?php getPhotos() ?>
  </div>
</div>

<!-- SCRIPTS -->

<script>
  function email(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      r = JSON.parse(r)
      if (r["status"] == "ok") {
        togglePopup(currentPopup)
        setTimeout(function() {
          window.location.href = "/"
        },300)
      } else addAlert(r['message'])
    })
  }

  function interestedConfirm(sender, char_id) {
    togglePopup(document.getElementById("popup_confirm_interest"))
    document.getElementById("confirm_yes").onclick = function() {
      post("/resources/ajax/functions.php", {"func": "interested", "char_id": char_id}, function(r) {
        r = JSON.parse(r)
        if (r['status'] == 'ok' && r['interested']) sender.className = "interested"
        else if (r['status'] == 'ok' && !r['interested']) {
          sender.className = ""
          togglePopup(currentPopup)
          setTimeout(function() {
            window.location.href = "/"
          },300)
        } else addAlert(r['message'])
      })
    }
  }

  function postCall(form) {
    data = parse(form)
    data["auditions"] = parseArray("auditions")
    data["shootings"] = parseArray('shootings')
    data["characters"] = parseArray('characters')
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

  function postCall(form) {
    data = parse(form)
    data["auditions"] = parseArray("edit_auditions")
    data["shootings"] = parseArray('edit_shootings')
    data["characters"] = parseArray('edit_characters')
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

  function getEditCall(id) {
    post("/resources/ajax/functions.php", {"func": "getCall", "id": id}, function(r) {
      r = JSON.parse(r)
      if (r["status"] == "ok") {
        var popup = document.getElementById("popup_edit")
        popup.querySelector("input[name='title']").value = r['call']['title']
        popup.querySelector("select[name='type']").selectedIndex = r['call']['type']
        popup.querySelector("select[name='genre']").selectedIndex = r['call']['genre']
        if (r['call']['genre2']) popup.querySelector("select[name='genre2']").selectedIndex = r['call']['genre2']
        popup.querySelector("textarea[name='storyline']").value = r['call']['storyline']
        togglePopup(popup)
      } else addAlert(r['message'])
    })
  }
        // popup.querySelector("[data-collaborators]").innerHTML = ""
        // r['collaborators'].forEach(function(d) {
        //   popup.querySelector("[data-collaborators]").innerHTML += "<div class='c_text'><a href='/user/"+d['d_id']+"/'>"+d['firstname']+" "+d['lastname']+"</a></div>"
        // })
        // popup.querySelector("[data-auditions]").innerHTML = ""
        // r['auditions'].forEach(function(d) {
        //   popup.querySelector("[data-auditions]").innerHTML += "<div class='row'> \
        //                                                           <div class='label' style='width: 205px'> \
        //                                                             <div class='c_text'>"+d['audition_time']+"</div> \
        //                                                           </div> \
        //                                                           <div class='label'> \
        //                                                             <div class='c_text'>"+d['audition_place']+"</div> \
        //                                                           </div> \
        //                                                         </div>"
        // })
        // popup.querySelector("[data-shootings]").innerHTML = ""
        // r['shootings'].forEach(function(d) {
        //   popup.querySelector("[data-shootings]").innerHTML += "<div class='row'> \
        //                                                           <div class='label' style='width: 205px'> \
        //                                                             <div class='c_text'>"+d['shooting_from']+"</div> \
        //                                                           </div> \
        //                                                           <div class='label'> \
        //                                                             <div class='c_text'>"+d['shooting_to']+"</div> \
        //                                                           </div> \
        //                                                         </div>"
        // })
        // popup.querySelector("[data-characters]").innerHTML = ""
        // r['characters'].forEach(function(d) {
        //   popup.querySelector("[data-characters]").innerHTML += "<div class='row'> \
        //                                                           <div class='label' style='width:160px'> \
        //                                                             <div class='c_text'><b>"+d['name']+"</b></div> \
        //                                                           </div> \
        //                                                           <div class='label' style='width:75px'> \
        //                                                             <div class='c_text'>"+d['min']+"</div> \
        //                                                           </div> \
        //                                                           <div class='label' style='width:75px'> \
        //                                                             <div class='c_text'>"+d['max']+"</div> \
        //                                                           </div> \
        //                                                           <div class='label' style='width:173px; position: relative'> \
        //                                                             <input type='button' style='top: -11px' class='c_edit "+(d['interested'] ? 'interested' : '')+"' value='Interested' onclick='interested(this, "+d['id']+")' "+(d['can_interested'] ? '' : 'disabled')+"> \
        //                                                             <div class='c_text'>"+(d['gender']==1?"Male":d['gender']==2?"Female":"Any Gender")+"</div> \
        //                                                           </div> \
        //                                                         </div> \
        //                                                         <div class='c_text'><p style='width: 405px'>"+d['description']+"</p></div>"
        // })

  function uploadProfilePic(input) {
    if (input.files[0].size <= 999999) {
      post("/resources/ajax/functions.php", {"func": "uploadProfilePic", "image": input.files[0]}, function(r) {
        r = JSON.parse(r)
        if (r['status'] == 'ok') window.location.href = "/"
        addAlert(r['message'])
      })
    } else addAlert("That file is larger than 500kb")
  }

  function uploadPic(input) {
    if (input.files[0].size <= 999999) {
      post("/resources/ajax/functions.php", {"func": "uploadPic", "image": input.files[0]}, function(r) {
        r = JSON.parse(r)
        if (r['status'] == 'ok') window.location.href = "/"
        addAlert(r['message'])
      })
    } else addAlert("That file is larger than 500kb")
  }

  function updateInfo(form) {
    data = parse(form)
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

  function addVideo(form) {
    data = parse(form)
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

  function deleteAsset(id) {
    post("/resources/ajax/functions.php", {"func": "deleteAsset", "id": id}, function(r) {
      r = JSON.parse(r)
      if (r["status"] == "ok") window.location.href = "/"
      else addAlert(r['message'])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

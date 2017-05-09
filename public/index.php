<?php include "../inc/header.php";

$praise = $db->query("SELECT praise.*, accounts.firstname, accounts.lastname FROM praise, accounts WHERE praise_to=$page_id AND (praise_from=a_id OR praise_from=d_id) ORDER BY added DESC")->fetchAll();
$followers = $db->query("SELECT follow.*, accounts.firstname, accounts.lastname FROM follow, accounts WHERE follow_from=a_id AND follow_to=".$MYACCOUNT['d_id']." ORDER BY added DESC")->fetchAll();

$assets = $db->query("SELECT id, url, title, type FROM assets WHERE page_id=$page_id ORDER BY added DESC");
$asset_profile = "/resources/images/placeholder.png";
$asset_videos = array();
$asset_photos = array();
foreach ($assets->fetchAll() as $asset) {
  if ($asset['type'] == 1) $asset_profile = "/resources/assets/profile/".$asset['url'];
  else if ($asset['type'] == 2) array_push($asset_photos, $asset);
  else if ($asset['type'] == 3 || $asset['type'] == 4) array_push($asset_videos, $asset);
}
?>

<!-- POPUPS -->

<div id="popup_post" class='popup'>
  <input type='button' class='c_edit' value="close" onclick="togglePopup(currentPopup)">
  <div class="card">
    <form onsubmit="postCall(this); return false">
      <input type="hidden" name="func" value="postCall">
      <h1>Create Call</h1>
      <div class="label">
        <p>Title</p>
        <input type='text' name='title' spellcheck='false' autocomplete='off' maxlength='100' placeholder="Guardians of the Galaxy"><br>
      </div>
      <div class="label">
        <p>Class</p>
        <select name="type">
        <?php
        $classes = $db->query("SELECT * FROM classes")->fetchAll();
        foreach ($classes as $class) {
          echo "<option value='".$class['id']."'>".$class['class']."</option>";
        }
        ?>
        </select>
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
            <input type='text' data-input='time' spellcheck='false' autocomplete='off' maxlength='19' placeholder='mm/dd/YYYY hh:mm am/pm' onkeyup="checkDateTime(event, this)">
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
            <input type='text' data-input='from' spellcheck='false' autocomplete='off' maxlength='19' placeholder='mm/dd/YYYY' onkeyup="checkDate(event, this)">
          </div>
          <div class="label">
            <div class='c_add' data-add onclick="addElement('shootings')">+</div>
            <input type='text' data-input='to' spellcheck='false' autocomplete='off' maxlength='19' placeholder='mm/dd/YYYY' onkeyup="checkDate(event, this)">
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
          <textarea rows='2' data-input='description' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Any more additional information pertaining to this actor or character?"></textarea>
        </div>
      </div>
      <input type='submit' value='Create Casting Call'><input type='button' value='Upload Script'>
    </form>
  </div>
</div>
<div id="popup_basic" class='popup'>
  <input type='button' class='c_edit' value="close" onclick="togglePopup(currentPopup)">
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
        <textarea id="input_bio" rows='3' spellcheck='false' autocomplete='off' maxlength='1000' name="bio" placeholder="Profile pictures speak 1,000 words but your's speaks 'cutey pie' so why not fill in the rest."><?php echo $MYACCOUNT['bio'] ?></textarea>
      </div>
      <h2>NOTIFICATIONS</h2><hr>
      <div class='row'>
        <div class="label">
          <p>Actor Followed You</p>
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
      </div>
      <div class='row'>
        <div class="label">
          <p>Actor Praised You</p>
          <select name="gender">
            <option value="1">In Site</option>
            <option value="2">Email</option>
            <option value="3" selected>In Site + Email</option>
          </select>
        </div>
      </div>
      <input type='submit' value='Update Information'><input type='button' value='Toggle to <?php echo $MYACCOUNT['mode'] ? "Talent" : "Director" ?> Mode' onclick="window.location.href='/toggle/'"><input type='button' value='Logout' onclick="window.location.href='/logout/'">
    </form>
  </div>
</div>
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
<div id="popup_video" class='popup'>
  <input type='button' class='c_edit' value="close" onclick="togglePopup(currentPopup)">
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
<div id="popup_auditions" class='popup'>
  <input type='button' class='c_edit' value="close" onclick="togglePopup(currentPopup)">
  <div class='card'>
    <?php
    $gender = $MYACCOUNT['gender'];
    $age = $MYACCOUNT['age'];
    $auditions = $db->query("SELECT characters.*, calls.title, (SELECT COUNT(*) FROM interested WHERE char_id=characters.id) as interested FROM characters JOIN calls ON characters.call_id=calls.id WHERE $age>=min AND $age<=max AND (gender=3 OR gender=$gender)")->fetchAll();
    echo "<h1>You've got Matches!</h1>";
    if (empty($auditions)) {
      echo "<p>There are no upcoming auditions for you.</p>";
    } else {
      $auditions_splice = array(array_splice($auditions, 0, ceil(count($auditions) / 2)), $auditions);
      foreach ($auditions_splice as $splice) {
        echo "<div class='column'>";
        foreach ($splice as $a) {
          $times = $db->query("SELECT * FROM auditions WHERE call_id=".$a['call_id']."")->fetchAll();
          echo "
          <div class='c_prospect'>
          <b>".$a["name"]." · <a href=''>".$a['title']."</a></b><br>
          ";
          foreach ($times as $t) echo "".format($t['audition_time'], "M jS · l · g:ia")."<br>";
          echo "<input type='button' class='".($a['interested']?'interested':'')."' value='interested' onclick='interestedHide(this, ".$a['id'].")'>
              </div>";
        }
        echo "</div>";
      }
    }
    ?>
  </div>
</div>

<!-- PROFILE HEADER -->

<div id='profile'>
  <input type='file' id="profile_pic_file" onchange="uploadProfilePic(this)" style="display:none" accept="image/x-png,image/jpeg">
  <div class='c_pic' onclick="document.getElementById('profile_pic_file').click()" style="background-image: url('<?php echo $asset_profile; ?>')"></div>
  <div class='card'>
    <h1 id="name"><?php echo $MYACCOUNT['firstname']." ".$MYACCOUNT['lastname'] ?></h1>
    <p><b><?php echo $MYACCOUNT['mode'] ? "Director" : "Talent" ?> · <?php echo ($MYACCOUNT["gender"] == 1) ? "Male" : "Female"; ?> · Age <?php echo $MYACCOUNT['age'] ?></b></p>
  </div>
  <div class='c_buttons'>
    <input type='button' value='Account Settings' onclick="togglePopup(document.getElementById('popup_basic'))"><br>
    <?php echo $MYACCOUNT['mode'] ? "" : "<input type='button' value='My Availability' onclick=\"togglePopup(document.getElementById('popup_basic'))\">"; ?>
  </div>
</div>

<!-- PROFILE CARDS -->
<div class='card_column_left'>
  <div class='card'>
    <input type='button' class='c_edit' value="edit" onclick="togglePopup(document.getElementById('popup_basic'))">
    <h1>Bio</h1>
    <p><?php echo $MYACCOUNT['bio'] != "" ? $MYACCOUNT['bio'] : "You have no bio. <a onclick=\"togglePopup(document.getElementById('popup_basic'))\">Add a Bio?</a>" ?></p>
  </div>
  <div class='card'>
    <h1>Praise</h1>
    <?php
    if (count($praise) == 0) echo "<p>You have no praise.</p>";
    else {
      foreach ($praise as $d) {
        if ($d['heart']) echo "<p><a href='/user/".$d['praise_from']."/'>".$d['firstname']." ".$d['lastname']."</a> recommends you</p>";
        else echo "<p>\"".$d['comment']."\" <a href='/user/".$d['praise_from']."/'>-".$d['firstname']." ".$d['lastname']."</a>";
      }
    }
    ?>
  </div>
  <?php
  if ($MYACCOUNT['mode']) {
    echo "
    <div class='card'>
      <h1>Followers</h1>";
    if (count($followers) == 0) echo "<p>This director has no followers.</p>";
    else {
      foreach ($followers as $d) {
        echo "<p><a href='/user/".$d['follow_from']."/'>".$d['firstname']." ".$d['lastname']."</a></p>";
      }
    }
    echo "
    </div>
    ";
  }
  ?>
</div>
<div class='card_column_right'>
  <div class='card'>
  <?php
  if ($MYACCOUNT['mode']) {
    $calls = $db->query("SELECT calls.id, title, type, storyline, calls.added FROM calls JOIN collaborators ON calls.id=collaborators.call_id WHERE d_id='$page_id'")->fetchAll();
    echo "
    <input type='button' class='c_edit c_add' value='+' onclick=\"togglePopup(document.getElementById('popup_post'))\">
    <h1>Your Calls</h1>";
    if (count($calls) == 0) {
      echo "You have no calls. <a onclick=\"togglePopup(document.getElementById('popup_post'))\">Create a Call?</a>";
    } else {
      foreach ($calls as $call) {
        $characters = $db->query("SELECT * FROM characters LEFT JOIN (SELECT char_id, COUNT(*) as cnt FROM interested GROUP BY char_id) t2 ON char_id=id WHERE call_id=".$call['id']." ORDER BY cnt DESC")->fetchAll();
        $interested = $db->query("SELECT char_id, a_id, url, type FROM interested JOIN characters ON interested.char_id=characters.id JOIN assets ON interested.a_id=assets.page_id WHERE assets.type=1")->fetchAll();
        echo "
        <div class='c_item' style='text-align: left; position: relative'>
          <input type='button' class='c_edit' value='email blast'>
          <input type='button' class='c_edit' value='edit' style='right: 97px'>
          <div class='c_text'>
            <b><a onclick=\"getCall('".$call['id']."')\">".$call["title"]."</a></b>
          </div>";
        foreach ($characters as $c) {
          echo "<div class='c_text'><b><a>".$c["name"]."</b></a> · ".($c["cnt"] ? $c["cnt"]:0)." actors interested</div>";
          foreach ($interested as $i) {
            if ($i['char_id'] == $c['id']) {
              echo "<div class='c_photo' style='background-image: url(/resources/assets/profile/".$i['url'].")'></div>";
            }
          }
        }
        echo "</div>";
      }
    }
  } else {
    $characters = $db->query("SELECT characters.*, calls.title, calls.type FROM interested JOIN characters ON interested.char_id=characters.id JOIN calls ON characters.call_id=calls.id WHERE interested.a_id=".$MYACCOUNT['a_id']." ORDER BY interested.added DESC")->fetchAll();
    echo "
    <input type='button' class='c_edit c_add' value='+' onclick=\"window.location.href='/calls/'\">
    <h1>Your Auditions</h1>";
    if (empty($characters)) {
      echo "<p>You have no interest in any auditions. <a href='/calls/'>Find an audition?</a></p>";
    } else {
      foreach ($characters as $c) {
        $auditions = $db->query("SELECT * FROM auditions WHERE call_id=".$c['call_id']."")->fetchAll();
        echo "<div class='c_prospect'>
                <b>".$c["name"]." · <a onclick=\"getCall('".$c['call_id']."')\">".$c['title']."</a></b><br>
                ".$classes[$c['type']]['class']."<br>";
        foreach ($auditions as $d) echo "Audition · <a href='https://www.google.com/calendar/render?action=TEMPLATE&text=".$c['title']." Audition&dates=20140127T224000Z/20140320T221500Z&details=Auditioning for ".$c['name'].": ".$c['description']."&location=".$d['audition_place']."&sf=true&output=xml' target='_blank' rel='nofollow'>".format($d['audition_time'], "g:ia D, M jS")."</a><br>";
        echo "<input type='button' class='interested' value='interested' onclick='interestedHide(this, ".$c['id'].")'>
              <input type='button' value='download script'>
            </div>";
      }
    }
  }
  ?>
  </div>
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
          echo "<div class='label'>
                  <input type='button' class='c_edit c_delete card_delete_video' value='-' onclick=\"deleteAsset(".$asset['id'].")\">
                  <p>".$asset['title']."</p>
                  <iframe src='https://www.youtube.com/embed/".$asset['url']."' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                </div>";
        } else if ($asset['type'] == 4) {
          echo "<div class='label'>
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
        echo "<div class='c_photo' style=\"background-image: url(/resources/assets/photos/".$asset['url'].")\" onclick=\"document.getElementById('popup_photo').style.backgroundImage='url(/resources/assets/photos_large/".$asset['url'].")'; togglePopup(document.getElementById('popup_photo'))\">
              <input type='button' class='c_edit c_delete' value='-' onclick=\"deleteAsset(".$asset['id'].")\"></div>";
      }
      echo "</div>";
    }
    ?>
  </div>
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
          console.log(d)
          // popup.querySelector("[data-collaborators]").innerHTML += "<div class='c_text'><a href='/user/"+d['d_id']+"/'>"+d['firstname']+" "+d['lastname']+"</a></div>"
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

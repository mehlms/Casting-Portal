<?php include "../inc/header.php";

if ($MYACCOUNT['d_id'] == $data || $MYACCOUNT['a_id'] == $data) echo "<script>window.location.href = '/'</script>";
$ACCOUNT = $db->query("SELECT accounts.*, (SELECT heart FROM praise WHERE praise_from=$page_id AND praise_to=$data AND heart=1) as heart, (SELECT COUNT(*) FROM follow WHERE follow_from=$page_id AND follow_to=$data) as followed FROM accounts WHERE a_id=$data OR d_id=$data")->fetch();
if ($ACCOUNT == null) echo "<script>window.location.href = '/404/'</script>";

$ACCOUNT['mode'] = ($data==$ACCOUNT['d_id']) ? 1 : 0;
$ACCOUNT['age'] = date_diff(date_create($ACCOUNT['birthdate']), date_create('now'))->y;
$ACCOUNT['bio'] = $ACCOUNT['mode'] ? $ACCOUNT['d_bio'] : $ACCOUNT['a_bio'];

$assets = $db->query("SELECT id, url, title, type FROM assets WHERE page_id=$data ORDER BY added DESC");
$asset_profile = "/resources/images/placeholder.png";
$asset_videos = array();
$asset_photos = array();
foreach ($assets->fetchAll() as $asset) {
  if ($asset['type'] == 1) $asset_profile = "/resources/assets/profile/".$asset['url'];
  else if ($asset['type'] == 2) array_push($asset_photos, $asset);
  else if ($asset['type'] == 3 || $asset['type'] == 4) array_push($asset_videos, $asset);
}

function getVideos() {
  global $asset_videos;
  global $ACCOUNT;

  if (empty($asset_videos)) {
    echo "<p>This ".($ACCOUNT['mode'] ? "director" : "talent")." has no videos.</p>";
  } else {
    echo "<div class='c_assets'>";
    foreach ($asset_videos as $d) {
      if ($d['type'] == 3) {
        echo "<div class='label'>
                <p>".$d['title']."</p>
                <iframe src='https://www.youtube.com/embed/".$d['url']."' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
              </div>";
      } else if ($d['type'] == 4) {
        echo "<div class='label'>
                <p>".$d['title']."</p>
                <iframe src='https://player.vimeo.com/video/".$d['url']."' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
              </div>";
      }
    }
    echo "</div>";
  }
}

function getPhotos() {
  global $asset_photos;
  global $ACCOUNT;

  if (empty($asset_photos)) {
    echo "<p>This ".($ACCOUNT['mode'] ? "director" : "talent")." has no photos.</p>";
  } else {
    echo "<div class='c_assets'>";
    foreach ($asset_photos as $d) {
      echo "<div class='c_photo' style=\"background-image: url(/resources/assets/photos/".$d['url'].")\" onclick=\"document.getElementById('popup_photo').style.backgroundImage='url(/resources/assets/photos_large/".$d['url'].")'; togglePopup(document.getElementById('popup_photo'))\"></div>";
    }
    echo "</div>";
  }
}

function getFollowerCard() {
  global $ACCOUNT;

  if ($ACCOUNT['mode']) {
    global $db;
    global $data;
    $followers = $db->query("SELECT follow.*, accounts.firstname, accounts.lastname FROM follow, accounts WHERE follow_from=a_id AND follow_to=$data ORDER BY added DESC")->fetchAll();

    echo "
    <div class='card'>
      <h1>Followers</h1>";
    if (count($followers) == 0) echo "<p>This director has no followers.</p>";
    else {
      foreach ($followers as $d) {
        echo "<p><a href='/user/".$d['follow_from']."/'>".$d['firstname']." ".$d['lastname']."</a></p>";
      }
    }
    echo "</div>";
  }
}

function getPraiseCard() {
  global $db;
  global $ACCOUNT;
  global $MYACCOUNT;
  global $data;
  $praise = $db->query("SELECT praise.*, accounts.firstname, accounts.lastname FROM praise, accounts WHERE praise_to=$data AND (praise_from=a_id OR praise_from=d_id) ORDER BY added DESC")->fetchAll();

  echo "<div class='card'>";
  if ($ACCOUNT['mode'] != $MYACCOUNT['mode']) {
    echo "
    <form onsubmit='praise(this); return false;'>
      <input type='hidden' name='func' value='praise'>
      <input type='hidden' name='praise_to' value='".$data."'>
      <input type='hidden' name='heart' value='1'>
      <input type='submit' value='' class='c_edit c_heart c_add ".($ACCOUNT['heart'] ? "c_heart_filled" : '')."'><input type='button' class='c_edit c_add' value='+' onclick=\"togglePopup(document.getElementById('popup_praise'))\">
    </form>";
  }
  echo "<h1>Praise</h1>";
  if (count($praise) == 0) echo "<p>No praise yet.</p>";
  else {
    foreach ($praise as $d) {
      if ($d['heart']) echo "<p><a href='/user/".$d['praise_from']."/'>".$d['firstname']." ".$d['lastname']."</a> <nobr>recommends ".$ACCOUNT['firstname']."</nobr></p>";
      else echo "<p>\"".$d['comment']."\" <nobr><a href='/user/".$d['praise_from']."/'>-".$d['firstname']." ".$d['lastname']."</a></nobr>";
    }
  }
  echo "</div>";
}
?>

<!-- POPUPS -->

<div id="popup_praise" class='popup'>
  <div class='card'>
    <form onsubmit='praise(this); return false;'>
      <input type='hidden' name="func" value="praise">
      <input type='hidden' name="praise_to" value="<?php echo $data ?>">
      <h1>Leave a Comment</h1>
      <div class="label">
        <textarea name='comment' rows='3' spellcheck='false' autocomplete='off' maxlength='140' name="bio" placeholder="Worked together? Leave some feedback in 140 characters."></textarea>
      </div>
      <input type='submit' value='Add Praise'>
    </form>
  </div>
</div>
<div id="popup_email" class='popup'>
  <div class='card'>
    <form onsubmit="sendFormPopup('email', this); return false">
      <input type='file' id="attachment_file" name='attachment' onchange="(this.files[0].size > 999999) ? addAlert('File is larger than 1mb') : document.getElementById('attachment_input').value='File: '+this.value.substr(12, 9)+'..'" style="display:none">
      <input type="hidden" name="user_id" value="<?php echo $ACCOUNT['id'] ?>">
      <h1>Email</h1>
      <div class="label">
        <textarea rows='5' name='body' spellcheck='false' autocomplete='off' maxlength='1000' placeholder='This email will go directly to the selected user.'></textarea>
      </div>
      <input type='submit' value='Send'><input type='button' id='attachment_input' value='Add Attachment' onclick="document.getElementById('attachment_file').click()">
    </form>
  </div>
</div>

<!-- PROFILE HEADER -->

<div id='profile'>
  <div class='c_pic' style="background-image: url('<?php echo $asset_profile; ?>')"></div>
  <div class='card'>
    <input type='button' class='c_edit <?php echo $ACCOUNT['mode'] ? "c_director" : "c_talent" ?>' onclick="window.location.href='/user/<?php echo $ACCOUNT['mode'] ? $ACCOUNT['a_id'] : $ACCOUNT['d_id'] ?>/'">
    <h1 id="name"><?php echo $ACCOUNT['firstname']." ".$ACCOUNT['lastname'] ?></h1>
    <p><b><?php echo $ACCOUNT['mode'] ? "Director" : "Talent" ?> · <?php echo ($ACCOUNT["gender"] == 1) ? "Male" : "Female"; ?> · Age <?php echo $ACCOUNT['age'] ?></b></p>
  </div>
  <div class='c_buttons'>
    <input type='button' value='Send Email' onclick="togglePopup(document.getElementById('popup_email'))"><br>
    <?php
    if (!$MYACCOUNT['mode'] && $ACCOUNT['mode']) {
      echo "
      <form onsubmit=\"sendForm('follow', this); return false;\">
        <input type='hidden' name='func' value='follow'>
        <input type='hidden' name='follow_to' value=".$data.">
      <input type='submit' value='".($ACCOUNT['followed'] ? "Followed" : "Follow")."' class='".($ACCOUNT['followed'] ? "interested" : "")."'><br>
      </form>";
    }
    ?>
  </div>
</div>

<!-- PROFILE CARDS -->
<div class='card_column_left'>
  <div class='card'>
    <h1>Bio</h1>
    <p><?php echo $ACCOUNT['bio'] != "" ? $ACCOUNT['bio'] : "This ".($ACCOUNT['mode'] ? "director" : "talent")." has no bio." ?></p>
  </div>
  <?php getPraiseCard() ?>
  <?php getFollowerCard() ?>
</div>
<div class='card_column_right'>
  <div class='card'>
    <h1>Videos</h1>
    <?php getVideos() ?>
  </div>
  <div class='card'>
    <h1>Photos</h1>
    <?php getPhotos() ?>
  </div>
</div>

<!-- SCRIPTS -->

<script>
  function praise(form) {
    post("/resources/ajax/praise.php", parse(form), function(r) {
      r = JSON.parse(r)
      if (r['status'] == 'ok' && r['heart'] == 1) window.location.href = window.location.href
      else if (r['status'] == 'ok') {
        togglePopup(currentPopup)
        setTimeout(function() {
          window.location.href = window.location.href
        }, 300)
      }
      else addAlert(r['message'])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

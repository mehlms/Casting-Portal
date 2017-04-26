<?php include "../inc/header.php";
$USERACCOUNT = $db->query("SELECT * FROM accounts WHERE d_id='$data'")->fetch();
$ASSETS = $db->query("SELECT url, title, type FROM assets WHERE page_id='$data'");
$profilePic = "";
$vimeoVideos = array();
$youtubeVideos = array();
foreach ($ASSETS->fetchAll() as $asset) {
  if ($asset['type'] == 1) $profilePic = "/resources/assets/profile/".$asset['url'];
  if ($asset['type'] == 3) array_push($vimeoVideos, $asset);
  if ($asset['type'] == 2) array_push($youtubeVideos, $asset);
}
?>

<div class="profile_header">
  <input type='file' id="file" onchange="uploadImage(this)" name='image_upload_file' style="display:none">
  <div class="profile_picture" onclick="document.getElementById('file').click()" id="profile_picture" style="background-image: url('<?php echo $profilePic ?>')"></div>
  <div class="equal">
    <h1 class='underline' style='text-align:left'><?php echo $USERACCOUNT['firstname']." ".$USERACCOUNT['lastname'] ?> <?php if ($MYACCOUNT['d_id'] == $USERACCOUNT['d_id']) echo "<input type='button' value='EDIT PROFILE' class='subscribe' onclick=\"location.pathname='/settings/'\">"; else echo "<input type='button' value='+ SUBSCRIBE' class='subscribe' onclick='subscribe(this)'>"; ?></h1>
    <h2>
      Age <?php echo date_diff(date_create($USERACCOUNT['birthdate']), date_create('now'))->y ?>, <?php if ($USERACCOUNT["gender"] == 1) echo "Male"; else if ($USERACCOUNT["gender"] == 2) echo "Female" ?>, Director <br>
      <?php echo $MYACCOUNT['d_bio']; ?>
    </h2>
  </div>
</div>
<div class='profile_videos'>
  <h1 style='text-align:left'>VIDEOS</h1>
  <?php
  foreach ($vimeoVideos as $asset) {
    echo "<h2>".$asset['title']."</h2>";
    echo "<iframe src='https://player.vimeo.com/video/".$asset['url']."' width='355' height='200' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";
  }
  foreach ($youtubeVideos as $asset) {
    echo "<h2>".$asset['title']."</h2>";
    echo "<iframe src='https://www.youtube.com/embed/".$asset['url']."' width='355' height='200' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";
  }
  ?>
</div>
<div>
  <h1 style='text-align:left'>RECOMMENDATIONS</h1>
  <!-- <h2>No recommendations yet..</h2> -->
</div>

<script>
  function uploadImage(image) {
    if (image.files[0].size <= 500000) {
      post("/resources/ajax/functions.php", {"func": "uploadImage", "image": image.files[0]}, function(r) {
        r = JSON.parse(r)
        if (r['status'] == 'ok') document.getElementById("profile_picture").style.backgroundImage = "url('/resources/assets/profile/"+r['filename']+"')"
        addAlert(r['message'])
      })
    } else addAlert("That file is larger than 500kb")
  }
</script>

<?php include "../inc/footer.php" ?>

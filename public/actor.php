<?php include "../inc/header.php";
$USERACCOUNT = $db->query("SELECT * FROM accounts WHERE a_id='$data'")->fetch();
$ASSETS = $db->query("SELECT url, type FROM assets WHERE page_id='$data'")->fetchAll();
$profilePic = "";
foreach ($ASSETS as $asset) if ($asset['type'] == 1) $profilePic = "/resources/assets/profile/".$asset['url'];
?>

<div class="profile_header">
  <input type='file' id="file" onchange="uploadImage(this)" name='image_upload_file' style="display:none">
  <div class="profile_picture" onclick="document.getElementById('file').click()" id="profile_picture" style="background-image: url('<?php echo $profilePic ?>')"></div>
  <div class="equal">
    <h1 class='underline'><?php echo $USERACCOUNT['firstname']." ".$USERACCOUNT['lastname'] ?> <input type='button' value='+ SUBSCRIBE' class='subscribe' onclick="subscribe(this)"></h1>
    <h2>
      Age <?php echo date_diff(date_create($USERACCOUNT['birthdate']), date_create('now'))->y ?>, <?php if ($USERACCOUNT["gender"] == 1) echo "Male"; else if ($USERACCOUNT["gender"] == 2) echo "Female" ?> <br>
      Actor <br>
    </h2>
  </div>
</div>

<script>
  function uploadImage(image) {
    if (image.files[0].size <= 500000) {
      post("/resources/ajax/functions.php", {"func": "uploadImage", "image": image.files[0]}, function(r) {
        console.log(r)
        r = JSON.parse(r)
        if (r['status'] == 'ok') document.getElementById("profile_picture").style.backgroundImage = "url('/resources/assets/profile/"+r['filename']+"')"
        addAlert(r['message'])
      })
    } else addAlert("That file is larger than 500kb")
  }
</script>

<?php include "../inc/footer.php" ?>

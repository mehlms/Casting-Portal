<?php include "../inc/header.php";
$birthdate = new DateTime($MYACCOUNT['birthdate']);
$bio = $MYACCOUNT['mode'] ? $MYACCOUNT['d_bio'] : $MYACCOUNT['a_bio'];
?>

<div class='div_row'>
  <div class='equal'>
    <h1>Personal Information</h1>
    <form onsubmit='updateInfo(this); return false'>
      <input type='hidden' name='func' value='updateInfo'>
      <label>FIRSTNAME</label><input type='text' value="<?php echo $MYACCOUNT['firstname']; ?>" spellcheck='false' autocomplete='off' maxlength='40' name='firstname'>
      <label>LASTNAME</label><input type='text' value="<?php echo $MYACCOUNT['lastname']; ?>" spellcheck='false' autocomplete='off' maxlength='40' name='lastname'>
      <label>DATE OF BIRTH</label>
      <div class="row">
        <input type='text' value="<?php echo $birthdate->format('m'); ?>" name='month' spellcheck='false' autocomplete='off' maxlength='2'>
        <input type='text' value="<?php echo $birthdate->format('d'); ?>" name='day' spellcheck='false' autocomplete='off' maxlength='2'>
        <input type='text' value="<?php echo $birthdate->format('Y'); ?>" name='year' spellcheck='false' autocomplete='off' maxlength='4'>
      </div>
      <label>BIO</label><textarea rows='2' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Tall, skinny black man who's interested in filming horror/sex scenes for cash only..." name="bio"><?php echo $bio; ?></textarea>
      <input type='submit' value='Update Information'>
    </form>
  </div>
  <div class='equal'>
    <h1>Past Work</h1>
    <form onsubmit='addVideo(this); return false'>
      <input type='hidden' name='func' value='addVideo'>
      <label>VIDEOS</label>
      <input type='text' placeholder='Video Title' spellcheck='false' autocomplete='off' maxlength='40' name='title'>
      <div class='row'>
        <input type='text' placeholder='Youtube Link' spellcheck='false' autocomplete='off' maxlength='100' name='youtubeLink'>
        <input type='text' placeholder='Vimeo Link' spellcheck='false' autocomplete='off' maxlength='100' name='vimeoLink'>
      </div>
      <input type='submit' value='Add Video'>
    </form>
    <h1>Account</h1>
    <form>
      <input type='button' value='Logout' onclick="location.pathname='/logout/'">
    </form>
  </div>
</div>

<script>
  function updateInfo(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      console.log(r)
      r = JSON.parse(r)
      if (r['status'] == 'ok') {

      }
      addAlert(r['message'])
    })
  }

  function addVideo(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      console.log(r)
      r = JSON.parse(r)
      if (r['status'] == 'ok') {

      }
      addAlert(r['message'])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

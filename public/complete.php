<?php include "../inc/header.php" ?>

<div id="center">
  <div>
    <h1>Almost There</h1>
    Please complete your profile to continue. <br><br>
    <form onsubmit='complete(this); return false' class='f1'>
      <div class='labels'>
        <div>Role</div>
        <div>Gender</div>
        <div>Date of Birth</div>
        <div>Fullname</div>
      </div>
      <input type='hidden' name='func' value='complete'>
      <div class="row">
        <input type="radio" name="role" value="0" checked>
        <input type="radio" name="role" value="1" id='r1'><label for='r1'>Casting Director</label>
        <input type="radio" name="role" value="2" id='r2'><label for='r2'>Talent</label>
      </div>
      <div class="row">
        <input type="radio" name="gender" value="0" checked>
        <input type="radio" name="gender" value="1" id='r3'><label for='r3'>Male</label>
        <input type="radio" name="gender" value="2" id='r4'><label for='r4'>Female</label>
      </div>
      <div class="row">
        <input type='text' placeholder='Month' name='month' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Day' name='day' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Year' name='year' spellcheck='false' autocomplete='off' maxlength='40'>
      </div>
      <div class="row">
        <input type='text' placeholder='Firstname' name='firstname' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Lastname' name='lastname' spellcheck='false' autocomplete='off' maxlength='40'>
      </div>
      <input type='submit' value='&rarr;'>
    </form>
  </div>
</div>

<script>
  function complete(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      r = JSON.parse(r)
      if (r["status"] == "ok") window.location = "/user/" + "<?php echo $MYACCOUNT['username'] ?>" + "/"
      addAlert(r["message"])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

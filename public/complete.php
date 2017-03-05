<?php include "../inc/header.php" ?>

<div id="center">
  <div>
    <h1>Almost There</h1>
    Please complete your profile to continue. <br><br>
    <form onsubmit='complete(this); return false' class='f1'>
      <input type='hidden' name='func' value='complete'>
      <div class="row">
        <input type='text' placeholder='Firstname' name='firstname' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Lastname' name='lastname' spellcheck='false' autocomplete='off' maxlength='40'>
      </div>
      <div class="row">
        <select name="role" style='flex-grow:.5'>
          <option value="0">Role</option>
          <option value="1">Casting Director</option>
          <option value="2">Talent</option>
        </select>
        <select name="gender" style='flex-grow:.5'>
          <option value="0">Gender</option>
          <option value="1">Male</option>
          <option value="2">Female</option>
        </select>
      </div>
      <div class="row">
        <input type='text' placeholder='Birth Month' name='month' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Birth Day' name='day' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Birth Year' name='year' spellcheck='false' autocomplete='off' maxlength='40'>
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

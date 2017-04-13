<?php include "../inc/header.php" ?>

<div id="center">
  <div>
    <h1>Complete Your Profile</h1>
    Please include some basic information to continue. <br>
    <div class='spacer'></div>
    <form onsubmit='complete(this); return false' class='f2 right'>
      <input type='hidden' name='func' value='complete'>
      <div class="row">
        <input type='text' placeholder='Firstname' name='firstname' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Lastname' name='lastname' spellcheck='false' autocomplete='off' maxlength='40'>
      </div>
      <div class="row">
        <input type='text' placeholder='Birth Month' name='month' spellcheck='false' autocomplete='off' maxlength='2'>
        <input type='text' placeholder='Birth Day' name='day' spellcheck='false' autocomplete='off' maxlength='2'>
        <input type='text' placeholder='Birth Year' name='year' spellcheck='false' autocomplete='off' maxlength='4'>
      </div>
      <div class="row">
        <select name="gender">
          <option value="0">Gender</option>
          <option value="1">Male</option>
          <option value="2">Female</option>
        </select>
        <select name="role">
          <option value="-1">Select Role</option>
          <option value="0">Actor</option>
          <option value="1">Director</option>
        </select>
      </div>
      <input type='submit' value='Complete My Profile'>
    </form>
  </div>
</div>

<script>
  function complete(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      r = JSON.parse(r)
      if (r["status"] == "ok") window.location = "/"
      addAlert(r["message"])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

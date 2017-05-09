<?php include "../inc/header.php" ?>

<div class="center">
  <div>
    <h1>Almost There</h1>
    <p>Complete your basic information to enter.</p>
    <form onsubmit='complete(this); return false'>
      <input type='hidden' name='func' value='complete'>
      <div class='label'>
        <p>Role</p>
        <select name="role">
          <option value="-1">Select</option>
          <option value="1">Director</option>
          <option value="0">Talent</option>
        </select><br>
      </div><br>
      <div class='label'>
        <p>Gender</p>
        <select name="gender">
          <option value="0">Select</option>
          <option value="1">Male</option>
          <option value="2">Female</option>
        </select>
      </div><br>
      <div class='label'>
        <p>Firstname</p>
        <input type='text' name='firstname' spellcheck='false' autocomplete='off' maxlength='40'>
      </div><br>
      <div class='label'>
        <p>Lastname</p>
        <input type='text' name='lastname' spellcheck='false' autocomplete='off' maxlength='40'>
      </div><br>
      <div class='label'>
        <p>Date of Birth</p>
        <input type='text' name='birthdate' spellcheck='false' autocomplete='off' maxlength='10' placeholder='mm/dd/YYYY' onkeyup="checkDate(event, this)">
      </div><br>
      <input type='submit' value='Complete My Profile'>
    </form>
  </div>
</div>

<script>
  function complete(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      try {
        r = JSON.parse(r)
      } catch (e) {
        console.log(r)
        return
      }
      if (r["status"] == "ok") window.location = "/"
      addAlert(r["message"])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

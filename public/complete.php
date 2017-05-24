<?php include "../inc/header.php" ?>

<div class="center">
  <div>
    <h1>Almost There</h1>
    <p>Complete your basic information to enter.</p>
    <form onsubmit="sendForm('complete', this); return false" style='width: 330px'>
      <div class='label'>
        <p>Role</p>
        <select name="role">
          <option value="-1">Select</option>
          <option value="1">Director</option>
          <option value="0">Talent</option>
        </select>
      </div>
      <div class='label'>
        <p>Gender</p>
        <select name="gender">
          <option value="0">Select</option>
          <option value="1">Male</option>
          <option value="2">Female</option>
        </select>
      </div>
      <div class='label'>
        <p>Firstname</p>
        <input type='text' name='firstname' spellcheck='false' autocomplete='off' maxlength='40'>
      </div>
      <div class='label'>
        <p>Lastname</p>
        <input type='text' name='lastname' spellcheck='false' autocomplete='off' maxlength='40'>
      </div>
      <div class='label'>
        <p>Date of Birth</p>
        <input type='text' name='birthdate' spellcheck='false' autocomplete='off' maxlength='10' placeholder='7/9/1994'>
      </div>
      <input type='submit' value='Complete My Profile'>
    </form>
  </div>
</div>

<?php include "../inc/footer.php" ?>

<?php include "../inc/header.php" ?>

<div class="center">
  <div>
    <h1>Authenticate</h1>
    <p>Please sign in with your Chapman ID to access the COPA casting portal.</p>
    <form onsubmit='sendForm("login", this); return false'>
      <div class='row'>
        <div class='label'>
          <input type='text' placeholder='Email' spellcheck='false' autocomplete='off' maxlength='40' name='email'>
        </div>
        <div class='label'>
          <input type='password' placeholder='Password' spellcheck='false' maxlength='40' name='password'><br>
        </div>
      </div>
      <input type='submit' value='Login'>
    </form>
  </div>
</div>

<?php include "../inc/footer.php" ?>

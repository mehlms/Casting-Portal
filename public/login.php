<?php include "../inc/header.php" ?>

<div class="center">
  <div>
    <h1>Authenticate</h1>
    <p>Please login with your Chapman ID to access the COPA casting portal.</p>
    <form onsubmit='login(this); return false'>
      <input type='hidden' name='func' value='login'>
      <input type='text' placeholder='Email' spellcheck='false' autocomplete='off' maxlength='40' name='email'><input type='password' placeholder='Password' spellcheck='false' maxlength='40' name='password'><br>
      <input type='submit' value='Login'>
    </form>
  </div>
</div>

<script>
  function login(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      console.log(r)
      r = JSON.parse(r)
      if (r["status"] == "ok") window.location = "/"
      addAlert(r["message"])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

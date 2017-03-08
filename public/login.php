<?php include "../inc/header.php" ?>

<div id="center">
  <div>
    <h1>Authenticate</h1>
    Please login with your Chapman ID to access the casting portal. <br>
    <div class='spacer'></div>
    <form onsubmit='login(this); return false' class='f1'>
      <input type='hidden' name='func' value='login'>
      <div class='row'>
        <input type='text' placeholder='Email' spellcheck='false' autocomplete='off' maxlength='40' name='email'>
        <input type='password' placeholder='Password' spellcheck='false' maxlength='40' name='password'>
      </div>
      <input type='submit' value='Login'>
    </form>
  </div>
</div>

<script>
  function login(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      r = JSON.parse(r)
      if (r["status"] == "ok") window.location = r["url"]
      addAlert(r["message"])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

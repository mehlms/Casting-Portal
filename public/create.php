<?php include "../inc/header.php"; ?>

<div id="center">
  <div>
    <h1>Create a Casting Call</h1>
    Actors will be recommended from your character descriptions. <br><br>
    <div class='labels'>
      <div>Name</div>
      <div>Gender</div>
      <div>Age Range</div>
      <div>Description</div>
    </div>
    <form onsubmit='create(this); return false' class='f1'>
      <input type='hidden' name='func' value='create'>
      <div class="row">
        <input type='text' placeholder='Firstname' name='firstname' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Lastname' name='lastname' spellcheck='false' autocomplete='off' maxlength='40'>
      </div>
      <div class="row">
        <input type="radio" name="gender" value="0" checked>
        <input type="radio" name="gender" value="1" id='r3'><label for='r3'>Male</label>
        <input type="radio" name="gender" value="2" id='r4'><label for='r4'>Female</label>
      </div>
      <div class="row">
        <input type='text' placeholder='Min' name='min' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Max' name='max' spellcheck='false' autocomplete='off' maxlength='40'>
      </div>
      <div class="row">
        <textarea rows='2' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Write a brief character bio.."></textarea>
        <!-- <input type='text' placeholder='Year' name='year' spellcheck='false' autocomplete='off' maxlength='40'> -->
      </div>
      <input type='submit' value='&rarr;'>
    </form>
  </div>
</div>

<script>
  function create(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      r = JSON.parse(r)
      addAlert(r["message"])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

<?php include "../inc/header.php"; ?>

<form onsubmit='create(this); return false' class='f1'>
  <input type='hidden' name='func' value='create'>
  <div class="row">
    <div>
      <label>Project Details</label>
      <select name="type">
        <option value="0">Project Type</option>
        <option value="23">Undergraduate Visual Storytelling (FTV 130)</option>
        <option value="1">Undergraduate Directing 2 (FP 338)</option>
        <option value="2">Undergraduate Directing 3 (FP 438)</option>
        <option value="3">Undergraduate Intermediate Production (FP 280)</option>
        <option value="4">Undergraduate Advanced Production (FP 331)</option>
        <option value="5">Undergraduate Senior Thesis (FP 497-498)</option>
        <option value="6">Undergraduate Byte-sized Television (TWP 313)</option>
        <option value="7">Undergraduate Television Pilots (TWP 398)</option>
        <option value="8">Undergraduate Digital Arts Project</option>
        <option value="9">Undergraduate Independent Study</option>
        <option value="10">Graduate Fundamentals of Directing 1 (FP 538)</option>
        <option value="11">Graduate Fundamentals of Directing 2 (FP 539)</option>
        <option value="12">Graduate Intermediate Directing (FP 664)</option>
        <option value="13">Graduate Advanced Directing (FP 665)</option>
        <option value="14">Graduate Master Class in Directing (FP 638)</option>
        <option value="15">Graduate Production Workshop 1 (FP 531)</option>
        <option value="16">Graduate Production Workshop 2 (FP 532)</option>
        <option value="17">Graduate Production Workshop 3 (FP 577)</option>
        <option value="18">Graduate Production Workshop 4 (FP 631)</option>
        <option value="19">Graduate Thesis (FP 698)</option>
        <option value="20">Graduate Filmmakers and Actors Workshop (FP 507)</option>
        <option value="21">Graduate Independent Study</option>
        <option value="22">Other</option>
      </select>
      <input type='text' placeholder='Project Title' name='firstname' spellcheck='false' autocomplete='off' maxlength='40'>
      <textarea rows='2' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Project Description.."></textarea>
      <div class="row">
        <input type='text' placeholder='Location' name='min' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Time' name='min' spellcheck='false' autocomplete='off' maxlength='40'>
      </div>
    </div>
  </div>

  <div>
    <label>Character 1</label>
    <div class="row">
      <input type='text' placeholder='Name' name='firstname' spellcheck='false' autocomplete='off' maxlength='40'>
      <select name="gender">
        <option value="0">Gender</option>
        <option value="1">Male</option>
        <option value="2">Female</option>
      </select>
      <input type='text' placeholder='Min Age' name='firstname' spellcheck='false' autocomplete='off' maxlength='40'>
      <input type='text' placeholder='Max Age' name='firstname' spellcheck='false' autocomplete='off' maxlength='40'>
    </div>
    <textarea rows='2' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Character Description.."></textarea>
  </div>
  <input type='submit' value='&rarr;'>
</form>

<script>
  function create(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      r = JSON.parse(r)
      addAlert(r["message"])
    })
  }
</script>

<?php include "../inc/footer.php" ?>

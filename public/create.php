<?php include "../inc/header.php"; ?>

<form onsubmit='create(this); return false'>
  <input type='hidden' name='func' value='create'>
  <div class='div_row'>
    <div class='equal'>
      <h1>Casting Call Details</h1>
      <label>PROJECT TITLE</label><input type='text' placeholder='Project Title' name='title' spellcheck='false' autocomplete='off' maxlength='40'>
      <label>TYPE</label><select>
        <option value="1">Undergraduate Visual Storytelling (FTV 130)</option>
        <option value="2">Undergraduate Directing 2 (FP 338)</option>
        <option value="3">Undergraduate Directing 3 (FP 438)</option>
        <option value="4">Undergraduate Intermediate Production (FP 280)</option>
        <option value="5">Undergraduate Advanced Production (FP 331)</option>
        <option value="6">Undergraduate Senior Thesis (FP 497-498)</option>
        <option value="7">Undergraduate Byte-sized Television (TWP 313)</option>
        <option value="8">Undergraduate Television Pilots (TWP 398)</option>
        <option value="9">Undergraduate Digital Arts Project</option>
        <option value="10">Undergraduate Independent Study</option>
        <option value="11">Graduate Fundamentals of Directing 1 (FP 538)</option>
        <option value="12">Graduate Fundamentals of Directing 2 (FP 539)</option>
        <option value="13">Graduate Intermediate Directing (FP 664)</option>
        <option value="14">Graduate Advanced Directing (FP 665)</option>
        <option value="15">Graduate Master Class in Directing (FP 638)</option>
        <option value="16">Graduate Production Workshop 1 (FP 531)</option>
        <option value="17">Graduate Production Workshop 2 (FP 532)</option>
        <option value="18">Graduate Production Workshop 3 (FP 577)</option>
        <option value="19">Graduate Production Workshop 4 (FP 631)</option>
        <option value="20">Graduate Thesis (FP 698)</option>
        <option value="21">Graduate Filmmakers and Actors Workshop (FP 507)</option>
        <option value="22">Graduate Independent Study</option>
        <option value="23">Other</option>
      </select>
      <label>WHEN</label><input type='text' placeholder='Audition Time' name='audition_time' spellcheck='false' autocomplete='off' maxlength='20'>
      <label>WHERE</label><input type='text' placeholder='Audition Location' name='audition_location' spellcheck='false' autocomplete='off' maxlength='40'>
      <label>STORYLINE</label><textarea rows='2' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Project Description..." name="description"></textarea>
    </div>
    <div class='equal'>
      <h1>Characters</h1>
      <label>CHARACTER DETAILS</label>
      <div class="row">
        <input type='text' placeholder='Character Name' name='name_c1' spellcheck='false' autocomplete='off' maxlength='40'>
        <input type='text' placeholder='Min Age' name='min_age_c1' spellcheck='false' autocomplete='off' maxlength='40' style='flex-grow:.5'>
        <input type='text' placeholder='Max Age' name='max_age_c1' spellcheck='false' autocomplete='off' maxlength='40' style='flex-grow:.5'>
        <select name="gender_c1" style='flex-grow:.5'>
          <option value="0">Gender</option>
          <option value="1">Male</option>
          <option value="2">Female</option>
          <option value="3">Either</option>
        </select>
      </div>
      <label>CHARACTER BIO</label><textarea rows='2' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Character Description.." name='description_c1'></textarea>
      <input type='button' value='Add Part'>
      <div id="characters"></div>
    </div>
  </div>
  <input type='submit' value='Post Casting Call'>
</form>

<script>
  var characters = {}

  function create(form) {
    post("/resources/ajax/functions.php", parse(form), function(r) {
      r = JSON.parse(r)
      console.log(r)
      if (r["status"] == "ok") window.location = r["url"]
      addAlert(r["message"])
    })
  }

  function addCharacter() {
    document.getElementById("characters").innerHTML = ""
  }
</script>

<?php include "../inc/footer.php" ?>

<?php include "../inc/header.php"; ?>

<form onsubmit='create(this); return false'>
  <input type='hidden' name='func' value='create'>
  <div class='div_row'>
    <div class='equal'>
      <h1>Casting Call Details</h1>
      <label>PROJECT TITLE</label><input type='text' placeholder='Ex: Black Mass, Clockstoppers, Blur' name='title' spellcheck='false' autocomplete='off' maxlength='40'>
      <label>TYPE</label><select name="type">
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
      <label>WHEN & WHERE</label>
      <textarea rows='2' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Ex: 4/9/17 1:30-2:30pm @ DH100" name="audition_time"></textarea>
      <input type='hidden' value="null" name="audition_location">
      <label>STORYLINE</label><textarea rows='4' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Ex: 'Black Mass' tells the story of James 'Whitney' Bulger, an Irish street punk who rose to power in organized crime by using his FBI informant status to bring down the Italian mafia in New England." name="description"></textarea>
    </div>
    <div class='equal'>
      <h1>Characters</h1>
      <label>CHARACTER INPUT</label>
      <div class="row">
        <input type='text' placeholder='Character Name' spellcheck='false' autocomplete='off' maxlength='40' id="char_name">
        <input type='text' placeholder='Min Age' spellcheck='false' autocomplete='off' maxlength='40' style='flex-grow:.5' id="char_min">
        <input type='text' placeholder='Max Age' spellcheck='false' autocomplete='off' maxlength='40' style='flex-grow:.5' id="char_max">
        <select style='flex-grow:.5' id="char_gender">
          <option value="0">Gender</option>
          <option value="1">Male</option>
          <option value="2">Female</option>
          <option value="3">Either</option>
        </select>
      </div>
      <textarea rows='2' spellcheck='false' autocomplete='off' maxlength='1000' placeholder="Character Description.." id="char_description"></textarea>
      <input type='button' value='Add Character' onclick="addPart()">
      <label>ADDED CHARACTERS</label>
      <div id="parts"></div>
    </div>
  </div>
  <input type='submit' value='Post Casting Call'>
</form>

<script>
  var parts = []

  refresh()

  function create(form) {
    var formData = parse(form)
    formData["parts"] = JSON.stringify(parts)
    post("/resources/ajax/functions.php", formData, function(r) {
      console.log(r)
      r = JSON.parse(r)
      if (r["status"] == "ok") window.location = r["url"]
      addAlert(r["message"])
    })
  }

  function removePart(ind) {
    parts.splice(ind, 1)
    refresh()
  }

  function addPart() {
    var charName = document.getElementById("char_name")
    var charMin = document.getElementById("char_min")
    var charMax = document.getElementById("char_max")
    var charGender = document.getElementById("char_gender")
    var charDescription = document.getElementById("char_description")

    if (charName.value != "" && charMin.value != "" && charMax.value != "" && charGender.value != "" && charDescription.value != "") {
      parts.push({"char_name": charName.value, "char_min": charMin.value, "char_max": charMax.value, "char_gender": charGender.value, "char_description": charDescription.value})
      changeValue("char_name", "")
      changeValue("char_min", "")
      changeValue("char_max", "")
      changeValue("char_gender", 0)
      changeValue("char_description", "")
      refresh()
    } else {
      addAlert("Please fill in all character fields.")
    }
  }

  function refresh() {
    document.getElementById("parts").innerHTML = ""

    if (parts.length > 0) {
      for (var i = 0; i < parts.length; i++) {
        document.getElementById("parts").innerHTML += "<div class='part' onclick='selectPart("+i+")'>" + parts[i]["char_name"] + "</div> <a onclick='removePart("+i+")'>remove</a><br>"
      }
    } else {
      document.getElementById("parts").innerHTML = "No Characters Added"
    }
  }

  function selectPart(id) {
    changeValue("char_name", parts[id]["char_name"])
    changeValue("char_min", parts[id]["char_min"])
    changeValue("char_max", parts[id]["char_max"])
    changeValue("char_gender", parts[id]["char_gender"])
    changeValue("char_description", parts[id]["char_description"])
  }

  function changeValue(id, value) {
    document.getElementById(id).value = value
    document.getElementById(id).click()
  }
</script>

<?php include "../inc/footer.php" ?>

<form action="/test/" method="POST">
  <input type='text' name="name[0][]" value="Matthew">
  <input type='text' name="name[0][]" value="Helms">
  <input type='text' name="name[1][]" value="Matthew">
  <input type='text' name="name[1][]" value="Helms">
  <input type='submit'>
</form>

<?php
foreach ($_POST['name'] as $i) {
  foreach ($i as $j) {
    echo $j;
  }
  echo "<br>";
}
?>

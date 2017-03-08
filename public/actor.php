<?php include "../inc/header.php";

$id = $_GET['data'];
$USERACCOUNT = $db->query("SELECT * FROM accounts WHERE a_id='$id'")->fetch();
?>

<div class="profile_header">
  <div class="profile_picture"></div>
  <div class="equal">
    <input type='button' value='+ SUBSCRIBE TO ACTOR' class='subscribe'>
    <h1 class='underline'><?php echo $USERACCOUNT['firstname']." ".$USERACCOUNT['lastname'] ?></h1>
    <h2><?php echo $USERACCOUNT['email'] ?></h2>
    <h2>Age <?php echo date_diff(date_create($USERACCOUNT['birthdate']), date_create('now'))->y ?>, <?php if ($USERACCOUNT["gender"] == 1) echo "Male"; else if ($USERACCOUNT["gender"] == 2) echo "Female" ?></h2>
  </div>
</div>
<?php include "../inc/footer.php" ?>

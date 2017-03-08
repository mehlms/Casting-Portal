<?php include "../inc/header.php";

$id = $_GET['data'];
$USERACCOUNT = $db->query("SELECT * FROM accounts WHERE d_id='$id'")->fetch();
?>

<div class="profile_picture"></div>
<h1>Director</h1>
Name: <b><?php echo $USERACCOUNT['firstname']." ".$USERACCOUNT['lastname'] ?></b><br>
Age: <b><?php echo date_diff(date_create($USERACCOUNT['birthdate']), date_create('now'))->y ?></b><br>
Gender: <b><?php if ($USERACCOUNT["gender"] == 1) echo "Male"; else if ($USERACCOUNT["gender"] == 2) echo "Female" ?></b><br>
Contact: <b><?php echo $USERACCOUNT['email'] ?></b><br>

<?php include "../inc/footer.php" ?>

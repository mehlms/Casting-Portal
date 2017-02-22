<?php include "../inc/header.php";

$username = $_GET['data'];
$USERACCOUNT = $db->query("SELECT * FROM accounts WHERE username='$username'")->fetch();
?>

<div class="profile_picture"></div>
Name: <b><?php echo $USERACCOUNT['firstname']." ".$USERACCOUNT['lastname'] ?></b> <br>
Age: <b><?php echo date_diff(date_create($USERACCOUNT['dob']), date_create('now'))->y ?></b> <br>
Gender: <b><?php if ($USERACCOUNT["gender"] == 1) echo "Male"; else if ($USERACCOUNT["gender"] == 2) echo "Female" ?></b> <br>
Role: <b><?php if ($USERACCOUNT["role"] == 1) echo "Casting Director"; else if ($USERACCOUNT["role"] == 2) echo "Actor" ?></b> <br>
Contact: <b><?php echo $USERACCOUNT['email'] ?></b> <br>

<?php include "../inc/footer.php" ?>

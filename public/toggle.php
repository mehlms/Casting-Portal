<?php include "../inc/db.php";

if ($MYACCOUNT) {
  $token = $MYACCOUNT['token'];
  $mode = $MYACCOUNT['mode'] ? 0 : 1;
  echo $mode;
  $db->query("UPDATE accounts SET mode=$mode WHERE token='$token'");
  header("Location: /");
}

?>

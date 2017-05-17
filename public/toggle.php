<?php include "../inc/db.php";

if ($MYACCOUNT) {
  $mode = $MYACCOUNT['mode'] ? 0 : 1;
  $db->query("UPDATE accounts SET mode=$mode WHERE token='".$MYACCOUNT['token']."'");
  header("Location: /");
}

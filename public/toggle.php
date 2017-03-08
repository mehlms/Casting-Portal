<?php include "../inc/db.php";

if ($MYACCOUNT && isset($_GET['data'])) {
  $token = $MYACCOUNT['token'];
  $mode = intval($_GET['data']);
  $db->query("UPDATE accounts SET mode=$mode WHERE token='$token'");

  if ($mode) header("Location: /director/".$MYACCOUNT['d_id']."/");
  else header("Location: /actor/".$MYACCOUNT['a_id']."/");
}

?>

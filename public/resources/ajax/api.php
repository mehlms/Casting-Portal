<?php include "../../../inc/db.php";

$email = isset($_POST["email"]) ? $_POST["email"] : null;
$password = isset($_POST["password"]) ? $_POST["password"] : null;

if ($email && $password && $db->query("SELECT COUNT(*) FROM api WHERE email='$email' and password='$password'")->fetch()["COUNT(*)"] == 1) {
  echo json_encode(array("status" => "ok"));
} else {
  echo json_encode(array("status" => "failed"));
}

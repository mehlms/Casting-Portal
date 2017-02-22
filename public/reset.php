<?php
include "../inc/db.php";

$db->query("DROP TABLE IF EXISTS api");
$db->query("CREATE TABLE api (id INT PRIMARY KEY AUTO_INCREMENT, email VARCHAR(40), password VARCHAR(40))");
$db->query("INSERT INTO api VALUES (null, 'helms107@mail.chapman.edu', 'Cocokai1')");

$db->query("DROP TABLE IF EXISTS accounts");
$db->query("CREATE TABLE accounts (id INT PRIMARY KEY AUTO_INCREMENT, token VARCHAR(40), username VARCHAR(40), email VARCHAR(40), firstname VARCHAR(40), lastname VARCHAR(40), role INT, gender INT, dob DATETIME, created DATETIME, last_access DATETIME)");

// table pictures (id, accountid, url, type) type determines profile picture or additional picture
// table actors 

?>

Tables Reset

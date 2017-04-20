<?php
include "../inc/db.php";

$db->query("DROP TABLE IF EXISTS api");
$db->query("DROP TABLE IF EXISTS accounts");
$db->query("DROP TABLE IF EXISTS actors");
$db->query("DROP TABLE IF EXISTS directors");
$db->query("DROP TABLE IF EXISTS calls");
$db->query("DROP TABLE IF EXISTS characters");
$db->query("DROP TABLE IF EXISTS assets");
$db->query("DROP TABLE IF EXISTS notifications");

$db->query("CREATE TABLE api (id INT PRIMARY KEY AUTO_INCREMENT, email VARCHAR(40), password VARCHAR(40))");
$db->query("CREATE TABLE accounts (id INT PRIMARY KEY AUTO_INCREMENT, a_id BIGINT, d_id BIGINT, mode BOOLEAN, token VARCHAR(40), email VARCHAR(80), firstname VARCHAR(50), lastname VARCHAR(50), gender INT, birthdate DATETIME, a_notifications INT, d_notifications INT, availability BOOLEAN, created_date DATETIME, last_access DATETIME)");
$db->query("CREATE TABLE calls (id BIGINT PRIMARY KEY, d_id BIGINT, title VARCHAR(200), type INT, description TEXT, audition_dates VARCHAR(100), shooting_dates VARCHAR(100), created_date DATETIME)");
$db->query("CREATE TABLE characters (id INT PRIMARY KEY AUTO_INCREMENT, call_id BIGINT, name VARCHAR(50), description TEXT, min_age INT, max_age INT, gender INT)");
$db->query("CREATE TABLE assets (id INT PRIMARY KEY AUTO_INCREMENT, page_id BIGINT, url VARCHAR(100), type INT, sort INT)");
$db->query("CREATE TABLE notifications (id INT PRIMARY KEY AUTO_INCREMENT, type INT, a_id BIGINT, d_id BIGINT, char_id INT, created DATETIME)");

// $db->query("INSERT INTO api VALUES (null, 'helms107@mail.chapman.edu', '1234')");
// $db->query("INSERT INTO api VALUES (null, 'hewit110@mail.chapman.edu', '1234')");
// $db->query("INSERT INTO api VALUES (null, 'hutzl102@mail.chapman.edu', '1234')");
$db->query("INSERT INTO api VALUES (null, 'guest@mail.chapman.edu', '1234')");
$db->query("INSERT INTO api VALUES (null, 'guest2@mail.chapman.edu', '1234')");
$db->query("INSERT INTO api VALUES (null, 'guest3@mail.chapman.edu', '1234')");
$db->query("INSERT INTO api VALUES (null, 'guest4@mail.chapman.edu', '1234')");

?>

Tables Reset

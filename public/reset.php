<?php
include "../inc/db.php";

$db->query("DROP TABLE IF EXISTS api");
$db->query("DROP TABLE IF EXISTS accounts");
$db->query("DROP TABLE IF EXISTS calls");
$db->query("DROP TABLE IF EXISTS characters");
$db->query("DROP TABLE IF EXISTS assets");
$db->query("DROP TABLE IF EXISTS interested");
$db->query("DROP TABLE IF EXISTS auditions");
$db->query("DROP TABLE IF EXISTS shootings");

$db->query("CREATE TABLE api (id INT PRIMARY KEY AUTO_INCREMENT, email VARCHAR(40), password VARCHAR(40))");
$db->query("CREATE TABLE accounts (id INT PRIMARY KEY AUTO_INCREMENT, a_id BIGINT, d_id BIGINT, a_bio VARCHAR(1000), d_bio VARCHAR(1000), mode BOOLEAN, token VARCHAR(40), email VARCHAR(80), firstname VARCHAR(50), lastname VARCHAR(50), gender INT, birthdate DATETIME, a_notifications INT, d_notifications INT, added DATETIME, last_access DATETIME)");
$db->query("CREATE TABLE calls (id BIGINT PRIMARY KEY, d_id BIGINT, title VARCHAR(200), type INT, description TEXT, audition_dates VARCHAR(100), shooting_dates VARCHAR(100), added DATETIME)");
$db->query("CREATE TABLE characters (id INT PRIMARY KEY AUTO_INCREMENT, call_id BIGINT, name VARCHAR(50), description TEXT, min_age INT, max_age INT, gender INT)");
$db->query("CREATE TABLE assets (id INT PRIMARY KEY AUTO_INCREMENT, page_id BIGINT, title VARCHAR(100), url VARCHAR(100), type INT, added DATETIME)");
$db->query("CREATE TABLE interested (id INT PRIMARY KEY AUTO_INCREMENT, a_id BIGINT, call_id BIGINT, char_id INT, added DATETIME)");
$db->query("CREATE TABLE auditions (id INT PRIMARY KEY AUTO_INCREMENT, call_id BIGINT, audition_time DATETIME, audition_place VARCHAR(40))");
$db->query("CREATE TABLE shootings (id INT PRIMARY KEY AUTO_INCREMENT, call_id BIGINT, shooting_from DATETIME, shooting_to DATETIME)");

$db->query("INSERT INTO api VALUES (null, 'helms107@mail.chapman.edu', 'Cocokai1')");
$db->query("INSERT INTO api VALUES (null, 'guest@mail.chapman.edu', '1234')");
$db->query("INSERT INTO api VALUES (null, 'guest2@mail.chapman.edu', '1234')");
$db->query("INSERT INTO api VALUES (null, 'guest3@mail.chapman.edu', '1234')");
$db->query("INSERT INTO api VALUES (null, 'guest4@mail.chapman.edu', '1234')");

?>

Tables Reset

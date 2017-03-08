<?php
include "../inc/db.php";

$db->query("DROP TABLE IF EXISTS api");
$db->query("DROP TABLE IF EXISTS accounts");
$db->query("DROP TABLE IF EXISTS actors");
$db->query("DROP TABLE IF EXISTS directors");
$db->query("DROP TABLE IF EXISTS calls");
$db->query("DROP TABLE IF EXISTS characters");
$db->query("DROP TABLE IF EXISTS assets");

$db->query("CREATE TABLE api (id INT PRIMARY KEY AUTO_INCREMENT, email VARCHAR(40), password VARCHAR(40))");
$db->query("CREATE TABLE accounts (id INT PRIMARY KEY AUTO_INCREMENT, a_id BIGINT, d_id BIGINT, mode BOOLEAN, token VARCHAR(40), email VARCHAR(80), firstname VARCHAR(50), lastname VARCHAR(50), gender INT, birthdate DATETIME, a_notifications INT, d_notifications INT, availability BOOLEAN, created_date DATETIME, last_access DATETIME)");
// $db->query("CREATE TABLE actors (id BIGINT PRIMARY KEY, account_id INT, notifications INT, availability BIT, tags BINARY(64))");
// $db->query("CREATE TABLE directors (id BIGINT PRIMARY KEY, account_id INT, notifications INT)");
$db->query("CREATE TABLE calls (id BIGINT PRIMARY KEY, director_id INT, title VARCHAR(100), type INT, description VARCHAR(1000), audition_location VARCHAR(100), audition_time VARCHAR(100), created_date DATETIME)");
$db->query("CREATE TABLE characters (id INT PRIMARY KEY AUTO_INCREMENT, call_id BIGINT, name VARCHAR(50), description VARCHAR(1000), min_age INT, max_age INT, gender INT)");
$db->query("CREATE TABLE assets (id INT PRIMARY KEY AUTO_INCREMENT, page_id BIGINT, url VARCHAR(100), type INT, sort INT)");

$db->query("INSERT INTO api VALUES (null, 'helms107@mail.chapman.edu', '1234')");

?>

Tables Reset

<?php
include "../inc/db.php";

// $db->query("CREATE TABLE calls (id PRIMARY KEY INT, director_id INT, description varchar(1000), start_shooting_date datetime, location varchar(100), title varchar(100), audition_date datetime, project_type INT, estimated_length datetime, end_shooting_date datetime)");

$db->query("DROP TABLE IF EXISTS api");
$db->query("DROP TABLE IF EXISTS accounts");
$db->query("DROP TABLE IF EXISTS calls");
$db->query("DROP TABLE IF EXISTS characters");
$db->query("CREATE TABLE api (id INT PRIMARY KEY AUTO_INCREMENT, email VARCHAR(40), password VARCHAR(40))");
$db->query("CREATE TABLE accounts (id INT PRIMARY KEY AUTO_INCREMENT, token VARCHAR(40), username VARCHAR(40), email VARCHAR(40), firstname VARCHAR(40), lastname VARCHAR(40), role INT, gender INT, dob DATETIME, created DATETIME, last_access DATETIME)");
$db->query("CREATE TABLE calls (id BIGINT PRIMARY KEY, director_id INT, title VARCHAR(100), type INT, description VARCHAR(1000), audition_location VARCHAR(100), audition_time VARCHAR(100), created DATETIME)");
$db->query("CREATE TABLE characters (id INT PRIMARY KEY AUTO_INCREMENT, call_id BIGINT, name VARCHAR(50), description VARCHAR(1000), min_age INT, max_age INT, gender INT)");

$db->query("INSERT INTO api VALUES (null, 'helms107@mail.chapman.edu', 'Cocokai1')");

?>

Tables Reset

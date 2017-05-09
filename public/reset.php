<?php
include "../inc/db.php";

$db->beginTransaction();
$db->query("DROP TABLE IF EXISTS api");
$db->query("DROP TABLE IF EXISTS accounts");
$db->query("DROP TABLE IF EXISTS calls");
$db->query("DROP TABLE IF EXISTS characters");
$db->query("DROP TABLE IF EXISTS assets");
$db->query("DROP TABLE IF EXISTS interested");
$db->query("DROP TABLE IF EXISTS auditions");
$db->query("DROP TABLE IF EXISTS shootings");
$db->query("DROP TABLE IF EXISTS collaborators");
$db->query("DROP TABLE IF EXISTS classes");
$db->query("DROP TABLE IF EXISTS praise");
$db->query("DROP TABLE IF EXISTS follow");

$db->query("CREATE TABLE api (id INT PRIMARY KEY AUTO_INCREMENT, email VARCHAR(40), password VARCHAR(40))");
$db->query("CREATE TABLE accounts (id INT PRIMARY KEY AUTO_INCREMENT, a_id BIGINT, d_id BIGINT, a_bio VARCHAR(1000), d_bio VARCHAR(1000), mode BOOLEAN, token VARCHAR(40), email VARCHAR(80), firstname VARCHAR(50), lastname VARCHAR(50), gender INT, birthdate DATETIME, a_notifications INT, d_notifications INT, added DATETIME, last_access DATETIME)");
$db->query("CREATE TABLE calls (id BIGINT PRIMARY KEY, title VARCHAR(200), type INT, storyline TEXT, added DATETIME)");
$db->query("CREATE TABLE auditions (id INT PRIMARY KEY AUTO_INCREMENT, call_id BIGINT, audition_time DATETIME, audition_place VARCHAR(40))");
$db->query("CREATE TABLE shootings (id INT PRIMARY KEY AUTO_INCREMENT, call_id BIGINT, shooting_from DATETIME, shooting_to DATETIME)");
$db->query("CREATE TABLE characters (id INT PRIMARY KEY AUTO_INCREMENT, call_id BIGINT, name VARCHAR(50), min INT, max INT, gender INT, description TEXT)");
$db->query("CREATE TABLE assets (id INT PRIMARY KEY AUTO_INCREMENT, page_id BIGINT, title VARCHAR(100), url VARCHAR(100), type INT, added DATETIME)");
$db->query("CREATE TABLE interested (id INT PRIMARY KEY AUTO_INCREMENT, a_id BIGINT, char_id INT, added DATETIME)");
$db->query("CREATE TABLE collaborators (id INT PRIMARY KEY AUTO_INCREMENT, call_id BIGINT, d_id BIGINT, added DATETIME)");
$db->query("CREATE TABLE classes (id INT PRIMARY KEY AUTO_INCREMENT, class VARCHAR(100))");
$db->query("CREATE TABLE praise (id INT PRIMARY KEY AUTO_INCREMENT, praise_from BIGINT, praise_to BIGINT, heart BOOLEAN, comment VARCHAR(140), added DATETIME)");
$db->query("CREATE TABLE follow (id INT PRIMARY KEY AUTO_INCREMENT, follow_from BIGINT, follow_to BIGINT, added DATETIME)");

$db->query("INSERT INTO api VALUES".
"(null, 'helms107@mail.chapman.edu', '1234'),".
"(null, 'guest@mail.chapman.edu', '1234'),".
"(null, 'guest2@mail.chapman.edu', '1234'),".
"(null, 'guest3@mail.chapman.edu', '1234'),".
"(null, 'guest4@mail.chapman.edu', '1234')");

$db->query("INSERT INTO classes VALUES".
"(null, 'Undergraduate Visual Storytelling (FTV 130)'),".
"(null, 'Undergraduate Directing 2 (FP 338)'),".
"(null, 'Undergraduate Directing 3 (FP 438)'),".
"(null, 'Undergraduate Intermediate Production (FP 280)'),".
"(null, 'Undergraduate Advanced Production (FP 331)'),".
"(null, 'Undergraduate Senior Thesis (FP 497-498)'),".
"(null, 'Undergraduate Byte-sized Television (TWP 313)'),".
"(null, 'Undergraduate Television Pilots (TWP 398)'),".
"(null, 'Undergraduate Digital Arts Project'),".
"(null, 'Undergraduate Independent Study'),".
"(null, 'Graduate Fundamentals of Directing 1 (FP 538)'),".
"(null, 'Graduate Fundamentals of Directing 2 (FP 539)'),".
"(null, 'Graduate Intermediate Directing (FP 664)'),".
"(null, 'Graduate Advanced Directing (FP 665)'),".
"(null, 'Graduate Master Class in Directing (FP 638)'),".
"(null, 'Graduate Production Workshop 1 (FP 531)'),".
"(null, 'Graduate Production Workshop 2 (FP 532)'),".
"(null, 'Graduate Production Workshop 3 (FP 577)'),".
"(null, 'Graduate Production Workshop 4 (FP 631)'),".
"(null, 'Graduate Thesis (FP 698)'),".
"(null, 'Graduate Filmmakers and Actors Workshop (FP 507)'),".
"(null, 'Graduate Independent Study'),".
"(null, 'Other')");
$db->commit();

?>

Tables Reset

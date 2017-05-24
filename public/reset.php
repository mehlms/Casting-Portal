<?php
include "../inc/db.php";
$data = isset($_GET['data']) ? $_GET['data'] : null;

if ($data === "abcdefghijklmnop123") {
  if (isset($_POST['reset'])) {
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
    $db->query("DROP TABLE IF EXISTS genres");
    $db->query("DROP TABLE IF EXISTS classesFilter");
    $db->query("DROP TABLE IF EXISTS genresFilter");

    $db->query("CREATE TABLE api (id INT PRIMARY KEY AUTO_INCREMENT, email VARCHAR(50), password VARCHAR(40))");
    $db->query("CREATE TABLE accounts (id INT PRIMARY KEY AUTO_INCREMENT, a_id INT, d_id INT, a_bio TEXT, d_bio TEXT, mode BOOLEAN, token VARCHAR(40), email VARCHAR(50), firstname VARCHAR(30), lastname VARCHAR(30), gender INT, birthdate DATETIME, looks_min INT, looks_max INT, added DATETIME, last_access DATETIME)");
    $db->query("CREATE TABLE calls (id INT PRIMARY KEY, title VARCHAR(200), type INT, genre INT, genre2 INT, storyline TEXT, added DATETIME)");
    $db->query("CREATE TABLE auditions (id INT PRIMARY KEY AUTO_INCREMENT, call_id INT, audition_time DATETIME, audition_place VARCHAR(50))");
    $db->query("CREATE TABLE shootings (id INT PRIMARY KEY AUTO_INCREMENT, call_id INT, shooting_from DATETIME, shooting_to DATETIME)");
    $db->query("CREATE TABLE characters (id INT PRIMARY KEY AUTO_INCREMENT, call_id INT, name VARCHAR(30), min INT, max INT, gender INT, description TEXT)");
    $db->query("CREATE TABLE assets (id INT PRIMARY KEY AUTO_INCREMENT, page_id INT, title VARCHAR(100), url VARCHAR(100), type INT, added DATETIME)");
    $db->query("CREATE TABLE interested (id INT PRIMARY KEY AUTO_INCREMENT, a_id INT, char_id INT, added DATETIME)");
    $db->query("CREATE TABLE collaborators (id INT PRIMARY KEY AUTO_INCREMENT, call_id INT, d_id INT, added DATETIME)");
    $db->query("CREATE TABLE praise (id INT PRIMARY KEY AUTO_INCREMENT, praise_from INT, praise_to INT, heart BOOLEAN, comment VARCHAR(140), added DATETIME)");
    $db->query("CREATE TABLE follow (id INT PRIMARY KEY AUTO_INCREMENT, follow_from INT, follow_to INT, added DATETIME)");
    $db->query("CREATE TABLE classes (id INT PRIMARY KEY AUTO_INCREMENT, class VARCHAR(100))");
    $db->query("CREATE TABLE classesFilter (id INT PRIMARY KEY AUTO_INCREMENT, a_id INT, class_id INT)");
    $db->query("CREATE TABLE genres (id INT PRIMARY KEY AUTO_INCREMENT, genre VARCHAR(30))");
    $db->query("CREATE TABLE genresFilter (id INT PRIMARY KEY AUTO_INCREMENT, a_id INT, genre_id INT)");

    $db->query("INSERT INTO api VALUES".
    "(null, 'helms107@mail.chapman.edu', '1234'),".
    "(null, 'benda106@mail.chapman.edu', '1234'),".
    "(null, 'guest100@mail.chapman.edu', '1234'),".
    "(null, 'guest101@mail.chapman.edu', '1234'),".
    "(null, 'guest102@mail.chapman.edu', '1234'),".
    "(null, 'guest103@mail.chapman.edu', '1234'),".
    "(null, 'guest104@mail.chapman.edu', '1234')");

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

    $db->query("INSERT INTO genres VALUES".
    "(null, 'Action'),".
    "(null, 'Adult'),".
    "(null, 'Adventure'),".
    "(null, 'Animation'),".
    "(null, 'Comedy'),".
    "(null, 'Documentary'),".
    "(null, 'Drama'),".
    "(null, 'Family'),".
    "(null, 'Fantasy'),".
    "(null, 'Historical'),".
    "(null, 'Horror'),".
    "(null, 'Musical'),".
    "(null, 'Romance'),".
    "(null, 'Sci-Fi'),".
    "(null, 'Sports'),".
    "(null, 'Thriller'),".
    "(null, 'War'),".
    "(null, 'Western')");
    $db->commit();
    echo "Database Reset";
  } else {
    echo "<form action='/reset/abcdefghijklmnop123/' method='post'>
            <input type='submit' name='reset' value='Click to Reset Database'>
          </form>";
  }
} else {
  header("Location: /reset1/");
}

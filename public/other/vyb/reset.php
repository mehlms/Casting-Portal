<?php
$db_opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$db = new PDO('mysql:host=localhost;dbname=vyb;charset=utf8', "root", "", $db_opt);

$db->query("DROP TABLE IF EXISTS accounts");
$db->query("DROP TABLE IF EXISTS streams");
$db->query("DROP TABLE IF EXISTS listeners");

$db->query("CREATE TABLE accounts (id INT PRIMARY KEY AUTO_INCREMENT, spotify_id VARCHAR(50), soundcloud_id VARCHAR(50), display_name VARCHAR(50), lat DECIMAL(10, 8), lng DECIMAL(11, 8), token VARCHAR(40), joined DATETIME)");
$db->query("CREATE TABLE streams (id INT PRIMARY KEY AUTO_INCREMENT, streamer_id INT, stream_name VARCHAR(40), song VARCHAR(40), artist VARCHAR(40), cover_url VARCHAR(120), uri VARCHAR(50), uri_next VARCHAR(50), start DATETIME)");
$db->query("CREATE TABLE listeners (id INT PRIMARY KEY AUTO_INCREMENT, streamer_id INT, listener_id INT)");

$db->query("INSERT INTO accounts VALUES (null, 'Melms', null, 'Matthew Helms', 33.8, -117.85, 'ASDHJKJASDHAHDSAJHKDHJ', NOW())");
$db->query("INSERT INTO streams VALUES (null, 1, 'Coldplay Mix', 'Hypnotised', 'Coldplay', 'https://i.scdn.co/image/7310dc4e9f9a452fe606861c5c1a7f8a1c5ff3bf', 'spotify:track:5LXj9Ch3O9ATm1NoHT8GXn', 'spotify:track:0Fd5QbUWNalQ7P2mfyGkfe', NOW())");
// $db->query("INSERT INTO accounts VALUES (null, 'Grance', null, 'Grant Nguyen', 34.0, -118.0, 'YUIQWUDHSJAKDHAJKS', NOW())");
// $db->query("INSERT INTO streams VALUES (null, 2, 'Dance Party', '123qjwkspotify', '', '', '', NOW())");

?>

Tables Reset

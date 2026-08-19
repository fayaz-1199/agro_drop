<?php

/* Change these values if your MySQL credentials are different. */
$host = '192.168.11.105';
$dbname = 'agro_drop';
$username = 'user1';
$password = 'tmssict123';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    exit('Database connection failed. Check config/database.php and import agro_drop.sql.');
}

mysqli_set_charset($conn, 'utf8mb4');

/* Used by the existing customer edit/create pages. */
$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $username,
    $password,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

<?php

$host = "localhost";
$dbname = "agro_drop";
$username = "root";
$password = "root";

try {

    $conn = mysqli_connect($host, $username, $password, $dbname);

    if (!$conn) {
        die("Database Connection Failed: " . mysqli_connect_error());
    }

// Charset Set
    mysqli_set_charset($conn, "utf8mb4");

} catch (PDOException $e) {

    die("Database Connection Failed: " . $e->getMessage());

}
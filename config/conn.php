<?php
$hostname   = "localhost";
$username   = "root";
$password   = "";
$database   = "inventory";

try {
    $con = mysqli_connect($hostname, $username, $password, $database);

    if (!$con) {
        throw new Exception("Koneksi database gagal: " . mysqli_connect_error());
    }

    // Set charset ke UTF-8
    mysqli_set_charset($con, "utf8");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
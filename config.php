<?php
$host = "localhost";
$username = "root"; // default XAMPP username
$password = "";     // default XAMPP password
$dbname = "healthybites";

// Buat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
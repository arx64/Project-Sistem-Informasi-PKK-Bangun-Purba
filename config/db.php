<?php
$host = "localhost";
$user = "root"; // ganti sesuai MySQL
$pass = "";     // ganti sesuai MySQL
$db   = "db_pkk_bpurba";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>
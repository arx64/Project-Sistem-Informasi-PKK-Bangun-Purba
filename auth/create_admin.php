<?php
include '../config/db.php';

$username = "pkk";
$password = password_hash("pkk123", PASSWORD_DEFAULT);
$role     = "pkk";

$stmt = $conn->prepare("INSERT INTO pengguna (username, password, role) VALUES (?,?,?)");
$stmt->bind_param("sss", $username, $password, $role);
if ($stmt->execute()) {
    echo "User admin berhasil dibuat!";
} else {
    echo "Gagal membuat user admin: " . $stmt->error;
}
$stmt->close();
?>
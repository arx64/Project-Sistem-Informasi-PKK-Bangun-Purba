<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php'; // Sertakan koneksi database

// Periksa apakah parameter 'table' ada dan tidak kosong
if (!isset($_GET['table']) || empty($_GET['table'])) {
    die("Error: Nama tabel tidak disediakan.");
}

$tableName = $_GET['table'];

// --- Keamanan: Validasi Nama Tabel ---
// Untuk mencegah SQL Injection, pastikan tabel yang diminta benar-benar ada di database.
$allowedTables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $allowedTables[] = $row[0];
}

if (!in_array($tableName, $allowedTables)) {
    die("Error: Nama tabel tidak valid atau tidak ditemukan.");
}
// --- Akhir Validasi Keamanan ---

// Set header HTTP untuk memicu download file
$fileName = $tableName . '_export_' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Buka stream output PHP
$output = fopen('php://output', 'w');

// Ambil nama kolom dan tulis sebagai baris header di CSV
$query = $conn->query("SELECT * FROM `" . $tableName . "` LIMIT 1");
if ($row = $query->fetch_assoc()) {
    fputcsv($output, array_keys($row));
}

// Ambil semua data dari tabel dan tulis baris demi baris
$result = $conn->query("SELECT * FROM `" . $tableName . "`");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
$conn->close();
exit;

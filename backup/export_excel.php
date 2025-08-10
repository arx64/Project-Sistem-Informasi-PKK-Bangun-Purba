<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

if (!isset($_GET['table'])) {
    die("Tabel tidak dipilih.");
}

$table = $_GET['table'];

// Ambil data dari tabel
$query = $conn->query("SELECT * FROM `$table`");

if (!$query) {
    die("Tabel tidak ditemukan: " . $conn->error);
}

// Header untuk download file Excel (CSV)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename={$table}_" . date("Y-m-d") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil nama kolom
$fields = $query->fetch_fields();
foreach ($fields as $field) {
    echo $field->name . "\t";
}
echo "\n";

// Ambil data tiap baris
while ($row = $query->fetch_assoc()) {
    echo implode("\t", $row) . "\n";
}
exit;

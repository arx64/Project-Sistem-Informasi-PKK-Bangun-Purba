<?php
ob_start(); // buffer output agar tidak bentrok dengan header()
session_start();
// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php'; // Hanya untuk mendapatkan nama database jika perlu

// --- Dapatkan Kredensial Database ---
// Cara terbaik adalah mendefinisikan variabel ini di file config Anda.
// Jika file db.php Anda hanya berisi $conn = new mysqli(...),
// Anda perlu mendefinisikan ulang kredensial di sini.
// Ganti dengan kredensial database Anda yang sebenarnya.
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'db_pkk_bpurba'; // Akan diambil dari koneksi $conn

// Coba dapatkan nama database dari objek koneksi
if ($result = $conn->query("SELECT DATABASE()")) {
    $dbName = $result->fetch_row()[0];
    $result->close();
}

if (empty($dbName)) {
    die("Tidak dapat menentukan nama database.");
}
// --- Akhir Kredensial ---

$backup_content = "";
$backup_content .= "-- Sistem Informasi PKK Bangun Purba - Database Backup\n";
$backup_content .= "-- Tanggal Backup: " . date('Y-m-d H:i:s') . "\n";
$backup_content .= "-- Host: " . $dbHost . "\n";
$backup_content .= "-- Database: " . $dbName . "\n";
$backup_content .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
$backup_content .= "SET time_zone = \"+00:00\";\n\n";

// Ambil semua tabel
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Loop melalui setiap tabel
foreach ($tables as $table) {
    $backup_content .= "\n-- --------------------------------------------------------\n\n";
    $backup_content .= "--\n-- Struktur tabel untuk `" . $table . "`\n--\n\n";

    // Tambahkan perintah DROP TABLE
    $backup_content .= "DROP TABLE IF EXISTS `" . $table . "`;\n";

    // Dapatkan dan tambahkan perintah CREATE TABLE
    $create_table_result = $conn->query("SHOW CREATE TABLE `" . $table . "`");
    $create_table_row = $create_table_result->fetch_row();
    $backup_content .= $create_table_row[1] . ";\n\n";

    // Dapatkan dan tambahkan data (INSERT INTO)
    $data_result = $conn->query("SELECT * FROM `" . $table . "`");
    $num_fields = $data_result->field_count;

    if ($data_result->num_rows > 0) {
        $backup_content .= "--\n-- Dumping data untuk tabel `" . $table . "`\n--\n\n";
        while ($row = $data_result->fetch_row()) {
            $backup_content .= "INSERT INTO `" . $table . "` VALUES(";
            for ($j = 0; $j < $num_fields; $j++) {
                // Escape karakter khusus
                // $row[$j] = addslashes($row[$j]);
                $row[$j] = addslashes($row[$j] ?? '');
                $row[$j] = str_replace("\n", "\\n", $row[$j]);

                if (isset($row[$j])) {
                    $backup_content .= '"' . $row[$j] . '"';
                } else {
                    $backup_content .= 'NULL';
                }

                if ($j < ($num_fields - 1)) {
                    $backup_content .= ',';
                }
            }
            $backup_content .= ");\n";
        }
    }
}

$conn->close();

// Set header untuk download file .sql
$fileName = 'backup_db_pkk_' . date('Y-m-d_H-i-s') . '.sql';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . strlen($backup_content));

// Tampilkan konten backup
echo $backup_content;
exit;

ob_end_flush();

<?php
ob_start();
session_start();

// Hanya admin yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// --- Kredensial Database ---
$dbHost = $conn->host_info;
$dbName = '';
if ($result = $conn->query("SELECT DATABASE()")) {
    $dbName = $result->fetch_row()[0];
    $result->close();
}
if (empty($dbName)) {
    die("Tidak dapat menentukan nama database.");
}
// --- End Kredensial ---

// Mode backup: full / structure
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'full';
// contoh: backup_sql.php?mode=structure

$backup_content  = "-- Sistem Informasi PKK - Database Backup\n";
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

// Loop setiap tabel
foreach ($tables as $table) {
    $backup_content .= "\n-- --------------------------------------------------------\n\n";
    $backup_content .= "-- Struktur tabel untuk `" . $table . "`\n\n";
    $backup_content .= "DROP TABLE IF EXISTS `" . $table . "`;\n";

    // Ambil CREATE TABLE
    $create_table_result = $conn->query("SHOW CREATE TABLE `" . $table . "`");
    $create_table_row = $create_table_result->fetch_row();

    // Bersihkan collation yg tidak kompatibel (contoh dari MySQL 8 ke MariaDB)
    $create_sql = str_replace("utf8mb4_0900_ai_ci", "utf8mb4_general_ci", $create_table_row[1]);

    // Reset AUTO_INCREMENT ke 1 agar fresh di server
    $create_sql = preg_replace('/AUTO_INCREMENT=\d+/', 'AUTO_INCREMENT=1', $create_sql);

    $backup_content .= $create_sql . ";\n\n";

    // Kalau mode full → dump data
    if ($mode === 'full') {
        $data_result = $conn->query("SELECT * FROM `" . $table . "`");
        $num_fields  = $data_result->field_count;

        if ($data_result->num_rows > 0) {
            $backup_content .= "-- Dumping data untuk tabel `" . $table . "`\n\n";
            while ($row = $data_result->fetch_row()) {
                $values = [];
                foreach ($row as $val) {
                    if (is_null($val)) {
                        $values[] = "NULL";
                    } else {
                        $val = addslashes($val);
                        $val = str_replace("\n", "\\n", $val);
                        $values[] = '"' . $val . '"';
                    }
                }
                $backup_content .= "INSERT INTO `$table` VALUES(" . implode(',', $values) . ");\n";
            }
        }
    }
}

$conn->close();

// Set header download
$fileName = 'backup_db_pkk_' . $mode . '_' . date('Y-m-d_H-i-s') . '.sql';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . strlen($backup_content));

echo $backup_content;
exit;

<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';
// Sertakan pustaka FPDF
require('../fpdf/fpdf.php');

// Periksa apakah parameter 'table' ada
if (!isset($_GET['table']) || empty($_GET['table'])) {
    die("Error: Nama tabel tidak disediakan.");
}

$tableName = $_GET['table'];

// --- Keamanan: Validasi Nama Tabel ---
$allowedTables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $allowedTables[] = $row[0];
}
if (!in_array($tableName, $allowedTables)) {
    die("Error: Nama tabel tidak valid atau tidak ditemukan.");
}
// --- Akhir Validasi Keamanan ---

// Ambil data dan nama kolom dari tabel
$result = $conn->query("SELECT * FROM `" . $tableName . "`");
if (!$result) {
    die("Error saat mengambil data: " . $conn->error);
}
$fields = $result->fetch_fields();
$header = [];
foreach ($fields as $field) {
    $header[] = $field->name;
}

// Inisialisasi PDF (L untuk Landscape agar lebih muat)
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Judul Dokumen
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(277, 10, 'Data Tabel: ' . ucfirst($tableName), 0, 1, 'C');
$pdf->Ln(5);

// Header Tabel
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(230, 230, 230); // Warna abu-abu untuk header
$num_columns = count($header);
$page_width = 277; // Lebar halaman A4 landscape dikurangi margin
$col_width = $page_width / $num_columns; // Lebar kolom dinamis

foreach ($header as $col) {
    $pdf->Cell($col_width, 7, $col, 1, 0, 'C', true);
}
$pdf->Ln();

// Isi Tabel
$pdf->SetFont('Arial', '', 8);
while ($row = $result->fetch_assoc()) {
    foreach ($header as $col) {
        // Potong teks yang terlalu panjang agar tidak merusak layout
        $cellText = $row[$col];
        if ($pdf->GetStringWidth($cellText) > $col_width - 2) {
            $cellText = substr($cellText, 0, 25) . '...'; // Sesuaikan panjang pemotongan
        }
        $pdf->Cell($col_width, 6, $cellText, 1);
    }
    $pdf->Ln();
}

// Output PDF untuk di-download
$fileName = $tableName . '_export_' . date('Y-m-d') . '.pdf';
$pdf->Output('D', $fileName);

$conn->close();
exit;

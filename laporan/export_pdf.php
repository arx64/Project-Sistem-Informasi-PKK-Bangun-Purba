<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'kades', 'pkk'])) {
    header("Location: ../auth/login.php");
    exit;
}
require('../fpdf/fpdf.php'); // Pastikan folder fpdf ada
include '../config/db.php';

// Ambil filter
$tanggalMulai = $_GET['mulai'] ?? '';
$tanggalSelesai = $_GET['selesai'] ?? '';
$dawis = $_GET['dawis'] ?? '';

$where = [];
if (!empty($tanggalMulai) && !empty($tanggalSelesai)) {
    $where[] = "k.tanggal BETWEEN '$tanggalMulai' AND '$tanggalSelesai'";
}
if (!empty($dawis)) {
    $where[] = "d.id_dawis = '$dawis'";
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Query
$sql = "
    SELECT k.nama_kegiatan, k.tanggal, d.nama_dawis, k.deskripsi
    FROM kegiatan k
    LEFT JOIN dawis d ON k.id_dawis = d.id_dawis
    $whereSQL
    ORDER BY k.tanggal DESC
";
$result = $conn->query($sql);

// PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Kegiatan PKK', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 7, 'No', 1);
$pdf->Cell(50, 7, 'Nama Kegiatan', 1);
$pdf->Cell(25, 7, 'Tanggal', 1);
$pdf->Cell(30, 7, 'Dawis', 1);
$pdf->Cell(75, 7, 'Deskripsi', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$no = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10, 6, $no++, 1);
    $pdf->Cell(50, 6, $row['nama_kegiatan'], 1);
    $pdf->Cell(25, 6, $row['tanggal'], 1);
    $pdf->Cell(30, 6, $row['nama_dawis'], 1);
    $pdf->Cell(75, 6, $row['deskripsi'], 1);
    $pdf->Ln();
}

$pdf->Output();

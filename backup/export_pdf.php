<?php
require('../fpdf/fpdf.php');
include '../config/db.php';

// Ambil data laporan (JOIN ke tabel dawis & kegiatan)
$query = $conn->query("
    SELECT k.id_kegiatan, k.nama_kegiatan, k.tanggal, k.deskripsi, d.nama_dawis
    FROM kegiatan k
    LEFT JOIN dawis d ON k.id_dawis = d.id_dawis
    ORDER BY k.tanggal DESC
");

// Inisialisasi PDF
$pdf = new FPDF('L', 'mm', 'A4'); // L = Landscape
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Kegiatan PKK', 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 6, 'Tanggal Cetak: ' . date('d-m-Y'), 0, 1, 'R');
$pdf->Ln(3);

// Header Tabel
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(230, 230, 230); // Warna abu-abu muda
$header = ['No', 'Nama Kegiatan', 'Tanggal', 'Deskripsi', 'Dawis'];
$w = [10, 70, 25, 120, 40]; // Lebar kolom

for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
}
$pdf->Ln();

// Isi Tabel
$pdf->SetFont('Arial', '', 10);
$no = 1;
while ($row = $query->fetch_assoc()) {
    $pdf->Cell($w[0], 8, $no++, 1, 0, 'C');
    $pdf->Cell($w[1], 8, $row['nama_kegiatan'], 1);
    $pdf->Cell($w[2], 8, date('d-m-Y', strtotime($row['tanggal'])), 1, 0, 'C');
    $pdf->Cell($w[3], 8, $row['deskripsi'], 1);
    $pdf->Cell($w[4], 8, $row['nama_dawis'] ?? '-', 1, 0, 'C');
    $pdf->Ln();
}

// Output PDF
$pdf->Output('D', 'laporan_kegiatan_' . date('Y-m-d') . '.pdf');

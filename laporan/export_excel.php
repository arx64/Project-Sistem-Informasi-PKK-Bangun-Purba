<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'kades', 'pkk'])) {
    header("Location: ../auth/login.php");
    exit;
}
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

// Set header untuk download Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Kegiatan.xls");

echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Nama Kegiatan</th>
        <th>Tanggal</th>
        <th>Dawis</th>
        <th>Deskripsi</th>
      </tr>";

$no = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . $no++ . "</td>
            <td>" . htmlspecialchars($row['nama_kegiatan']) . "</td>
            <td>" . $row['tanggal'] . "</td>
            <td>" . $row['nama_dawis'] . "</td>
            <td>" . htmlspecialchars($row['deskripsi']) . "</td>
          </tr>";
}
echo "</table>";

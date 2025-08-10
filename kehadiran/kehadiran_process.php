<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pkk'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

$id_kegiatan = intval($_POST['id_kegiatan']);
$statusData = $_POST['status'] ?? [];

// Simpan satu per satu
foreach ($statusData as $id_anggota => $status) {
    $id_anggota = intval($id_anggota);
    $status = $conn->real_escape_string($status);

    // Cek apakah sudah ada
    $cek = $conn->query("SELECT id_kehadiran FROM kehadiran WHERE id_kegiatan=$id_kegiatan AND id_anggota=$id_anggota");
    if ($cek->num_rows > 0) {
        $conn->query("UPDATE kehadiran SET status='$status' WHERE id_kegiatan=$id_kegiatan AND id_anggota=$id_anggota");
    } else {
        $conn->query("INSERT INTO kehadiran (id_anggota, id_kegiatan, status) VALUES ($id_anggota, $id_kegiatan, '$status')");
    }
}

header("Location: index.php?id_kegiatan=$id_kegiatan");
exit;

<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pkk'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kegiatan = intval($_POST['id_kegiatan']);
    $statuses = $_POST['status'] ?? [];

    // 1. Proses Status Kehadiran Anggota
    foreach ($statuses as $id_anggota => $status) {
        $id_anggota = intval($id_anggota);
        $status = $conn->real_escape_string($status);

        // Cek apakah data sudah ada untuk di-update atau perlu di-insert
        $stmt_check = $conn->prepare("SELECT id_kehadiran FROM kehadiran WHERE id_kegiatan = ? AND id_anggota = ?");
        $stmt_check->bind_param("ii", $id_kegiatan, $id_anggota);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            // Update data yang sudah ada
            $stmt_update = $conn->prepare("UPDATE kehadiran SET status = ? WHERE id_kegiatan = ? AND id_anggota = ?");
            $stmt_update->bind_param("sii", $status, $id_kegiatan, $id_anggota);
            $stmt_update->execute();
        } else {
            // Insert data baru
            $stmt_insert = $conn->prepare("INSERT INTO kehadiran (id_kegiatan, id_anggota, status) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("iis", $id_kegiatan, $id_anggota, $status);
            $stmt_insert->execute();
        }
    }

    // 2. Proses Upload Foto Bukti
    if (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/bukti_kehadiran/';
        $fileTmpPath = $_FILES['foto_bukti']['tmp_name'];
        $fileName = basename($_FILES['foto_bukti']['name']);
        $fileSize = $_FILES['foto_bukti']['size'];
        $fileType = $_FILES['foto_bukti']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
            // Buat nama file unik untuk menghindari penimpaan file dengan nama yang sama
            $newFileName = uniqid('bukti_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Update nama file di database
                $stmt_foto = $conn->prepare("UPDATE kegiatan SET foto_bukti = ? WHERE id_kegiatan = ?");
                $stmt_foto->bind_param("si", $newFileName, $id_kegiatan);
                $stmt_foto->execute();
            } else {
                // Gagal memindahkan file
                $_SESSION['error_message'] = "Terjadi kesalahan saat mengunggah file.";
            }
        } else {
            // Validasi gagal
            $_SESSION['error_message'] = "Format file tidak valid atau ukuran terlalu besar (Maks 2MB).";
        }
    }

    // Set pesan sukses dan redirect kembali
    if (!isset($_SESSION['error_message'])) {
        $_SESSION['success_message'] = "Data kehadiran berhasil disimpan.";
    }
    header("Location: index.php?id_kegiatan=" . $id_kegiatan);
    exit;
}

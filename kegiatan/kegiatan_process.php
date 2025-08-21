<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pkk'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// Pastikan folder upload ada
$uploadDir = "../uploads/kegiatan/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Fungsi validasi file upload
function validasiFile($file)
{
    $maxSize = 2 * 1024 * 1024; // 2MB
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // Cek ukuran file
    if ($file['size'] > $maxSize) {
        return "Ukuran file maksimal 2MB!";
    }

    // Cek tipe MIME
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        return "Hanya file gambar yang diperbolehkan (JPG, PNG, GIF, WEBP)!";
    }

    return true;
}

// ========== TAMBAH DATA ==========
if (isset($_POST['tambah'])) {
    $id_dawis = $_POST['id_dawis'];
    $nama_kegiatan = $_POST['nama_kegiatan'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];

    // Upload foto
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $validasi = validasiFile($_FILES['foto']);
        if ($validasi === true) {
            $fotoName = time() . "_" . basename($_FILES['foto']['name']);
            $fotoPath = $uploadDir . $fotoName;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $fotoPath)) {
                $foto = $fotoName;
            }
        } else {
            $_SESSION['error'] = $validasi;
            header("Location: /kegiatan");
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO kegiatan (id_dawis, nama_kegiatan, tanggal, deskripsi, foto) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $id_dawis, $nama_kegiatan, $tanggal, $deskripsi, $foto);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Data kegiatan berhasil ditambahkan!";
    header("Location: /kegiatan");
    exit;
}

// ========== UPDATE DATA ==========
if (isset($_POST['update'])) {
    $id_kegiatan = $_POST['id_kegiatan'];
    $id_dawis = $_POST['id_dawis'];
    $nama_kegiatan = $_POST['nama_kegiatan'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];
    $foto_lama = $_POST['foto_lama'];

    $foto = $foto_lama;

    // Jika upload foto baru
    if (!empty($_FILES['foto']['name'])) {
        $validasi = validasiFile($_FILES['foto']);
        if ($validasi === true) {
            $fotoName = time() . "_" . basename($_FILES['foto']['name']);
            $fotoPath = $uploadDir . $fotoName;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $fotoPath)) {
                $foto = $fotoName;
                // Hapus foto lama jika ada
                if (!empty($foto_lama) && file_exists($uploadDir . $foto_lama)) {
                    unlink($uploadDir . $foto_lama);
                }
            }
        } else {
            $_SESSION['error'] = $validasi;
            header("Location: edit.php?id=" . $id_kegiatan);
            exit;
        }
    }

    $stmt = $conn->prepare("UPDATE kegiatan SET id_dawis=?, nama_kegiatan=?, tanggal=?, deskripsi=?, foto=? WHERE id_kegiatan=?");
    $stmt->bind_param("issssi", $id_dawis, $nama_kegiatan, $tanggal, $deskripsi, $foto, $id_kegiatan);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Data kegiatan berhasil diperbarui!";
    header("Location: /kegiatan");
    exit;
}

// ========== HAPUS DATA ==========
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Ambil nama file foto
    $stmt = $conn->prepare("SELECT foto FROM kegiatan WHERE id_kegiatan=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($foto);
    $stmt->fetch();
    $stmt->close();

    // Hapus dari database
    $stmt = $conn->prepare("DELETE FROM kegiatan WHERE id_kegiatan=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Hapus file foto
    if (!empty($foto) && file_exists($uploadDir . $foto)) {
        unlink($uploadDir . $foto);
    }

    $_SESSION['success'] = "Data kegiatan berhasil dihapus!";
    header("Location: /kegiatan");
    exit;
}

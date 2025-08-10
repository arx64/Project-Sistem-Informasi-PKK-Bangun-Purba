<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pkk'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// Ambil data kegiatan yang mau diedit
$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM kegiatan WHERE id_kegiatan = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Ambil daftar Dawis untuk dropdown
$dawisList = $conn->query("SELECT id_dawis, nama_dawis FROM dawis ORDER BY nama_dawis ASC");

if (!$row) {
    die("Data kegiatan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Kegiatan</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/side_bar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="m-0">Edit Kegiatan</h1>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <form method="POST" action="kegiatan_process.php" enctype="multipart/form-data">
                        <input type="hidden" name="id_kegiatan" value="<?= $row['id_kegiatan'] ?>">
                        <input type="hidden" name="foto_lama" value="<?= $row['foto'] ?>">

                        <div class="card">
                            <div class="card-body">

                                <!-- Pilih Dawis -->
                                <div class="form-group">
                                    <label>Dawis</label>
                                    <select name="id_dawis" class="form-control" required>
                                        <option value="">Pilih Dawis</option>
                                        <?php while ($d = $dawisList->fetch_assoc()) { ?>
                                            <option value="<?= $d['id_dawis'] ?>" <?= ($d['id_dawis'] == $row['id_dawis']) ? 'selected' : '' ?>>
                                                <?= $d['nama_dawis'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- Nama Kegiatan -->
                                <div class="form-group">
                                    <label>Nama Kegiatan</label>
                                    <input type="text" name="nama_kegiatan" class="form-control" value="<?= htmlspecialchars($row['nama_kegiatan']) ?>" required>
                                </div>

                                <!-- Tanggal -->
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal'] ?>" required>
                                </div>

                                <!-- Deskripsi -->
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                </div>

                                <!-- Foto -->
                                <div class="form-group">
                                    <label>Foto</label><br>
                                    <?php if (!empty($row['foto'])) { ?>
                                        <img src="../uploads/kegiatan/<?= $row['foto'] ?>" width="120" class="mb-2">
                                    <?php } ?>
                                    <input type="file" name="foto" class="form-control">
                                    <small>Kosongkan jika tidak ingin mengganti foto.</small>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" name="update" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include '../includes/footer.php'; ?>
    </div>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
</body>

</html>
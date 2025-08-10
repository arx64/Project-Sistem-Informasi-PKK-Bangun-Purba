<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pkk'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

// Ambil daftar kegiatan
$kegiatan = $conn->query("SELECT * FROM kegiatan ORDER BY tanggal DESC");

// Cek jika ada kegiatan dipilih
$id_kegiatan = isset($_GET['id_kegiatan']) ? intval($_GET['id_kegiatan']) : 0;
$anggota = [];
$kehadiranData = [];

if ($id_kegiatan) {
    // Ambil semua anggota
    $anggota = $conn->query("SELECT * FROM anggota ORDER BY nama_anggota");

    // Ambil data kehadiran yang sudah ada
    $res = $conn->query("SELECT * FROM kehadiran WHERE id_kegiatan = $id_kegiatan");
    while ($row = $res->fetch_assoc()) {
        $kehadiranData[$row['id_anggota']] = $row['status'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kehadiran</title>
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
                    <h4>Input Kehadiran</h4>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">

                    <!-- Pilih kegiatan -->
                    <form method="GET" class="mb-3">
                        <label>Pilih Kegiatan</label>
                        <select name="id_kegiatan" class="form-control" onchange="this.form.submit()" required>
                            <option value="">-- Pilih --</option>
                            <?php while ($k = $kegiatan->fetch_assoc()): ?>
                                <option value="<?= $k['id_kegiatan'] ?>" <?= ($id_kegiatan == $k['id_kegiatan']) ? 'selected' : '' ?>>
                                    <?= $k['nama_kegiatan'] ?> (<?= $k['tanggal'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </form>

                    <?php if ($id_kegiatan && $anggota->num_rows > 0): ?>
                        <form method="POST" action="kehadiran_process.php">
                            <input type="hidden" name="id_kegiatan" value="<?= $id_kegiatan ?>">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Anggota</th>
                                        <th style="width: 300px;">Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($a = $anggota->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $a['nama_anggota'] ?></td>
                                            <td>
                                                <?php
                                                $status = $kehadiranData[$a['id_anggota']] ?? '';
                                                ?>
                                                <label><input type="radio" name="status[<?= $a['id_anggota'] ?>]" value="Hadir" <?= ($status == 'Hadir') ? 'checked' : '' ?>> Hadir</label>
                                                <label class="ml-3"><input type="radio" name="status[<?= $a['id_anggota'] ?>]" value="Izin" <?= ($status == 'Izin') ? 'checked' : '' ?>> Izin</label>
                                                <label class="ml-3"><input type="radio" name="status[<?= $a['id_anggota'] ?>]" value="Tidak Hadir" <?= ($status == 'Tidak Hadir') ? 'checked' : '' ?>> Tidak Hadir</label>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary">Simpan Kehadiran</button>
                            <a href="rekap.php?id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-success">Lihat Rekap</a>
                        </form>
                    <?php endif; ?>

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
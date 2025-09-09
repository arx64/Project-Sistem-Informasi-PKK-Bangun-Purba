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
$fotoBukti = ''; // Variabel untuk menyimpan nama file foto bukti

if ($id_kegiatan) {
    // Ambil semua anggota
    $anggota = $conn->query("SELECT a.id_anggota, a.nama_anggota, a.alamat, b.nama_dawis 
FROM anggota AS a 
INNER JOIN dawis AS b ON a.id_dawis = b.id_dawis
ORDER BY a.nama_anggota");

    // Ambil data kehadiran yang sudah ada
    $res = $conn->query("SELECT * FROM kehadiran WHERE id_kegiatan = $id_kegiatan");
    while ($row = $res->fetch_assoc()) {
        $kehadiranData[$row['id_anggota']] = $row['status'];
    }

    // Ambil foto bukti yang sudah ada untuk kegiatan ini
    $stmt = $conn->prepare("SELECT foto_bukti FROM kegiatan WHERE id_kegiatan = ?");
    $stmt->bind_param("i", $id_kegiatan);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $kegiatanData = $result->fetch_assoc();
        $fotoBukti = $kegiatanData['foto_bukti'];
    }
    $stmt->close();
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
    <!-- Tambahkan CSS DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/side_bar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <!-- <?= $_SESSION['error_message']; ?> -->
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
                            <?php mysqli_data_seek($kegiatan, 0); // Reset pointer hasil query 
                            ?>
                            <?php while ($k = $kegiatan->fetch_assoc()): ?>
                                <option value="<?= $k['id_kegiatan'] ?>" <?= ($id_kegiatan == $k['id_kegiatan']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama_kegiatan']) ?> (<?= $k['tanggal'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                    <?php if ($id_kegiatan && $anggota->num_rows > 0): ?>
                        <!-- PENTING: Tambahkan enctype="multipart/form-data" di sini -->
                        <form method="POST" action="kehadiran_process.php" enctype="multipart/form-data">
                            <input type="hidden" name="id_kegiatan" value="<?= $id_kegiatan ?>">

                            <!-- Tombol bulk action -->
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-success" onclick="setAllStatus('Hadir')">Semua Hadir</button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="setAllStatus('Izin')">Semua Izin</button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="setAllStatus('Tidak Hadir')">Semua Tidak Hadir</button>
                            </div>

                            <table id="tabelKehadiran" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Anggota</th>
                                        <th>Asal Dawis</th>
                                        <th style="width: 300px;">Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 0;
                                    while ($a = $anggota->fetch_assoc()): $no++; ?>
                                        <?php $status = $kehadiranData[$a['id_anggota']] ?? ''; ?>
                                        <tr>
                                            <td><?= $no; ?></td>
                                            <td><?= htmlspecialchars($a['nama_anggota']) ?></td>
                                            <td><?= htmlspecialchars($a['nama_dawis']) ?></td>
                                            <td>
                                                <select class="form-control form-control-sm" name="status[<?= $a['id_anggota'] ?>]">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="Hadir" <?= ($status == 'Hadir') ? 'selected' : '' ?>>Hadir</option>
                                                    <option value="Izin" <?= ($status == 'Izin') ? 'selected' : '' ?>>Izin</option>
                                                    <option value="Tidak Hadir" <?= ($status == 'Tidak Hadir') ? 'selected' : '' ?>>Tidak Hadir</option>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>

                            <!-- [BARU] Form Upload Foto Bukti Kehadiran -->
                            <div class="form-group mt-4">
                                <label for="foto_bukti">Upload Foto Bukti Kehadiran</label>

                                <!-- Tampilkan dan hapus pesan notifikasi -->
                                <?php if (isset($_SESSION['success_message'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?= htmlspecialchars($_SESSION['success_message']); ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php unset($_SESSION['success_message']); // KRUSIAL: Hapus session setelah ditampilkan 
                                    ?>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['error_message'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?= htmlspecialchars($_SESSION['error_message']); ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php unset($_SESSION['error_message']); // KRUSIAL: Hapus session setelah ditampilkan 
                                    ?>
                                <?php endif; ?>

                                <input type="file" class="form-control-file" id="foto_bukti" name="foto_bukti" accept="image/png, image/jpeg, image/jpg">
                                <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG. Ukuran maksimal: 2MB.</small>
                                <?php if (!empty($fotoBukti)): ?>
                                    <div class="mt-2">
                                        <p>Foto saat ini:</p>
                                        <img src="../uploads/bukti_kehadiran/<?= htmlspecialchars($fotoBukti) ?>" alt="Foto Bukti" style="max-width: 250px; height: auto; border: 1px solid #ddd; padding: 5px;">
                                        <p class="text-muted"><small>Mengunggah foto baru akan menggantikan foto yang sudah ada.</small></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Simpan Kehadiran</button>
                            <a href="rekap.php?id_kegiatan=<?= $id_kegiatan ?>" class="btn btn-success mt-3">Lihat Rekap</a>
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
    <!-- Tambahkan JS DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Fungsi untuk set semua status sekaligus
        function setAllStatus(status) {
            document.querySelectorAll('#tabelKehadiran select').forEach(function(select) {
                select.value = status;
            });
        }

        $(document).ready(function() {
            $('#tabelKehadiran').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                },
                "pageLength": 10
            });
        });
    </script>
</body>

</html>
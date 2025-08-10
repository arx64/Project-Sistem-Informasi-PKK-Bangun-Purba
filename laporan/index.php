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

        // Query laporan
        // $sql = "
        //     SELECT k.id_kegiatan, k.nama_kegiatan, k.tanggal, d.nama_dawis, k.deskripsi
        //     FROM kegiatan k
        //     LEFT JOIN dawis d ON k.id_dawis = d.id_dawis
        //     $whereSQL
        //     ORDER BY k.tanggal DESC
        // ";
        //         $sql = "
        //     SELECT k.nama_kegiatan, k.tanggal, k.deskripsi, k.foto
        //     FROM kegiatan k
        //     $whereSQL
        //     ORDER BY k.tanggal DESC
        // ";
        $sql = "
    SELECT k.nama_kegiatan, k.tanggal, k.deskripsi, k.foto, d.nama_dawis
    FROM kegiatan k
    LEFT JOIN dawis d ON k.id_dawis = d.id_dawis
    $whereSQL
    ORDER BY k.tanggal DESC
";


        $result = $conn->query($sql);

// Ambil data Dawis untuk filter
$dawisList = $conn->query("SELECT * FROM dawis ORDER BY nama_dawis ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
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
                    <h1 class="m-0">Laporan Kegiatan</h1>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">

                    <!-- Filter Laporan -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong>Filter Laporan</strong>
                        </div>
                        <div class="card-body">
                            <form method="GET">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" name="mulai" value="<?= $tanggalMulai ?>" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Tanggal Selesai</label>
                                        <input type="date" name="selesai" value="<?= $tanggalSelesai ?>" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Dawis</label>
                                        <select name="dawis" class="form-control">
                                            <option value="">Semua</option>
                                            <?php while ($d = $dawisList->fetch_assoc()) { ?>
                                                <option value="<?= $d['id_dawis'] ?>" <?= ($dawis == $d['id_dawis']) ? 'selected' : '' ?>>
                                                    <?= $d['nama_dawis'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mt-4">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
                                        <a href="export_excel.php?mulai=<?= $tanggalMulai ?>&selesai=<?= $tanggalSelesai ?>&dawis=<?= $dawis ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</a>
                                        <a href="export_pdf.php?mulai=<?= $tanggalMulai ?>&selesai=<?= $tanggalSelesai ?>&dawis=<?= $dawis ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> PDF</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Laporan -->
                    <div class="card">
                        <div class="card-header">
                            <strong>Hasil Laporan</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped table-responsive">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kegiatan</th>
                                        <th>Tanggal</th>
                                        <th>Dawis</th>
                                        <th>Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                            <td><?= $row['tanggal'] ?></td>
                                            <td><?= $row['nama_dawis'] ?></td>
                                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

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
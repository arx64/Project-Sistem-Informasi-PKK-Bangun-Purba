<?php
// Memulai sesi dan memastikan hanya admin yang dapat mengakses halaman ini
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    // Arahkan ke halaman login jika bukan admin atau sesi tidak ada
    header("Location: ../auth/login.php"); // Sesuaikan path jika perlu
    exit;
}

// Sertakan file koneksi database
// Path ini mengasumsikan struktur folder: project/admin/backup/
include '../config/db.php';

// Inisialisasi array untuk menampung nama tabel
$tables = [];
// Query untuk mendapatkan semua nama tabel dari database
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    $result->free();
}
$conn->close(); // Tutup koneksi setelah tidak diperlukan lagi
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Backup & Export Data</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- AdminLTE Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Include komponen UI dari template AdminLTE -->
        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/side_bar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><i class="fas fa-database"></i> Backup & Export Data</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active">Backup & Export</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Card Utama untuk Export Tabel -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-table"></i> Export Data per Tabel</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 60%;">Nama Tabel</th>
                                        <th class="text-center">Export ke Excel (.csv)</th>
                                        <th class="text-center">Export ke PDF (.pdf)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tables)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada tabel yang ditemukan di database.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($tables as $table): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($table) ?></strong></td>
                                                <td class="text-center">
                                                    <a href="export_excel.php?table=<?= urlencode($table) ?>" class="btn btn-success btn-sm" title="Export tabel <?= htmlspecialchars($table) ?> ke Excel">
                                                        <i class="fas fa-file-excel"></i> Export
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <a href="export_pdf.php?table=<?= urlencode($table) ?>" class="btn btn-danger btn-sm" title="Export tabel <?= htmlspecialchars($table) ?> ke PDF">
                                                        <i class="fas fa-file-pdf"></i> Export
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center">
                            <a href="backup_sql.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-download"></i> Backup Seluruh Database (.sql)
                            </a>
                            <p class="text-muted mt-2 mb-0">
                                <small>Tindakan ini akan mengunduh seluruh struktur dan data database dalam satu file SQL.</small>
                            </p>
                        </div>
                    </div>

                    <!-- Card Informasi Tambahan -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Export Excel</strong> menghasilkan file berformat `.csv` yang kompatibel dengan semua aplikasi spreadsheet.</li>
                                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Export PDF</strong> menghasilkan dokumen `.pdf` dengan format tabel yang rapi.</li>
                                        <li><i class="fas fa-check-circle text-success mr-2"></i><strong>Backup Database</strong> sangat penting untuk pemulihan data jika terjadi kegagalan sistem.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Perhatian</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-exclamation-circle text-warning mr-2"></i>Pastikan Anda memiliki hak akses yang cukup untuk melakukan backup.</li>
                                        <li><i class="fas fa-exclamation-circle text-warning mr-2"></i>File backup berisi data yang mungkin sensitif. Simpan di lokasi yang aman.</li>
                                        <li><i class="fas fa-exclamation-circle text-warning mr-2"></i>Lakukan backup secara berkala untuk mencegah kehilangan data.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php include '../includes/footer.php'; ?>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
</body>

</html>
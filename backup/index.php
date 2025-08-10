<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Backup & Export Data</title>

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
                    <h1 class="m-0">Backup & Export Data</h1>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">

                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-download"></i> Pilih Tabel untuk Export</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Tabel</th>
                                        <th>Export Excel</th>
                                        <th>Export PDF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $tabels = ['pengguna', 'dawis', 'anggota', 'kegiatan', 'kehadiran'];
                                    $no = 1;
                                    foreach ($tabels as $tbl): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= ucfirst($tbl) ?></td>
                                            <td>
                                                <a href="export_excel.php?table=<?= $tbl ?>" class="btn btn-success btn-sm">
                                                    <i class="fas fa-file-excel"></i> Excel
                                                </a>
                                            </td>
                                            <td>
                                                <a href="export_pdf.php?table=<?= $tbl ?>" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </section>
        </div>

        <?php include '../includes/footer.php'; ?>
    </div>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
</body>

</html>
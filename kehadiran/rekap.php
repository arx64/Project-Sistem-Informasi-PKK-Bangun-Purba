<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pkk', 'kades'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

$id_kegiatan = intval($_GET['id_kegiatan']);
$kegiatan = $conn->query("SELECT * FROM kegiatan WHERE id_kegiatan=$id_kegiatan")->fetch_assoc();

// Hitung rekap
$q = $conn->query("
    SELECT 
        SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) AS hadir,
        SUM(CASE WHEN status='Izin' THEN 1 ELSE 0 END) AS izin,
        SUM(CASE WHEN status='Tidak Hadir' THEN 1 ELSE 0 END) AS tidak_hadir
    FROM kehadiran
    WHERE id_kegiatan=$id_kegiatan
");
$data = $q->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Kehadiran</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/side_bar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h4>Rekap Kehadiran - <?= $kegiatan['nama_kegiatan'] ?></h4>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid text-center">
                    <canvas id="chartKehadiran" style="max-width: 400px; margin:auto;"></canvas>
                </div>
            </div>
        </div>
        <?php include '../includes/footer.php'; ?>
    </div>
    <script>
        new Chart(document.getElementById('chartKehadiran'), {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Izin', 'Tidak Hadir'],
                datasets: [{
                    data: [<?= $data['hadir'] ?>, <?= $data['izin'] ?>, <?= $data['tidak_hadir'] ?>],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            }
        });
    </script>
    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
</body>

</html>
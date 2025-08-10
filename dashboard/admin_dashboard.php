<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header("Location: ../auth/login.php");
  exit;
}
include '../config/db.php';

// Statistik ringkas
$totalPengguna = $conn->query("SELECT COUNT(*) AS total FROM pengguna")->fetch_assoc()['total'];
$totalAnggota  = $conn->query("SELECT COUNT(*) AS total FROM anggota")->fetch_assoc()['total'];
$totalKegiatan = $conn->query("SELECT COUNT(*) AS total FROM kegiatan")->fetch_assoc()['total'];
$totalDawis    = $conn->query("SELECT COUNT(*) AS total FROM dawis")->fetch_assoc()['total'];

// Grafik Jumlah Anggota per Dawis
$qAnggota = $conn->query("
    SELECT d.nama_dawis, COUNT(a.id_anggota) as total
    FROM dawis d
    LEFT JOIN anggota a ON d.id_dawis = a.id_dawis
    GROUP BY d.id_dawis
");
$labelsDawis = [];
$dataAnggota = [];
while ($row = $qAnggota->fetch_assoc()) {
  $labelsDawis[] = $row['nama_dawis'];
  $dataAnggota[] = $row['total'];
}

// Grafik Persentase Kehadiran
$qKehadiran = $conn->query("
    SELECT 
        SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) AS hadir,
        SUM(CASE WHEN status='Izin' THEN 1 ELSE 0 END) AS izin,
        SUM(CASE WHEN status='Tidak Hadir' THEN 1 ELSE 0 END) AS tidak_hadir
    FROM kehadiran
");
$rowKehadiran = $qKehadiran->fetch_assoc();
$labelsKehadiran = ['Hadir', 'Izin', 'Tidak Hadir'];
$dataKehadiran = [
  $rowKehadiran['hadir'],
  $rowKehadiran['izin'],
  $rowKehadiran['tidak_hadir']
];

// Daftar kegiatan terbaru
$qKegiatan = $conn->query("SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>

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
          <h1 class="m-0">Dashboard Admin</h1>
        </div>
      </div>

      <div class="content">
        <div class="container-fluid">
          <!-- Statistik -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <div class="small-box bg-primary">
                <div class="inner">
                  <h3><?= $totalPengguna ?></h3>
                  <p>Pengguna</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?= $totalAnggota ?></h3>
                  <p>Anggota PKK</p>
                </div>
                <div class="icon"><i class="fas fa-user-friends"></i></div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?= $totalKegiatan ?></h3>
                  <p>Kegiatan</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?= $totalDawis ?></h3>
                  <p>Dawis</p>
                </div>
                <div class="icon"><i class="fas fa-sitemap"></i></div>
              </div>
            </div>
          </div>

          <!-- Grafik -->
          <div class="row">
            <div class="col-md-6">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Jumlah Anggota per Dawis</h3>
                </div>
                <div class="card-body">
                  <canvas id="anggotaChart"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Persentase Kehadiran</h3>
                </div>
                <div class="card-body">
                  <canvas id="kehadiranChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Kegiatan Terbaru -->
          <div class="card card-info">
            <div class="card-header">
              <h3 class="card-title">Kegiatan Terbaru</h3>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Nama Kegiatan</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($k = $qKegiatan->fetch_assoc()) { ?>
                    <tr>
                      <td><?= $k['nama_kegiatan'] ?></td>
                      <td><?= $k['tanggal'] ?></td>
                      <td><?= $k['deskripsi'] ?></td>
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    new Chart(document.getElementById('anggotaChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode($labelsDawis) ?>,
        datasets: [{
          label: 'Jumlah Anggota',
          data: <?= json_encode($dataAnggota) ?>,
          backgroundColor: '#007bff'
        }]
      }
    });

    new Chart(document.getElementById('kehadiranChart'), {
      type: 'pie',
      data: {
        labels: <?= json_encode($labelsKehadiran) ?>,
        datasets: [{
          data: <?= json_encode($dataKehadiran) ?>,
          backgroundColor: ['#28a745', '#ffc107', '#dc3545']
        }]
      }
    });
  </script>
</body>

</html>
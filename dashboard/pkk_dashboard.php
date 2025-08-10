<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pkk') {
  header("Location: ../auth/login.php");
  exit;
}
include '../config/db.php';

// Jumlah Kegiatan
$totalKegiatan = $conn->query("SELECT COUNT(*) as total FROM kegiatan")->fetch_assoc()['total'];

// Jumlah Anggota
$totalAnggota = $conn->query("SELECT COUNT(*) as total FROM anggota")->fetch_assoc()['total'];

// Grafik Anggota per Dawis
$qDawis = $conn->query("
    SELECT d.nama_dawis, COUNT(a.id_anggota) as total
    FROM dawis d
    LEFT JOIN anggota a ON d.id_dawis = a.id_dawis
    GROUP BY d.id_dawis
");
$labelsDawis = [];
$dataDawis = [];
while ($row = $qDawis->fetch_assoc()) {
  $labelsDawis[] = $row['nama_dawis'];
  $dataDawis[] = $row['total'];
}

// Kehadiran terakhir (ambil kegiatan terakhir)
$qLastKegiatan = $conn->query("SELECT id_kegiatan FROM kegiatan ORDER BY tanggal DESC LIMIT 1");
if ($qLastKegiatan->num_rows > 0) {
  $lastId = $qLastKegiatan->fetch_assoc()['id_kegiatan'];
  $qKehadiran = $conn->query("
        SELECT 
            SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) AS hadir,
            SUM(CASE WHEN status='Izin' THEN 1 ELSE 0 END) AS izin,
            SUM(CASE WHEN status='Tidak Hadir' THEN 1 ELSE 0 END) AS tidak_hadir
        FROM kehadiran
        WHERE id_kegiatan = $lastId
    ")->fetch_assoc();
} else {
  $qKehadiran = ['hadir' => 0, 'izin' => 0, 'tidak_hadir' => 0];
}

// Kegiatan terbaru
$kegiatanTerbaru = $conn->query("SELECT * FROM kegiatan ORDER BY tanggal DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PKK - Dashboard</title>
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
          <h4>Dashboard PKK</h4>
        </div>
      </div>
      <div class="content">
        <div class="container-fluid">

          <!-- Statistik -->
          <div class="row">
            <div class="col-lg-6 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?= $totalKegiatan ?></h3>
                  <p>Total Kegiatan</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
              </div>
            </div>
            <div class="col-lg-6 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?= $totalAnggota ?></h3>
                  <p>Total Anggota</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
              </div>
            </div>
          </div>

          <!-- Grafik -->
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">Jumlah Anggota per Dawis</div>
                <div class="card-body">
                  <canvas id="anggotaChart"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">Rekap Kehadiran Terakhir</div>
                <div class="card-body">
                  <canvas id="kehadiranChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabel kegiatan terbaru -->
          <div class="card">
            <div class="card-header">Kegiatan Terbaru</div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Nama Kegiatan</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($k = $kegiatanTerbaru->fetch_assoc()): ?>
                    <tr>
                      <td><?= $k['nama_kegiatan'] ?></td>
                      <td><?= $k['tanggal'] ?></td>
                      <td><?= $k['deskripsi'] ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
    <?php include '../includes/footer.php'; ?>
  </div>
  <script>
    new Chart(document.getElementById('anggotaChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode($labelsDawis) ?>,
        datasets: [{
          label: 'Jumlah Anggota',
          data: <?= json_encode($dataDawis) ?>,
          backgroundColor: '#0d6efd'
        }]
      }
    });
    new Chart(document.getElementById('kehadiranChart'), {
      type: 'pie',
      data: {
        labels: ['Hadir', 'Izin', 'Tidak Hadir'],
        datasets: [{
          data: [<?= $qKehadiran['hadir'] ?>, <?= $qKehadiran['izin'] ?>, <?= $qKehadiran['tidak_hadir'] ?>],
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
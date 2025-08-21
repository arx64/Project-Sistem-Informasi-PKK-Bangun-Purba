<?php
// detail_kegiatan.php

include 'config/db.php';

// Ambil ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: kegiatan.php");
    exit;
}

$id_kegiatan = intval($_GET['id']);

// Ambil data kegiatan
$query = "
    SELECT k.*, d.nama_dawis 
    FROM kegiatan k
    LEFT JOIN dawis d ON k.id_dawis = d.id_dawis
    WHERE k.id_kegiatan = ?
    LIMIT 1
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_kegiatan);
$stmt->execute();
$result = $stmt->get_result();
$kegiatan = $result->fetch_assoc();

if (!$kegiatan) {
    header("Location: kegiatan.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($kegiatan['nama_kegiatan']); ?> - PKK Bangun Purba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #006400;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .nav-link:hover {
            color: #FFD700 !important;
        }

        .nav-link-active {
            color: #FFD700 !important;
            font-weight: bold;
        }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/hero.jpg') center/cover no-repeat;
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        footer {
            background-color: #004d00;
            color: white;
            padding: 20px 0;
        }

        footer a {
            color: #FFD700;
            text-decoration: none;
        }

        .img-hero {
            background: url('assets/img/icon-hero.jpg');
            background-size: cover;
            background-position: center;
            height: 300px;
        }

        .detail-img {
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="/assets/img/icon-pemkab.png" alt="Logo" width="40" height="40" class="me-2">
                PKK Bangun Purba
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto text-center">
                    <li class="nav-item"><a class="nav-link" href="/index.php"><i class="bi bi-house-door"></i> Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="/profil.php"><i class="bi bi-person"></i> Profil</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-active" href="/kegiatan.php"><i class="bi bi-calendar-event"></i> Kegiatan</a></li>
                    <li class="nav-item"><a class="nav-link" href="/kontak.php"><i class="bi bi-envelope"></i> Kontak</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Banner -->
    <div class="hero img-hero">
        <div class="container bg-dark bg-opacity-50 p-2 rounded">
            <h1 class="fw-bold"><?php echo htmlspecialchars($kegiatan['nama_kegiatan']); ?></h1>
            <p class="lead"><?php echo date('d M Y', strtotime($kegiatan['tanggal'])); ?> | <?php echo htmlspecialchars($kegiatan['nama_dawis']); ?></p>
        </div>
    </div>

    <!-- Konten Detail -->
    <div class="container my-5">
        <div class="row g-4">
            <div class="col-md-6">
                <img src="uploads/kegiatan/<?php echo htmlspecialchars($kegiatan['foto']); ?>" class="img-fluid detail-img shadow" alt="Foto Kegiatan">
            </div>
            <div class="col-md-6">
                <h3 class="text-success">Deskripsi Kegiatan</h3>
                <p><?php echo nl2br(htmlspecialchars($kegiatan['deskripsi'])); ?></p>
            </div>
        </div>
        <div class="mt-4">
            <a href="kegiatan.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Kegiatan</a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Alamat Kantor PKK</h5>
                    <p>Jl. Perintis Kemerdekaan, Bangun Purba Tengah, Kec. Bangun Purba, Kabupaten Deli Serdang, Sumatera Utara 20581</p>
                </div>
                <div class="col-md-3">
                    <h5>Link Terkait</h5>
                    <ul class="list-unstyled">
                        <li><a href="https://www.tiktok.com/@pkk.bangun.purba"><i class="bi bi-tiktok"></i> Tiktok</a></li>
                        <li><a href="https://www.instagram.com/pkkbangunpurba/"><i class="bi bi-instagram"></i> IG</a></li>
                        <li><a href="https://web.facebook.com/profile.php?id=61579244115376"><i class="bi bi-facebook"></i> FB</a></li>
                    </ul>
                </div>
                <div class="col-md-3 text-md-end">
                    <h5>Kontak</h5>
                    <p>Email: <a href="mailto:pkkbangunpurba@gmail.com">pkkbangunpurba@gmail.com</a></p>
                </div>
            </div>
            <div class="text-center mt-3">&copy; <?php echo date('Y'); ?> Sistem Informasi PKK - Created By <a href="https://www.instagram.com/_arifin.ilham06" target="_blank">Muhammad Arifin Ilham</a> & <a href="https://www.instagram.com/_andinidwiputri_" target="_blank">Andini Dwi Putri</a></div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
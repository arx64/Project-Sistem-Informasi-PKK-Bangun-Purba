<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kontak - Sistem Informasi PKK</title>
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
                    <li class="nav-item"><a class="nav-link" href="/kegiatan.php"><i class="bi bi-calendar-event"></i> Kegiatan</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-active" href="/kontak.php"><i class="bi bi-envelope"></i> Kontak</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Banner -->
    <div class="hero img-hero">
        <div class="container bg-dark bg-opacity-50 p-2 rounded">
            <h1 class="fw-bold">Hubungi Kami</h1>
            <p class="lead">Informasi kontak dan form kirim pesan</p>
        </div>
    </div>

    <!-- Konten Kontak -->
    <div class="container my-5">
        <div class="row g-4">
            <!-- Info Kontak -->
            <div class="col-md-5">
                <h3 class="text-success">Informasi Kontak</h3>
                <p><i class="bi bi-geo-alt"></i> Jl. Perintis Kemerdekaan, Bangun Purba Tengah, Kec. Bangun Purba, Kabupaten Deli Serdang, Sumatera Utara 20581</p>
                <p><i class="bi bi-envelope"></i> pkkbangunpurba@gmail.com</p>
                <!-- <p><i class="bi bi-telephone"></i> 0812-3456-7890</p>
                <p><i class="bi bi-clock"></i> Senin - Jumat, 08:00 - 16:00</p> -->

                <!-- Peta Lokasi -->
                <h5 class="mt-4">Peta Lokasi</h5>
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d509810.2285515965!2d98.2399754734375!3d3.3764656000000106!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303115679f6e25d3%3A0xf07a07d143e89d8d!2sKantor%20Camat%20Bangun%20Purba!5e0!3m2!1sid!2sid!4v1755143331916!5m2!1sid!2sid"
                    width="100%" height="250" style="border:0;"
                    allowfullscreen="" loading="lazy">
                </iframe>

            </div>

            <!-- Form Kirim Pesan -->
            <div class="col-md-7">
                <h3 class="text-success">Kirim Pesan</h3>
                <form method="POST" action="proses_kontak.php">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pesan</label>
                        <textarea name="pesan" rows="5" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Kirim</button>
                </form>
            </div>
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
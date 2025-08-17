<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil - Sistem Informasi PKK</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            packages: ["orgchart"]
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Nama');
            data.addColumn('string', 'Atasan');
            data.addColumn('string', 'ToolTip');

            // Struktur Organisasi
            data.addRows([
                [{
                    v: 'ketua',
                    f: '<div style="padding:10px"><b>KETUA</b><br>Ny. Mentari Boby Arianto<br><img src="https://i.pinimg.com/736x/92/b6/3e/92b63e9f521161e94f6221560dc354d3.jpg" width="70"></div>'
                }, '', 'Ketua PKK'],
                [{
                    v: 'wakil',
                    f: '<div style="padding:10px"><b>WAKIL KETUA</b><br>Ny. Suhartati A Jamil Ritonga<br><img src="https://i.pinimg.com/736x/92/b6/3e/92b63e9f521161e94f6221560dc354d3.jpg" width="70"></div>'
                }, 'ketua', 'Wakil Ketua'],
                [{
                    v: 'sekretaris',
                    f: '<div style="padding:10px"><b>SEKRETARIS</b><br>Ny. Kartina Dahri<br><img src="https://i.pinimg.com/736x/92/b6/3e/92b63e9f521161e94f6221560dc354d3.jpg" width="70"></div>'
                }, 'wakil', 'Sekretaris'],
                [{
                    v: 'bendahara',
                    f: '<div style="padding:10px"><b>BENDAHARA</b><br>Ny. Sauli Tarigan<br><img src="https://i.pinimg.com/736x/92/b6/3e/92b63e9f521161e94f6221560dc354d3.jpg" width="70"></div>'
                }, 'wakil', 'Bendahara']
            ]);

            var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
            chart.draw(data, {
                allowHtml: true
            });
        }
    </script>
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
                    <li class="nav-item"><a class="nav-link nav-link-active" href="/profil.php"><i class="bi bi-person"></i> Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="/kegiatan.php"><i class="bi bi-calendar-event"></i> Kegiatan</a></li>
                    <li class="nav-item"><a class="nav-link" href="/kontak.php"><i class="bi bi-envelope"></i> Kontak</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Banner -->
    <div class="hero img-hero">
        <div class="container bg-dark bg-opacity-50 p-2 rounded">
            <h1 class="fw-bold">Profil PKK Bangun Purba</h1>
            <p class="lead">Mengenal lebih dekat visi, misi, dan sejarah PKK</p>
        </div>
    </div>

    <!-- Konten Profil -->
    <div class="container my-5">
        <h2 class="mb-4 text-success">Tentang Kami</h2>
        <p>
            PKK Bangun Purba adalah organisasi yang berperan aktif dalam meningkatkan kesejahteraan keluarga
            melalui berbagai kegiatan pemberdayaan, pendidikan, kesehatan, dan ekonomi masyarakat.
        </p>

        <h3 class="mt-5 text-success">Visi</h3>
        <p>
            Terwujudnya keluarga yang beriman, bertakwa, berakhlak mulia, sehat, sejahtera, maju, dan mandiri.
        </p>

        <h3 class="mt-5 text-success">Misi</h3>
        <ul>
            <li>Meningkatkan peran serta masyarakat dalam program pemberdayaan keluarga.</li>
            <li>Menumbuhkan kesadaran hidup sehat dan berkelanjutan.</li>
            <li>Meningkatkan keterampilan dan ekonomi kreatif masyarakat.</li>
        </ul>

        <h3 class=" my-5 text-success" style="text-align:center">Struktur Organisasi PKK</h3>
        <div id="chart_div"></div>
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
                        <li><a href="#"><i class="bi bi-tiktok"></i> Tiktok</a></li>
                        <li><a href="https://www.instagram.com/pkkbangunpurba/"><i class="bi bi-instagram"></i> IG</a></li>
                        <li><a href="#"><i class="bi bi-facebook"></i> FB</a></li>
                    </ul>
                </div>
                <div class="col-md-3 text-md-end">
                    <h5>Kontak</h5>
                    <p>Email: info@desa.id</p>
                </div>
            </div>
            <div class="text-center mt-3">&copy; <?php echo date('Y'); ?> Sistem Informasi PKK</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
session_start();
if (!in_array($_SESSION['role'], ['admin', 'pkk'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

// Filter tahun
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

// Ambil daftar tahun dari tabel kegiatan
$qTahun = mysqli_query($conn, "SELECT DISTINCT YEAR(tanggal) as tahun FROM kegiatan ORDER BY tahun DESC");

// Buat array nama bulan
$namaBulan = [
    1 => "Januari",
    2 => "Februari",
    3 => "Maret",
    4 => "April",
    5 => "Mei",
    6 => "Juni",
    7 => "Juli",
    8 => "Agustus",
    9 => "September",
    10 => "Oktober",
    11 => "November",
    12 => "Desember"
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Kehadiran Pertahun</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .print-area,
            .print-area * {
                visibility: visible;
            }

            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/side_bar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="m-0">Rekap Kehadiran Pertahun</h1>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">

                    <!-- Filter Tahun -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-calendar-alt"></i> Rekap Kehadiran Tahun <?= htmlspecialchars($tahun) ?>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select name="tahun" class="form-control" onchange="this.form.submit()">
                                            <?php while ($row = mysqli_fetch_assoc($qTahun)): ?>
                                                <option value="<?= $row['tahun'] ?>" <?= ($tahun == $row['tahun']) ? 'selected' : '' ?>>
                                                    <?= $row['tahun'] ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <!-- Grafik -->
                            <canvas id="grafikKehadiran" height="100"></canvas>

                            <!-- Tombol Print & PDF untuk 1 Tahun -->
                            <div class="mt-3">
                                <button class="btn btn-secondary" onclick="printAll()"><i class="fas fa-print"></i> Print 1 Tahun</button>
                                <button class="btn btn-danger" onclick="exportPDFAll()"><i class="fas fa-file-pdf"></i> Export PDF 1 Tahun</button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Perbulan -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-table"></i> Tabel Rekap Kehadiran Perbulan
                        </div>
                        <div class="card-body">
                            <?php foreach ($namaBulan as $numBulan => $nama): ?>
                                <?php
                                // Query rekap per bulan & anggota
                                $query = "
                                    SELECT 
                                        a.nama_anggota,
                                        SUM(CASE WHEN h.status = 'Hadir' THEN 1 ELSE 0 END) AS total_hadir,
                                        SUM(CASE WHEN h.status = 'Izin' THEN 1 ELSE 0 END) AS total_izin,
                                        SUM(CASE WHEN h.status = 'Tidak Hadir' THEN 1 ELSE 0 END) AS total_tidak_hadir
                                    FROM kehadiran h
                                    LEFT JOIN anggota a ON h.id_anggota = a.id_anggota
                                    LEFT JOIN kegiatan k ON h.id_kegiatan = k.id_kegiatan
                                    WHERE YEAR(k.tanggal) = $tahun AND MONTH(k.tanggal) = $numBulan
                                    GROUP BY a.nama_anggota
                                    ORDER BY a.nama_anggota
                                ";
                                $resBulan = $conn->query($query);
                                ?>

                                <div class="mt-4">
                                    <h5><i class="fas fa-calendar"></i> <?= $nama ?></h5>

                                    <!-- Tombol bulan -->
                                    <div class="mb-2">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="printTable('bulan<?= $numBulan ?>')">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="exportPDF('bulan<?= $numBulan ?>', '<?= $nama ?>', <?= $tahun ?>)">
                                            <i class="fas fa-file-pdf"></i> Export PDF
                                        </button>
                                    </div>

                                    <div class="table-responsive print-area" id="bulan<?= $numBulan ?>">
                                        <h4 class="text-center">Rekap Kehadiran <?= $nama ?> <?= $tahun ?></h4>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Anggota</th>
                                                    <th>Hadir</th>
                                                    <th>Izin</th>
                                                    <th>Tidak Hadir</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                if ($resBulan->num_rows > 0) {
                                                    while ($row = $resBulan->fetch_assoc()) {
                                                        echo "<tr>
                                                            <td>{$no}</td>
                                                            <td>" . htmlspecialchars($row['nama_anggota']) . "</td>
                                                            <td>{$row['total_hadir']}</td>
                                                            <td>{$row['total_izin']}</td>
                                                            <td>{$row['total_tidak_hadir']}</td>
                                                        </tr>";
                                                        $no++;
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='5' class='text-center'>Tidak ada data kehadiran bulan $nama</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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

    <script>
        // Print 1 tabel bulan
        function printTable(id) {
            const divContents = document.getElementById(id).innerHTML;
            const win = window.open("", "", "height=800,width=1000");
            win.document.write(`
        <html>
            <head>
                <title>Print Rekap</title>
                <link rel="stylesheet" href="../plugins/bootstrap/css/bootstrap.min.css">
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h4 { text-align: center; margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #000; padding: 8px; text-align: center; }
                    th { background: #f2f2f2; }
                </style>
            </head>
            <body>
                ${divContents}
            </body>
        </html>
    `);
            win.document.close();
            win.focus();
            win.print();
            win.close();
        }


        // Print semua tabel (1 tahun)
        function printAll() {
            const allTables = document.querySelectorAll(".print-area");
            let content = "";
            allTables.forEach(div => {
                content += div.innerHTML + "<br><br>";
            });

            const win = window.open("", "", "height=800,width=1000");
            win.document.write(`
        <html>
            <head>
                <title>Print Rekap Tahunan</title>
                <link rel="stylesheet" href="../plugins/bootstrap/css/bootstrap.min.css">
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h4 { text-align: center; margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #000; padding: 8px; text-align: center; }
                    th { background: #f2f2f2; }
                </style>
            </head>
            <body>
                <h2 class="text-center">Rekap Kehadiran Tahun <?= $tahun ?></h2>
                ${content}
            </body>
        </html>
    `);
            win.document.close();
            win.focus();
            win.print();
            win.close();
        }


        // Export PDF 1 tabel bulan
        function exportPDF(id, bulan, tahun) {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();
            doc.text(`Rekap Kehadiran ${bulan} ${tahun}`, 14, 15);
            doc.autoTable({
                html: `#${id} table`,
                startY: 20
            });
            doc.save(`rekap_${bulan}_${tahun}.pdf`);
        }

        // Export PDF semua bulan (1 tahun)
        function exportPDFAll() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();
            let y = 15;
            doc.text("Rekap Kehadiran Satu Tahun <?= $tahun ?>", 14, y);
            y += 10;

            <?php foreach ($namaBulan as $numBulan => $nama): ?>
                doc.text("<?= $nama ?>", 14, y);
                doc.autoTable({
                    html: "#bulan<?= $numBulan ?> table",
                    startY: y + 5
                });
                y = doc.lastAutoTable.finalY + 10;
            <?php endforeach; ?>

            doc.save("rekap_tahunan_<?= $tahun ?>.pdf");
        }
    </script>
</body>

</html>
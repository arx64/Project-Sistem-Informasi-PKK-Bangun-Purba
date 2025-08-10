<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pkk', 'kades'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

// Ambil semua kegiatan + join dawis
$result = $conn->query("
    SELECT k.id_kegiatan, k.nama_kegiatan, k.tanggal, k.deskripsi, k.foto, d.nama_dawis
    FROM kegiatan k
    LEFT JOIN dawis d ON k.id_dawis = d.id_dawis
    ORDER BY k.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kegiatan PKK</title>

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
                    <h1 class="m-0">Manajemen Kegiatan</h1>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">

                    <!-- Form Tambah Kegiatan -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Tambah Kegiatan</h5>
                        </div>
                        <div class="card-body">
                            <form action="kegiatan_process.php" method="POST" enctype="multipart/form-data">
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label>Dawis</label>
                                        <select name="id_dawis" class="form-control" required>
                                            <option value="">Pilih Dawis</option>
                                            <?php
                                            $dawisList = $conn->query("SELECT * FROM dawis ORDER BY nama_dawis ASC");
                                            while ($d = $dawisList->fetch_assoc()) {
                                                echo "<option value='{$d['id_dawis']}'>{$d['nama_dawis']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nama Kegiatan</label>
                                        <input type="text" name="nama_kegiatan" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control" required>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" required></textarea>
                                </div>

                                <div class="mb-2">
                                    <label>Foto</label>
                                    <input type="file" name="foto" class="form-control">
                                </div>

                                <button type="submit" name="tambah" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Data Kegiatan -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Data Kegiatan</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped table-responsive">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dawis</th>
                                        <th>Nama Kegiatan</th>
                                        <th>Tanggal</th>
                                        <th>Deskripsi</th>
                                        <th>Foto</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['nama_dawis']}</td>
                                        <td>{$row['nama_kegiatan']}</td>
                                        <td>{$row['tanggal']}</td>
                                        <td>{$row['deskripsi']}</td>
                                        <td>";
                                        if (!empty($row['foto']) && file_exists("../uploads/kegiatan/" . $row['foto'])) {
                                            echo "<img src='../uploads/kegiatan/{$row['foto']}' width='80'>";
                                        } else {
                                            echo "-";
                                        }
                                        echo "</td>
                                        <td>
                                            <a href='kegiatan_edit.php?id={$row['id_kegiatan']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
                                            <a href='kegiatan_process.php?hapus={$row['id_kegiatan']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin hapus?')\"><i class='fas fa-trash'></i></a>
                                        </td>
                                    </tr>";
                                        $no++;
                                    }
                                    ?>
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
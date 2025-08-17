<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pkk'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

// Ambil kode terakhir
$lastCodeQuery = $conn->query("SELECT kode_dawis FROM dawis ORDER BY id_dawis DESC LIMIT 1");
if ($lastCodeQuery->num_rows > 0) {
    $lastCodeRow = $lastCodeQuery->fetch_assoc();
    $lastNumber = (int)substr($lastCodeRow['kode_dawis'], 2); // ambil angka setelah DW
    $newNumber = $lastNumber + 1;
} else {
    $newNumber = 1;
}
$newKodeDawis = "DW" . str_pad($newNumber, 2, "0", STR_PAD_LEFT);

// CREATE Dawis
if (isset($_POST['tambah'])) {
    $kode_dawis = $_POST['kode_dawis'];
    $nama_dawis = $_POST['nama_dawis'];
    $rt = $_POST['rt'];
    $rw = $_POST['rw'];

    $stmt = $conn->prepare("INSERT INTO dawis (kode_dawis, nama_dawis, rt, rw) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $kode_dawis, $nama_dawis, $rt, $rw);
    $stmt->execute();
    $stmt->close();

    header("Location: /dawis");
    exit;
}

// UPDATE Dawis
if (isset($_POST['update'])) {
    $id = $_POST['id_dawis'];
    $kode_dawis = $_POST['kode_dawis'];
    $nama_dawis = $_POST['nama_dawis'];
    $rt = $_POST['rt'];
    $rw = $_POST['rw'];

    $stmt = $conn->prepare("UPDATE dawis SET kode_dawis=?, nama_dawis=?, rt=?, rw=? WHERE id_dawis=?");
    $stmt->bind_param("ssiii", $kode_dawis, $nama_dawis, $rt, $rw, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: /dawis");
    exit;
}

// DELETE Dawis
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Hapus semua anggota yang terhubung ke dawis ini
    // $stmtAnggota = $conn->prepare("DELETE FROM anggota WHERE id_dawis = ?");
    // $stmtAnggota->bind_param("i", $id);
    // $stmtAnggota->execute();

    // Baru hapus dawis
    $stmtDawis = $conn->prepare("DELETE FROM dawis WHERE id_dawis = ?");
    $stmtDawis->bind_param("i", $id);
    $stmtDawis->execute();

    // $stmt->close();

    header("Location: /dawis");
    exit;
}

        

// READ Dawis
$result = $conn->query("SELECT * FROM dawis ORDER BY id_dawis ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Dawis</title>

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
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Manajemen Dawis</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">

                    <!-- Form Tambah Dawis -->
                    <div class="card mb-4">
                        <div class="card-header">Tambah Dawis</div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="kode_dawis" class="form-control" value="<?= $newKodeDawis ?>" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="nama_dawis" class="form-control" placeholder="Nama Dawis" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="rt" class="form-control" placeholder="RT" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="rw" class="form-control" placeholder="RW" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="tambah" class="btn btn-primary"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Dawis -->
                    <div class="card">
                        <div class="card-header">Daftar Dawis</div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Kode Dawis</th>
                                        <th>Nama Dawis</th>
                                        <th>RT</th>
                                        <th>RW</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $row['id_dawis'] ?></td>
                                            <td><?= $row['kode_dawis'] ?></td>
                                            <td><?= $row['nama_dawis'] ?></td>
                                            <td><?= $row['rt'] ?></td>
                                            <td><?= $row['rw'] ?></td>
                                            <td>
                                                <a href="dawis_edit.php?id=<?= $row['id_dawis'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                <a href="?hapus=<?= $row['id_dawis'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                                            </td>
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
</body>

</html>
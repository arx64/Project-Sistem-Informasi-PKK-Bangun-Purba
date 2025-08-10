<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

// Ambil data Dawis berdasarkan ID
if (!isset($_GET['id'])) {
    header("Location: /dawis");
    exit;
}
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM dawis WHERE id_dawis = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$dawis = $result->fetch_assoc();
$stmt->close();

if (!$dawis) {
    header("Location: /dawis");
    exit;
}

// Update data Dawis
if (isset($_POST['update'])) {
    $nama_dawis = $_POST['nama_dawis'];
    $rt = $_POST['rt'];
    $rw = $_POST['rw'];

    $stmt = $conn->prepare("UPDATE dawis SET nama_dawis=?, rt=?, rw=? WHERE id_dawis=?");
    $stmt->bind_param("siii", $nama_dawis, $rt, $rw, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: /dawis");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Dawis</title>

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
                    <h1 class="m-0">Edit Dawis</h1>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">Form Edit Dawis</div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label>Kode Dawis</label>
                                        <input type="text" name="kode_dawis" class="form-control" value="<?= $dawis['kode_dawis'] ?>" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Nama Dawis</label>
                                        <input type="text" name="nama_dawis" class="form-control" value="<?= $dawis['nama_dawis'] ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label>RT</label>
                                        <input type="number" name="rt" class="form-control" value="<?= $dawis['rt'] ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label>RW</label>
                                        <input type="number" name="rw" class="form-control" value="<?= $dawis['rw'] ?>" required>
                                    </div>
                                </div>
                                <button type="submit" name="update" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
                                <a href="/dawis/" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                            </form>
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
<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pkk'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

// Ambil ID anggota
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = intval($_GET['id']);

// Ambil data anggota
$stmt = $conn->prepare("SELECT * FROM anggota WHERE id_anggota = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$anggota = $result->fetch_assoc();
$stmt->close();

if (!$anggota) {
    echo "Data anggota tidak ditemukan.";
    exit;
}

// Ambil data dawis untuk dropdown
$dawisResult = $conn->query("SELECT * FROM dawis ORDER BY nama_dawis ASC");

// Proses update data
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $id_dawis = $_POST['id_dawis'];

    $stmt = $conn->prepare("UPDATE anggota SET nama_anggota=?, alamat=?, id_dawis=? WHERE id_anggota=?");
    $stmt->bind_param("ssii", $nama, $alamat, $id_dawis, $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Data anggota berhasil diperbarui.";
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Anggota</title>

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
                    <h1 class="m-0">Edit Anggota</h1>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">Form Edit Anggota</div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($anggota['nama_anggota']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($anggota['alamat']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Dawis</label>
                                    <select name="id_dawis" class="form-control" required>
                                        <option value="">-- Pilih Dawis --</option>
                                        <?php while ($d = $dawisResult->fetch_assoc()) { ?>
                                            <option value="<?= $d['id_dawis'] ?>" <?= ($d['id_dawis'] == $anggota['id_dawis']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($d['nama_dawis']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <button type="submit" name="update" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
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
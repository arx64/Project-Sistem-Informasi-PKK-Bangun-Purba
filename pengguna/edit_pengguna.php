<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// Ambil data pengguna yang mau diedit
if (!isset($_GET['id'])) {
    header("Location: /pengguna");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$pengguna = $result->fetch_assoc();
$stmt->close();

if (!$pengguna) {
    header("Location: /pengguna");
    exit;
}

// Proses update pengguna
if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Kalau password diisi, update password juga
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE pengguna SET username = ?, password = ?, role = ? WHERE id_pengguna = ?");
        $stmt->bind_param("sssi", $username, $password, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE pengguna SET username = ?, role = ? WHERE id_pengguna = ?");
        $stmt->bind_param("ssi", $username, $role, $id);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: /pengguna");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Pengguna</title>
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
                    <h1 class="m-0">Edit Pengguna</h1>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">

                    <div class="card">
                        <div class="card-header bg-warning">
                            <i class="fas fa-edit"></i> Form Edit Pengguna
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" value="<?= htmlspecialchars($pengguna['username']) ?>" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Password Baru (kosongkan jika tidak diubah)</label>
                                    <input type="password" name="password" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="admin" <?= $pengguna['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="kades" <?= $pengguna['role'] == 'kades' ? 'selected' : '' ?>>Kades</option>
                                        <option value="pkk" <?= $pengguna['role'] == 'pkk' ? 'selected' : '' ?>>PKK</option>
                                    </select>
                                </div>

                                <button type="submit" name="update" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="/pengguna" class="btn btn-secondary">Batal</a>
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
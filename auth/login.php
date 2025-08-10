<?php
session_start();
include '../config/db.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: ../dashboard/admin_dashboard.php");
            exit;
        case 'kades':
            header("Location: ../dashboard/kades_dashboard.php");
            exit;
        case 'pkk':
            header("Location: ../dashboard/pkk_dashboard.php");
            exit;
    }
}

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    $stmt = $conn->prepare("SELECT * FROM pengguna WHERE username=? AND role=? LIMIT 1");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['id_pengguna'] = $row['id_pengguna'];
            $_SESSION['username']    = $row['username'];
            $_SESSION['role']        = $row['role'];

            switch ($row['role']) {
                case 'admin':
                    header("Location: ../dashboard/admin_dashboard.php");
                    break;
                case 'kades':
                    header("Location: ../dashboard/kades_dashboard.php");
                    break;
                case 'pkk':
                    header("Location: ../dashboard/pkk_dashboard.php");
                    break;
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username atau role tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login PKK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow p-4" style="width: 350px;">
            <img src="../assets/img/icon-pemkab.png" alt="Icon PKK" class="img-fluid mb-2" style="max-width: 100px; margin: 0 auto;">
            <h4 class="text-center mb-3">Login Sistem PKK</h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="kades">Kades</option>
                        <option value="pkk">PKK</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>

</html>
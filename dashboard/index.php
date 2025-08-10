<?php
session_start();

// Jika user belum login (tidak ada role)
if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Redirect sesuai role
switch ($_SESSION['role']) {
    case 'admin':
        header("Location: admin_dashboard.php");
        break;
    case 'kades':
        header("Location: kades_dashboard.php");
        break;
    case 'pkk':
        header("Location: pkk_dashboard.php");
        break;
    default:
        // Jika role tidak dikenali, kembalikan ke login
        session_destroy();
        header("Location: ../auth/login.php");
        break;
}
exit;

?>
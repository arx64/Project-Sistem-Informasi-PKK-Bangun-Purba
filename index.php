<?php
session_start();

if (!isset($_SESSION['role'])) {
    header('Location: /auth/login.php');
    exit();
}

switch ($_SESSION['role']) {
    case 'admin':
        header('Location: /dashboard/admin_dashboard.php');
        break;
    case 'kades':
        header('Location: /dashboard/kades_dashboard.php');
        break;
    case 'pkk':
        header('Location: /dashboard/pkk_dashboard.php');
        break;
    default:
        header('Location: /auth/login.php');
        break;
}
exit();
?>
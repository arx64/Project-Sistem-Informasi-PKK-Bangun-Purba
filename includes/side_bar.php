<?php
if (!isset($_SESSION)) {
    session_start();
}
$role = $_SESSION['role'] ?? '';
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="../assets/img/icon-pemkab.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">PKK Bangun Purba</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="../assets/img/circle-user.svg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= ucfirst($_SESSION['username']) ?? ''; ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

                <!-- Menu untuk Admin -->
                <?php if ($role == 'admin'): ?>
                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/pengguna" class="nav-link">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Manajemen Pengguna</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/anggota" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Anggota PKK</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/dawis" class="nav-link">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Dawis</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/kegiatan" class="nav-link">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Kegiatan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/laporan" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Laporan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/backup" class="nav-link">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Backup/Export</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Menu untuk Kades -->
                <?php if ($role == 'kades'): ?>
                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/anggota" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Data Anggota</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/kegiatan" class="nav-link">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Kegiatan PKK</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/laporan" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Laporan</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Menu untuk PKK -->
                <?php if ($role == 'pkk'): ?>
                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/anggota" class="nav-link">
                            <i class="nav-icon fas fa-user-plus"></i>
                            <p>Tambah Anggota</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/kegiatan" class="nav-link">
                            <i class="nav-icon fas fa-calendar-plus"></i>
                            <p>Input Kegiatan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/kehadiran" class="nav-link">
                            <i class="nav-icon fas fa-user-check"></i>
                            <p>Input Kehadiran</p>
                        </a>
                    </li>
                <?php endif; ?>


            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
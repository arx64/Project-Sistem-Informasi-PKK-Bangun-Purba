<style>
    .logout-link:hover {
        background-color: red;
        color: white;
        /* opsional agar teks tetap terbaca */
    }

    a:hover {
        background-color: #f8f9fa2c;
        /* Warna latar belakang saat hover */
    }
</style>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <!-- <i class="far fa-bell"></i> -->
                <i class="far fa-user"></i>
                <!-- <span class="badge badge-warning navbar-badge">15</span> -->
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">Profile Menu</span>
                <!-- <div class="dropdown-divider"></div>
                <a data-toggle="modal" data-target="#modalUbahPassword" class="dropdown-item">
                    Ubah Password
                </a> -->
                <!-- <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span> -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="/auth/logout.php" class="dropdown-item dropdown-footer logout-link" onclick="return confirm('Apakah anda yakin ingin keluar?');"><i class="nav-icon fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </li>

    </ul>
</nav>
<!-- /.navbar -->
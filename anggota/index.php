<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pkk', 'kades'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

// Tambah anggota
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_anggota'];
    $alamat = $_POST['alamat'];
    $id_dawis = $_POST['id_dawis'];

    $stmt = $conn->prepare("INSERT INTO anggota (nama_anggota, alamat, id_dawis) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nama, $alamat, $id_dawis);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Data anggota berhasil ditambahkan!";
    header("Location: /anggota");
    exit;
}

// Hapus anggota
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    $check = $conn->prepare("SELECT COUNT(*) FROM kehadiran WHERE id_anggota = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        $_SESSION['error'] = "Tidak bisa hapus! Anggota ini masih memiliki data kehadiran.";
    } else {
        $stmt = $conn->prepare("DELETE FROM anggota WHERE id_anggota = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Data anggota berhasil dihapus!";
    }

    header("Location: /anggota");
    exit;
}

// Ambil data anggota (query awal)
$qAnggota = $conn->query("
    SELECT a.id_anggota, a.nama_anggota, d.nama_dawis, d.rt, d.rw, a.alamat
    FROM anggota a
    LEFT JOIN dawis d ON a.id_dawis = d.id_dawis
    ORDER BY a.id_anggota DESC
");

// Ambil daftar dawis untuk dropdown
$qDawis = $conn->query("SELECT * FROM dawis ORDER BY nama_dawis ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Anggota PKK</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- Tambahkan CSS DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php include '../includes/navbar.php'; ?>
        <?php include '../includes/side_bar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <?php if (in_array($_SESSION['role'], ['admin', 'pkk'])) {
                        echo '<h1 class="m-0">Manajemen Anggota PKK</h1>';
                    } else {
                        echo '<h1 class="m-0">Data Anggota PKK</h1>';
                    } ?>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">

                    <!-- Form Tambah Anggota -->
                    <?php if (in_array($_SESSION['role'], ['admin', 'pkk'])): ?>
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-user-plus"></i> Tambah Anggota
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="form-group">
                                        <label>Nama Anggota</label>
                                        <input type="text" name="nama_anggota" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <textarea name="alamat" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Asal Dawis</label>
                                        <select name="id_dawis" class="form-control" required>
                                            <option value="">-- Pilih Dawis --</option>
                                            <?php while ($d = $qDawis->fetch_assoc()): ?>
                                                <option value="<?= $d['id_dawis'] ?>">
                                                    <?= $d['nama_dawis'] ?> (RT <?= $d['rt'] ?>/RW <?= $d['rw'] ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="tambah" class="btn btn-success">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>


                    <!-- Tabel Anggota -->
                    <div class="card mt-3">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-users"></i> Data Anggota PKK
                        </div>
                        <div class="card-body table-responsive">
                            <!-- Filter Dawis -->
                            <form method="GET" class="mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select name="dawis" class="form-control" onchange="this.form.submit()">
                                            <option value="">-- Semua Dawis --</option>
                                            <?php
                                            // Ambil daftar dawis dari database
                                            $queryDawis = mysqli_query($conn, "SELECT id_dawis, nama_dawis FROM dawis");
                                            while ($row = mysqli_fetch_assoc($queryDawis)) {
                                                $selected = (isset($_GET['dawis']) && $_GET['dawis'] == $row['id_dawis']) ? 'selected' : '';
                                                echo "<option value='{$row['id_dawis']}' $selected>{$row['nama_dawis']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <!-- Tabel Data -->
                            <table id="tabelAnggota" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anggota</th>
                                        <th>Alamat</th>
                                        <th>Dawis</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $filter = "";
                                    if (!empty($_GET['dawis'])) {
                                        $dawisId = intval($_GET['dawis']);
                                        $filter = "WHERE anggota.id_dawis = $dawisId";
                                    }

                                    $data = mysqli_query($conn, "
            SELECT anggota.*, dawis.nama_dawis 
            FROM anggota 
            JOIN dawis ON anggota.id_dawis = dawis.id_dawis 
            $filter
        ");

                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($data)) {
                                        echo "<tr>
                <td>{$no}</td>
                <td>{$row['nama_anggota']}</td>
                <td>{$row['alamat']}</td>
                <td>{$row['nama_dawis']}</td>
                <td>";

                                        if (in_array($_SESSION['role'], ['admin', 'pkk'])) {
                                            echo "<a href='anggota_edit.php?id={$row['id_anggota']}' class='btn btn-warning btn-sm'>
                        <i class='fas fa-edit'></i>
                      </a>
                      <a href=\"javascript:void(0)\" 
                         onclick=\"confirmDelete('?hapus={$row['id_anggota']}')\" 
                         class=\"btn btn-danger btn-sm\">
                         <i class=\"fas fa-trash\"></i>
                      </a>";
                                        } else {
                                            echo "<span class='text-muted'>Tidak ada aksi</span>";
                                        }

                                        echo "</td>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tambahkan JS DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabelAnggota').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                },
                "pageLength": 10
            });
        });
    </script>
    <script>
        // Konfirmasi Hapus
        function confirmDelete(url) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data anggota dan presensi terkait akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>

    <?php if (isset($_SESSION['success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= $_SESSION['success']; ?>',
                timer: 2000,
                showConfirmButton: false
            })
        </script>
    <?php unset($_SESSION['success']);
    endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '<?= $_SESSION['error']; ?>',
                confirmButtonColor: '#d33'
            })
        </script>
    <?php unset($_SESSION['error']);
    endif; ?>

</body>

</html>
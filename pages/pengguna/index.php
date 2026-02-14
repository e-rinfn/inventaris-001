<?php
require_once '../../includes/auth_check.php';
// Hanya admin yang bisa mengakses
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}

require_once '../../config/database.php';

$query = "SELECT * FROM pengguna ORDER BY nama_lengkap";
$stmt = $pdo->query($query);
$pengguna = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php include '../../includes/header.php'; ?>

<style>
    /* Paksa SweetAlert berada di atas segalanya */
    .swal2-container {
        z-index: 99999 !important;
    }
</style>


<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu -->

            <?php include '../../includes/sidebar.php'; ?>

            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->

                <?php include '../../includes/navbar.php'; ?>


                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2>Data Pengguna</h2>
                            <a href="tambah.php" class="disabled btn btn-primary">
                                <i class="bx bx-plus-circle"></i> Tambah Pengguna
                            </a>
                        </div>

                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success"><?= $_GET['success'] ?></div>
                        <?php endif; ?>
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?= $_GET['error'] ?></div>
                        <?php endif; ?>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Username</th>
                                                <th>Nama Lengkap</th>
                                                <th>Role</th>
                                                <th>Terakhir Login</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pengguna as $key => $user): ?>
                                                <tr>
                                                    <td><?= $key + 1 ?></td>
                                                    <td><?= $user['username'] ?></td>
                                                    <td><?= $user['nama_lengkap'] ?></td>
                                                    <td>
                                                        <span class="badge bg-<?=
                                                                                $user['role'] == 'admin' ? 'primary' : ($user['role'] == 'guru' ? 'success' : 'warning')
                                                                                ?>">
                                                            <?= ucfirst($user['role']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= $user['terakhir_login'] ? date('d/m/Y H:i', strtotime($user['terakhir_login'])) : 'Belum pernah' ?></td>
                                                    <td>
                                                        <a href="edit.php?id=<?= $user['id_pengguna'] ?>" class="disabled btn btn-sm btn-warning">
                                                            <i class="bx bx-pencil"></i>
                                                        </a>
                                                        <?php if ($user['id_pengguna'] != $_SESSION['id_pengguna']): ?>
                                                            <a href="hapus.php?id=<?= $user['id_pengguna'] ?>"
                                                                class="disabled btn btn-sm btn-danger btn-delete"
                                                                title="Hapus">
                                                                <i class="bx bx-trash"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- / Content -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');

            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Yakin ingin menghapus pengguna ini?.",
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
                });
            });
        });
    </script>


    <?php include '../../includes/footer.php'; ?>
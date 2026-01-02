<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit();
}

$pengguna = $pdo->query("SELECT * FROM pengguna ORDER BY nama_lengkap")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="page-header">
        <h2>Kelola Pengguna</h2>
        <a href="tambah.php" class="btn btn-success">+ Tambah Pengguna</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="card">
        <table>
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
                <?php if (empty($pengguna)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data pengguna</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pengguna as $key => $item): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= htmlspecialchars($item['username']) ?></td>
                            <td><?= htmlspecialchars($item['nama_lengkap']) ?></td>
                            <td><span class="badge badge-<?= $item['role'] == 'admin' ? 'danger' : 'info' ?>"><?= ucfirst($item['role']) ?></span></td>
                            <td><?= $item['terakhir_login'] ? date('d/m/Y H:i', strtotime($item['terakhir_login'])) : '-' ?></td>
                            <td class="text-center">
                                <div class="action-btns">
                                    <a href="edit.php?id=<?= $item['id_pengguna'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <?php if ($item['id_pengguna'] != $_SESSION['id_pengguna']): ?>
                                        <a href="hapus.php?id=<?= $item['id_pengguna'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus pengguna ini?')">Hapus</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
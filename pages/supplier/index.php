<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$supplier = $pdo->query("SELECT * FROM supplier ORDER BY nama_supplier")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="page-header">
        <h2>Data Supplier</h2>
        <a href="tambah.php" class="btn btn-success">+ Tambah Supplier</a>
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
                    <th>Nama Supplier</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($supplier)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data supplier</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($supplier as $key => $item): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= htmlspecialchars($item['nama_supplier']) ?></td>
                            <td><?= htmlspecialchars($item['alamat']) ?: '-' ?></td>
                            <td><?= htmlspecialchars($item['telepon']) ?: '-' ?></td>
                            <td class="text-center">
                                <div class="action-btns">
                                    <a href="edit.php?id=<?= $item['id_supplier'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="hapus.php?id=<?= $item['id_supplier'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus supplier ini?')">Hapus</a>
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
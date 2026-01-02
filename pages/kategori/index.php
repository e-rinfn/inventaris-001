<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$kategori = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="page-header">
        <h2>Data Kategori Barang</h2>
        <a href="tambah.php" class="btn btn-success">+ Tambah Kategori</a>
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
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kategori)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data kategori</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($kategori as $key => $item): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= htmlspecialchars($item['nama_kategori']) ?></td>
                            <td><?= htmlspecialchars($item['deskripsi']) ?: '-' ?></td>
                            <td class="text-center">
                                <div class="action-btns">
                                    <a href="edit.php?id=<?= $item['id_kategori'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="hapus.php?id=<?= $item['id_kategori'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus kategori ini?')">Hapus</a>
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
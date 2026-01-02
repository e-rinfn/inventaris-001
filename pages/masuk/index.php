<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$query = "SELECT bm.*, b.nama_barang, b.kode_barang, s.nama_supplier, p.nama_lengkap 
          FROM barang_masuk bm
          JOIN barang b ON bm.id_barang = b.id_barang
          LEFT JOIN supplier s ON bm.id_supplier = s.id_supplier
          JOIN pengguna p ON bm.id_pengguna = p.id_pengguna
          ORDER BY bm.tanggal_masuk DESC, bm.id_masuk DESC";
$transaksi = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="page-header">
        <h2>Data Barang Masuk</h2>
        <a href="tambah.php" class="btn btn-success">+ Tambah Barang Masuk</a>
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
                    <th>Tanggal</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Input Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transaksi)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transaksi as $key => $item): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= date('d/m/Y', strtotime($item['tanggal_masuk'])) ?></td>
                            <td><?= htmlspecialchars($item['kode_barang']) ?></td>
                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                            <td><?= $item['jumlah'] ?></td>
                            <td><?= htmlspecialchars($item['nama_lengkap']) ?></td>
                            <td class="text-center">
                                <div class="action-btns">
                                    <a href="edit.php?id=<?= $item['id_masuk'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="hapus.php?id=<?= $item['id_masuk'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
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
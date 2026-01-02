<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$query = "SELECT bh.*, b.nama_barang, b.kode_barang, p.nama_lengkap 
          FROM barang_hilang bh
          JOIN barang b ON bh.id_barang = b.id_barang
          JOIN pengguna p ON bh.id_pengguna = p.id_pengguna
          ORDER BY bh.tanggal_hilang DESC, bh.id_hilang DESC";
$transaksi = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="page-header">
        <h2>Data Barang Hilang</h2>
        <a href="tambah.php" class="btn btn-success">+ Tambah Data Hilang</a>
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
                    <th>Keterangan</th>
                    <th>Input Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transaksi)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transaksi as $key => $item): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= date('d/m/Y', strtotime($item['tanggal_hilang'])) ?></td>
                            <td><?= htmlspecialchars($item['kode_barang']) ?></td>
                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                            <td><?= $item['jumlah'] ?></td>
                            <td><?= htmlspecialchars($item['keterangan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($item['nama_lengkap']) ?></td>
                            <td class="text-center">
                                <a href="hapus.php?id=<?= $item['id_hilang'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
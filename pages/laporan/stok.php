<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$query = "SELECT b.*, k.nama_kategori, l.nama_lokasi 
          FROM barang b
          JOIN kategori k ON b.id_kategori = k.id_kategori
          JOIN lokasi l ON b.id_lokasi = l.id_lokasi
          ORDER BY b.nama_barang";
$barang = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="page-header">
        <h2>Laporan Stok Barang</h2>
        <div>
            <a href="cetak_stok.php" target="_blank" class="btn btn-primary">Cetak PDF</a>
            <a href="export_stok.php" class="btn btn-success">Export Excel</a>
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($barang)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($barang as $key => $item): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= htmlspecialchars($item['kode_barang']) ?></td>
                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                            <td><?= htmlspecialchars($item['nama_kategori']) ?></td>
                            <td><?= htmlspecialchars($item['nama_lokasi']) ?></td>
                            <td><?= $item['stok'] ?></td>
                            <td><?= htmlspecialchars($item['satuan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
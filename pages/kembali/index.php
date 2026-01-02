<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$query = "SELECT bk.*, b.nama_barang, b.kode_barang, p.nama_lengkap 
          FROM barang_kembali bk
          JOIN barang b ON bk.id_barang = b.id_barang
          JOIN pengguna p ON bk.id_pengguna = p.id_pengguna
          ORDER BY bk.tanggal_kembali DESC, bk.id_kembali DESC";
$transaksi = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="page-header">
        <h2>Data Barang Kembali</h2>
        <a href="tambah.php" class="btn btn-success">+ Tambah Pengembalian</a>
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
                    <th>Pengembalian Dari</th>
                    <th>Kondisi</th>
                    <th>Input Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transaksi)): ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transaksi as $key => $item): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= date('d/m/Y', strtotime($item['tanggal_kembali'])) ?></td>
                            <td><?= htmlspecialchars($item['kode_barang']) ?></td>
                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                            <td><?= $item['jumlah'] ?></td>
                            <td><?= htmlspecialchars($item['pengembalian_dari']) ?></td>
                            <td>
                                <?php
                                $kondisi = $item['kondisi'] ?? 'baik';
                                $badgeClass = $kondisi == 'baik' ? 'badge-success' : 'badge-warning';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= ucfirst($kondisi) ?></span>
                            </td>
                            <td><?= htmlspecialchars($item['nama_lengkap']) ?></td>
                            <td class="text-center">
                                <a href="hapus.php?id=<?= $item['id_kembali'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
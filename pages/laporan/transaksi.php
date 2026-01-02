<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$filter = $_GET['filter'] ?? 'semua';
$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');

$transaksi = [];

if ($filter == 'semua' || $filter == 'masuk') {
    $query = "SELECT 'Masuk' as jenis, bm.tanggal_masuk as tanggal, b.kode_barang, b.nama_barang, bm.jumlah, p.nama_lengkap
              FROM barang_masuk bm
              JOIN barang b ON bm.id_barang = b.id_barang
              JOIN pengguna p ON bm.id_pengguna = p.id_pengguna
              WHERE bm.tanggal_masuk BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$tgl_awal, $tgl_akhir]);
    $transaksi = array_merge($transaksi, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($filter == 'semua' || $filter == 'keluar') {
    $query = "SELECT 'Keluar' as jenis, bk.tanggal_keluar as tanggal, b.kode_barang, b.nama_barang, bk.jumlah, p.nama_lengkap
              FROM barang_keluar bk
              JOIN barang b ON bk.id_barang = b.id_barang
              JOIN pengguna p ON bk.id_pengguna = p.id_pengguna
              WHERE bk.tanggal_keluar BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$tgl_awal, $tgl_akhir]);
    $transaksi = array_merge($transaksi, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($filter == 'semua' || $filter == 'hilang') {
    $query = "SELECT 'Hilang' as jenis, bh.tanggal_hilang as tanggal, b.kode_barang, b.nama_barang, bh.jumlah, p.nama_lengkap
              FROM barang_hilang bh
              JOIN barang b ON bh.id_barang = b.id_barang
              JOIN pengguna p ON bh.id_pengguna = p.id_pengguna
              WHERE bh.tanggal_hilang BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$tgl_awal, $tgl_akhir]);
    $transaksi = array_merge($transaksi, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

// Sort by date
usort($transaksi, function ($a, $b) {
    return strtotime($b['tanggal']) - strtotime($a['tanggal']);
});
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="page-header">
        <h2>Laporan Transaksi</h2>
        <div>
            <a href="cetak_transaksi.php?filter=<?= $filter ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>" target="_blank" class="btn btn-primary">Cetak PDF</a>
            <a href="export_transaksi.php?filter=<?= $filter ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>" class="btn btn-success">Export Excel</a>
        </div>
    </div>

    <div class="card mb-2">
        <form method="GET" class="search-form">
            <select name="filter">
                <option value="semua" <?= $filter == 'semua' ? 'selected' : '' ?>>Semua Transaksi</option>
                <option value="masuk" <?= $filter == 'masuk' ? 'selected' : '' ?>>Barang Masuk</option>
                <option value="keluar" <?= $filter == 'keluar' ? 'selected' : '' ?>>Barang Keluar</option>
                <option value="hilang" <?= $filter == 'hilang' ? 'selected' : '' ?>>Barang Hilang</option>
            </select>
            <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>">
            <span>s/d</span>
            <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Operator</th>
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
                            <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                            <td>
                                <span class="badge badge-<?= $item['jenis'] == 'Masuk' ? 'success' : ($item['jenis'] == 'Keluar' ? 'warning' : 'danger') ?>">
                                    <?= $item['jenis'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($item['kode_barang']) ?></td>
                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                            <td><?= $item['jumlah'] ?></td>
                            <td><?= htmlspecialchars($item['nama_lengkap']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
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

usort($transaksi, function ($a, $b) {
    return strtotime($b['tanggal']) - strtotime($a['tanggal']);
});

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_transaksi_' . date('Y-m-d') . '.xls"');
?>
<table border="1">
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Jenis</th>
        <th>Kode</th>
        <th>Nama Barang</th>
        <th>Jumlah</th>
        <th>Operator</th>
    </tr>
    <?php foreach ($transaksi as $key => $item): ?>
        <tr>
            <td><?= $key + 1 ?></td>
            <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
            <td><?= $item['jenis'] ?></td>
            <td><?= htmlspecialchars($item['kode_barang']) ?></td>
            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
            <td><?= $item['jumlah'] ?></td>
            <td><?= htmlspecialchars($item['nama_lengkap']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
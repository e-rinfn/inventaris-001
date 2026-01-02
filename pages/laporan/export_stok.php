<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$query = "SELECT b.kode_barang, b.nama_barang, k.nama_kategori, l.nama_lokasi, b.stok, b.satuan
          FROM barang b
          JOIN kategori k ON b.id_kategori = k.id_kategori
          JOIN lokasi l ON b.id_lokasi = l.id_lokasi
          ORDER BY b.nama_barang";
$barang = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_stok_' . date('Y-m-d') . '.xls"');
?>
<table border="1">
    <tr>
        <th>No</th>
        <th>Kode</th>
        <th>Nama Barang</th>
        <th>Kategori</th>
        <th>Lokasi</th>
        <th>Stok</th>
        <th>Satuan</th>
    </tr>
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
</table>
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
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Stok Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f39c12;
            color: white;
        }

        .text-center {
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <h1>LAPORAN STOK BARANG</h1>
    <p class="text-center">Tanggal Cetak: <?= date('d/m/Y H:i') ?></p>

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
        </tbody>
    </table>

    <button class="no-print" onclick="window.print()" style="margin-top: 20px; padding: 10px 20px;">Cetak</button>
</body>

</html>
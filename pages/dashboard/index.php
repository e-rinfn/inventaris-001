<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

// Hitung total barang
$total_barang = $pdo->query("SELECT COUNT(*) FROM barang")->fetchColumn();

// Hitung total kategori
$total_kategori = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();

// Hitung total lokasi
$total_lokasi = $pdo->query("SELECT COUNT(*) FROM lokasi")->fetchColumn();

// Hitung transaksi hari ini
$query_transaksi = "SELECT 
    (SELECT COUNT(*) FROM barang_masuk WHERE DATE(tanggal_masuk) = CURDATE()) as masuk,
    (SELECT COUNT(*) FROM barang_keluar WHERE DATE(tanggal_keluar) = CURDATE()) as keluar,
    (SELECT COUNT(*) FROM barang_hilang WHERE DATE(tanggal_hilang) = CURDATE()) as hilang";
$transaksi = $pdo->query($query_transaksi)->fetch(PDO::FETCH_ASSOC);

// Barang stok minimum
$stok_minimum = $pdo->query("SELECT b.nama_barang, b.stok, b.satuan, k.nama_kategori 
                             FROM barang b
                             JOIN kategori k ON b.id_kategori = k.id_kategori
                             WHERE b.stok < 5
                             ORDER BY b.stok ASC
                             LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Riwayat transaksi terakhir
$query_riwayat = "SELECT 
                    'masuk' as jenis, bm.tanggal_masuk as tanggal, b.nama_barang, bm.jumlah, u.nama_lengkap as operator
                  FROM barang_masuk bm
                  JOIN barang b ON bm.id_barang = b.id_barang
                  JOIN pengguna u ON bm.id_pengguna = u.id_pengguna
                  UNION ALL
                  SELECT 
                    'keluar' as jenis, bk.tanggal_keluar as tanggal, b.nama_barang, bk.jumlah, u.nama_lengkap as operator
                  FROM barang_keluar bk
                  JOIN barang b ON bk.id_barang = b.id_barang
                  JOIN pengguna u ON bk.id_pengguna = u.id_pengguna
                  UNION ALL
                  SELECT 
                    'hilang' as jenis, bh.tanggal_hilang as tanggal, b.nama_barang, bh.jumlah, u.nama_lengkap as operator
                  FROM barang_hilang bh
                  JOIN barang b ON bh.id_barang = b.id_barang
                  JOIN pengguna u ON bh.id_pengguna = u.id_pengguna
                  ORDER BY tanggal DESC
                  LIMIT 10";
$riwayat_transaksi = $pdo->query($query_riwayat)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
  <?php include '../../includes/navbar.php'; ?>

  <h2 class="mb-2">Dashboard</h2>

  <!-- Statistik -->
  <div class="row">
    <div class="col-4">
      <div class="stat-card">
        <h4>Total Jenis Barang</h4>
        <div class="number"><?= $total_barang ?></div>
        <a href="../barang/index.php">Lihat detail →</a>
      </div>
    </div>
    <div class="col-4">
      <div class="stat-card">
        <h4>Total Kategori</h4>
        <div class="number"><?= $total_kategori ?></div>
        <a href="../kategori/index.php">Lihat detail →</a>
      </div>
    </div>
    <div class="col-4">
      <div class="stat-card">
        <h4>Total Lokasi</h4>
        <div class="number"><?= $total_lokasi ?></div>
        <a href="../lokasi/index.php">Lihat detail →</a>
      </div>
    </div>
  </div>

  <!-- Transaksi Hari Ini -->
  <div class="card">
    <div class="card-title">Transaksi Hari Ini</div>
    <div class="row">
      <div class="col-4">
        <strong>Barang Masuk:</strong> <?= $transaksi['masuk'] ?? 0 ?>
      </div>
      <div class="col-4">
        <strong>Barang Keluar:</strong> <?= $transaksi['keluar'] ?? 0 ?>
      </div>
      <div class="col-4">
        <strong>Barang Hilang:</strong> <?= $transaksi['hilang'] ?? 0 ?>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Stok Minimum -->
    <div class="col-6">
      <div class="card">
        <div class="card-title">Stok Minimum (< 5)</div>
            <?php if (empty($stok_minimum)): ?>
              <p>Tidak ada barang dengan stok minimum</p>
            <?php else: ?>
              <table>
                <thead>
                  <tr>
                    <th>Barang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($stok_minimum as $item): ?>
                    <tr>
                      <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                      <td><?= htmlspecialchars($item['nama_kategori']) ?></td>
                      <td><?= $item['stok'] ?> <?= $item['satuan'] ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
        </div>
      </div>

      <!-- Riwayat Transaksi -->
      <div class="col-6">
        <div class="card">
          <div class="card-title">Riwayat Transaksi Terakhir</div>
          <?php if (empty($riwayat_transaksi)): ?>
            <p>Belum ada transaksi</p>
          <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Jenis</th>
                  <th>Barang</th>
                  <th>Jumlah</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($riwayat_transaksi as $item): ?>
                  <tr>
                    <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                    <td>
                      <span class="badge badge-<?= $item['jenis'] == 'masuk' ? 'success' : ($item['jenis'] == 'keluar' ? 'warning' : 'danger') ?>">
                        <?= ucfirst($item['jenis']) ?>
                      </span>
                    </td>
                    <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                    <td><?= $item['jumlah'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <?php include '../../includes/footer.php'; ?>
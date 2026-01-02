<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../config/config.php');
require_once 'auth_check.php';

$current_uri = $_SERVER['REQUEST_URI'];

function isActive($path)
{
  global $current_uri;
  return strpos($current_uri, $path) !== false ? 'active' : '';
}
?>

<div class="sidebar">
  <div class="logo">
    <img src="<?= $base_url ?>/assets/img/Logo.png" alt="Logo">
    <h3>Inventaris Barang</h3>
  </div>

  <ul>
    <li><a href="<?= $base_url ?>/pages/dashboard/index.php" class="<?= isActive('/dashboard') ?>">Dashboard</a></li>

    <li class="menu-header">Master Data</li>
    <li><a href="<?= $base_url ?>/pages/barang/index.php" class="<?= isActive('/barang') ?>">Data Barang</a></li>
    <li><a href="<?= $base_url ?>/pages/kategori/index.php" class="<?= isActive('/kategori') ?>">Kategori</a></li>
    <li><a href="<?= $base_url ?>/pages/lokasi/index.php" class="<?= isActive('/lokasi') ?>">Lokasi</a></li>

    <li class="menu-header">Siklus Barang</li>
    <li><a href="<?= $base_url ?>/pages/masuk/index.php" class="<?= isActive('/masuk') ?>">Barang Masuk</a></li>
    <li><a href="<?= $base_url ?>/pages/keluar/index.php" class="<?= isActive('/keluar') ?>">Barang Dipinjam</a></li>
    <li><a href="<?= $base_url ?>/pages/kembali/index.php" class="<?= isActive('/kembali') ?>">Barang Kembali</a></li>
    <li><a href="<?= $base_url ?>/pages/hilang/index.php" class="<?= isActive('/hilang') ?>">Barang Hilang</a></li>

    <li class="menu-header">Laporan</li>
    <li><a href="<?= $base_url ?>/pages/laporan/stok.php" class="<?= isActive('/laporan/stok') ?>">Laporan Stok</a></li>
    <li><a href="<?= $base_url ?>/pages/laporan/transaksi.php" class="<?= isActive('/laporan/transaksi') ?>">Laporan Transaksi</a></li>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
      <li class="menu-header">Admin</li>
      <li><a href="<?= $base_url ?>/pages/pengguna/index.php" class="<?= isActive('/pengguna') ?>">Kelola Pengguna</a></li>
    <?php endif; ?>
  </ul>
</div>
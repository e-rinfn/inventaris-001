<?php
require_once '../../../includes/auth_check.php';
require_once '../../../config/database.php';

$id_keluar = $_GET['id'] ?? 0;

$id_keluar = $_GET['id'];

try {
    // Mulai transaksi
    $pdo->beginTransaction();

    // 1. Ambil data barang keluar untuk mendapatkan informasi yang diperlukan
    $query_select = "SELECT bk.*, b.id_barang, b.stok, 
                    (SELECT COALESCE(SUM(bk2.jumlah), 0) 
                     FROM barang_kembali bk2 
                     WHERE bk2.id_keluar = bk.id_keluar) AS jumlah_kembali
                     FROM barang_keluar bk 
                     JOIN barang b ON bk.id_barang = b.id_barang 
                     WHERE bk.id_keluar = ?";
    $stmt_select = $pdo->prepare($query_select);
    $stmt_select->execute([$id_keluar]);
    $barang_keluar = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if (!$barang_keluar) {
        throw new Exception("Data barang keluar tidak ditemukan");
    }

    // 2. Validasi - tidak bisa menghapus jika sudah ada pengembalian
    if ($barang_keluar['jumlah_kembali'] > 0) {
        throw new Exception("Tidak dapat menghapus data barang keluar yang sudah memiliki pengembalian");
    }

    // 3. Validasi hak akses - hanya admin atau user yang membuat yang bisa menghapus
    if ($_SESSION['role'] !== 'admin' && $_SESSION['id_pengguna'] !== $barang_keluar['id_pengguna']) {
        throw new Exception("Anda tidak memiliki izin untuk menghapus data ini");
    }

    // 4. Tambahkan stok barang kembali
    $new_stok = $barang_keluar['stok'] + $barang_keluar['jumlah'];

    $query_update_stok = "UPDATE barang SET stok = ? WHERE id_barang = ?";
    $stmt_update_stok = $pdo->prepare($query_update_stok);
    $stmt_update_stok->execute([$new_stok, $barang_keluar['id_barang']]);

    // 5. Hapus data barang keluar
    $query_delete = "DELETE FROM barang_keluar WHERE id_keluar = ?";
    $stmt_delete = $pdo->prepare($query_delete);
    $stmt_delete->execute([$id_keluar]);

    // Commit transaksi
    $pdo->commit();

    header('Location: index.php?success=Data barang keluar berhasil dihapus dan stok barang telah dikembalikan');
    exit();
} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    $pdo->rollBack();

    header('Location: index.php?error=' . urlencode($e->getMessage()));
    exit();
}

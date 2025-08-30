<?php
require_once '../../../includes/auth_check.php';
require_once '../../../config/database.php';

// Pastikan hanya admin atau user yang membuat yang bisa menghapus
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?error=ID tidak valid');
    exit();
}

$id_kembali = $_GET['id'];

try {
    // Mulai transaksi
    $pdo->beginTransaction();

    // 1. Ambil data barang kembali untuk mendapatkan informasi yang diperlukan
    $query_select = "SELECT bk.*, b.id_barang, b.stok, bk2.jumlah as jumlah_keluar
                     FROM barang_kembali bk 
                     JOIN barang b ON bk.id_barang = b.id_barang 
                     JOIN barang_keluar bk2 ON bk.id_keluar = bk2.id_keluar
                     WHERE bk.id_kembali = ?";
    $stmt_select = $pdo->prepare($query_select);
    $stmt_select->execute([$id_kembali]);
    $barang_kembali = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if (!$barang_kembali) {
        throw new Exception("Data barang kembali tidak ditemukan");
    }

    // 2. Validasi hak akses - hanya admin atau user yang membuat yang bisa menghapus
    if ($_SESSION['role'] !== 'admin' && $_SESSION['id_pengguna'] !== $barang_kembali['id_pengguna']) {
        throw new Exception("Anda tidak memiliki izin untuk menghapus data ini");
    }

    // 3. Kurangi stok barang (karena pengembalian akan dihapus)
    $new_stok = $barang_kembali['stok'] - $barang_kembali['jumlah'];

    // Jika barang rusak, kita perlu menyesuaikan logika stok
    if ($barang_kembali['kondisi'] === 'rusak_berat') {
        // Untuk barang rusak berat, stok tidak bertambah saat dikembalikan
        // Jadi saat dihapus, tidak perlu mengurangi stok
        $new_stok = $barang_kembali['stok'];
    }

    $query_update_stok = "UPDATE barang SET stok = ? WHERE id_barang = ?";
    $stmt_update_stok = $pdo->prepare($query_update_stok);
    $stmt_update_stok->execute([$new_stok, $barang_kembali['id_barang']]);

    // 4. Hapus data barang kembali
    $query_delete = "DELETE FROM barang_kembali WHERE id_kembali = ?";
    $stmt_delete = $pdo->prepare($query_delete);
    $stmt_delete->execute([$id_kembali]);

    // Commit transaksi
    $pdo->commit();

    header('Location: index.php?success=Data barang kembali berhasil dihapus dan stok barang telah disesuaikan');
    exit();
} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header('Location: index.php?error=' . urlencode($e->getMessage()));
    exit();
}

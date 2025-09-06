<?php
require_once '../../../includes/auth_check.php';
require_once '../../../config/database.php';

// 1. Validasi Role dan Autentikasi
// if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
//     $_SESSION['error'] = "Anda tidak memiliki izin untuk melakukan operasi ini";
//     header("Location: ../../dashboard/index.php");
//     exit();
// }

// 2. Validasi Input ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID transaksi tidak valid";
    header("Location: index.php");
    exit();
}

$id_masuk = $_GET['id'];

try {
    // Mulai transaksi
    $pdo->beginTransaction();

    // 1. Ambil data barang masuk untuk mendapatkan informasi yang diperlukan
    $query_select = "SELECT bm.*, b.id_barang, b.stok 
                     FROM barang_masuk bm 
                     JOIN barang b ON bm.id_barang = b.id_barang 
                     WHERE bm.id_masuk = ?";
    $stmt_select = $pdo->prepare($query_select);
    $stmt_select->execute([$id_masuk]);
    $barang_masuk = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if (!$barang_masuk) {
        throw new Exception("Data barang masuk tidak ditemukan");
    }

    // 2. Validasi hak akses - hanya admin atau user yang membuat yang bisa menghapus
    if ($_SESSION['role'] !== 'admin' && $_SESSION['id_pengguna'] !== $barang_masuk['id_pengguna']) {
        throw new Exception("Anda tidak memiliki izin untuk menghapus data ini");
    }

    // 3. Kurangi stok barang (jika transaksi sudah mempengaruhi stok)
    $new_stok = $barang_masuk['stok'] - $barang_masuk['jumlah'];

    $query_update_stok = "UPDATE barang SET stok = ? WHERE id_barang = ?";
    $stmt_update_stok = $pdo->prepare($query_update_stok);
    $stmt_update_stok->execute([$new_stok, $barang_masuk['id_barang']]);

    // 4. Hapus data barang masuk
    $query_delete = "DELETE FROM barang_masuk WHERE id_masuk = ?";
    $stmt_delete = $pdo->prepare($query_delete);
    $stmt_delete->execute([$id_masuk]);

    // Commit transaksi
    $pdo->commit();

    header('Location: index.php?success=Data barang masuk berhasil dihapus');
    exit();
} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    $pdo->rollBack();

    header('Location: index.php?error=' . urlencode($e->getMessage()));
    exit();
}

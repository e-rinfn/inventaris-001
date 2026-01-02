<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT * FROM barang_keluar WHERE id_keluar = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        // Kembalikan stok
        $stmt = $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang = ?");
        $stmt->execute([$data['jumlah'], $data['id_barang']]);

        $stmt = $pdo->prepare("DELETE FROM barang_keluar WHERE id_keluar = ?");
        $stmt->execute([$id]);
    }

    $pdo->commit();
    header("Location: index.php?success=Data+berhasil+dihapus");
} catch (PDOException $e) {
    $pdo->rollBack();
    header("Location: index.php?error=Gagal+menghapus+data");
}
exit();

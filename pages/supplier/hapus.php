<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_supplier = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM supplier WHERE id_supplier = ?");
    $stmt->execute([$id_supplier]);
    header("Location: index.php?success=Supplier+berhasil+dihapus");
} catch (PDOException $e) {
    header("Location: index.php?error=Gagal+menghapus+supplier");
}
exit();

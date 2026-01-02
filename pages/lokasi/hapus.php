<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_lokasi = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM lokasi WHERE id_lokasi = ?");
    $stmt->execute([$id_lokasi]);
    header("Location: index.php?success=Lokasi+berhasil+dihapus");
} catch (PDOException $e) {
    header("Location: index.php?error=Gagal+menghapus+lokasi");
}
exit();

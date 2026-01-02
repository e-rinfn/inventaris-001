<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_pengguna = $_GET['id'];

if ($id_pengguna == $_SESSION['id_pengguna']) {
    header("Location: index.php?error=Tidak+bisa+menghapus+diri+sendiri");
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
    $stmt->execute([$id_pengguna]);
    header("Location: index.php?success=Pengguna+berhasil+dihapus");
} catch (PDOException $e) {
    header("Location: index.php?error=Gagal+menghapus+pengguna");
}
exit();

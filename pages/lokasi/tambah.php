<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    header("Location: ../dashboard/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lokasi = $_POST['nama_lokasi'];
    $deskripsi = $_POST['deskripsi'] ?: null;

    try {
        $stmt = $pdo->prepare("INSERT INTO lokasi (nama_lokasi, deskripsi) VALUES (?, ?)");
        $stmt->execute([$nama_lokasi, $deskripsi]);
        header("Location: index.php?success=Lokasi+berhasil+ditambahkan");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal menambahkan lokasi: " . $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <h2>Tambah Lokasi</h2>
    <a href="index.php" class="btn btn-secondary mb-1">← Kembali</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="nama_lokasi">Nama Lokasi *</label>
                <input type="text" class="form-control" id="nama_lokasi" name="nama_lokasi" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
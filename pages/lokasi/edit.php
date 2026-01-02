<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    header("Location: ../dashboard/index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_lokasi = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM lokasi WHERE id_lokasi = ?");
$stmt->execute([$id_lokasi]);
$lokasi = $stmt->fetch();

if (!$lokasi) {
    header("Location: index.php?error=Lokasi+tidak+ditemukan");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lokasi = $_POST['nama_lokasi'];
    $deskripsi = $_POST['deskripsi'] ?: null;

    try {
        $stmt = $pdo->prepare("UPDATE lokasi SET nama_lokasi = ?, deskripsi = ? WHERE id_lokasi = ?");
        $stmt->execute([$nama_lokasi, $deskripsi, $id_lokasi]);
        header("Location: index.php?success=Lokasi+berhasil+diupdate");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal mengupdate lokasi: " . $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <h2>Edit Lokasi</h2>
    <a href="index.php" class="btn btn-secondary mb-1">← Kembali</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="nama_lokasi">Nama Lokasi *</label>
                <input type="text" class="form-control" id="nama_lokasi" name="nama_lokasi" value="<?= htmlspecialchars($lokasi['nama_lokasi']) ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($lokasi['deskripsi']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
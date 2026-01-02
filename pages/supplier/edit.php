<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_supplier = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM supplier WHERE id_supplier = ?");
$stmt->execute([$id_supplier]);
$supplier = $stmt->fetch();

if (!$supplier) {
    header("Location: index.php?error=Supplier+tidak+ditemukan");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supplier = $_POST['nama_supplier'];
    $alamat = $_POST['alamat'] ?: null;
    $telepon = $_POST['telepon'] ?: null;

    try {
        $stmt = $pdo->prepare("UPDATE supplier SET nama_supplier = ?, alamat = ?, telepon = ? WHERE id_supplier = ?");
        $stmt->execute([$nama_supplier, $alamat, $telepon, $id_supplier]);
        header("Location: index.php?success=Supplier+berhasil+diupdate");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal mengupdate supplier: " . $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <h2>Edit Supplier</h2>
    <a href="index.php" class="btn btn-secondary mb-1">← Kembali</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="nama_supplier">Nama Supplier *</label>
                <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" value="<?= htmlspecialchars($supplier['nama_supplier']) ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($supplier['alamat']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="telepon">Telepon</label>
                <input type="text" class="form-control" id="telepon" name="telepon" value="<?= htmlspecialchars($supplier['telepon']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
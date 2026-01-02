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

$id_kategori = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
$stmt->execute([$id_kategori]);
$kategori = $stmt->fetch();

if (!$kategori) {
    header("Location: index.php?error=Kategori+tidak+ditemukan");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = $_POST['nama_kategori'];
    $deskripsi = $_POST['deskripsi'] ?: null;

    try {
        $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?");
        $stmt->execute([$nama_kategori, $deskripsi, $id_kategori]);
        header("Location: index.php?success=Kategori+berhasil+diupdate");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal mengupdate kategori: " . $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <h2>Edit Kategori</h2>
    <a href="index.php" class="btn btn-secondary mb-1">← Kembali</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="nama_kategori">Nama Kategori *</label>
                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?= htmlspecialchars($kategori['nama_kategori']) ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($kategori['deskripsi']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
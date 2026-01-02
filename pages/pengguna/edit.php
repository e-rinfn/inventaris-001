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
$stmt = $pdo->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
$stmt->execute([$id_pengguna]);
$pengguna = $stmt->fetch();

if (!$pengguna) {
    header("Location: index.php?error=Pengguna+tidak+ditemukan");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $role = $_POST['role'];

    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE pengguna SET username = ?, password = ?, nama_lengkap = ?, role = ? WHERE id_pengguna = ?");
            $stmt->execute([$username, $password, $nama_lengkap, $role, $id_pengguna]);
        } else {
            $stmt = $pdo->prepare("UPDATE pengguna SET username = ?, nama_lengkap = ?, role = ? WHERE id_pengguna = ?");
            $stmt->execute([$username, $nama_lengkap, $role, $id_pengguna]);
        }
        header("Location: index.php?success=Pengguna+berhasil+diupdate");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal mengupdate pengguna: " . $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <h2>Edit Pengguna</h2>
    <a href="index.php" class="btn btn-secondary mb-1">← Kembali</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($pengguna['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password (kosongkan jika tidak diubah)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap *</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($pengguna['nama_lengkap']) ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role *</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="staff" <?= $pengguna['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="admin" <?= $pengguna['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
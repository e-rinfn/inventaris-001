<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$id_pengguna = $_SESSION['id_pengguna'];
$stmt = $pdo->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
$stmt->execute([$id_pengguna]);
$pengguna = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];

    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE pengguna SET nama_lengkap = ?, password = ? WHERE id_pengguna = ?");
            $stmt->execute([$nama_lengkap, $password, $id_pengguna]);
        } else {
            $stmt = $pdo->prepare("UPDATE pengguna SET nama_lengkap = ? WHERE id_pengguna = ?");
            $stmt->execute([$nama_lengkap, $id_pengguna]);
        }
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $success = "Profil berhasil diupdate";

        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
        $stmt->execute([$id_pengguna]);
        $pengguna = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Gagal mengupdate profil: " . $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <h2>Profil Saya</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($pengguna['username']) ?>" disabled>
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap *</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($pengguna['nama_lengkap']) ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <input type="text" class="form-control" value="<?= ucfirst($pengguna['role']) ?>" disabled>
            </div>
            <div class="form-group">
                <label for="password">Password Baru (kosongkan jika tidak diubah)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Update Profil</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
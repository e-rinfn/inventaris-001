<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM barang_keluar WHERE id_keluar = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    header("Location: index.php?error=Data+tidak+ditemukan");
    exit();
}

$barang = $pdo->query("SELECT * FROM barang ORDER BY nama_barang")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];
    $jumlah_baru = $_POST['jumlah'];
    $tanggal_keluar = $_POST['tanggal_keluar'];
    $penerima = $_POST['penerima'];
    $keterangan = $_POST['keterangan'] ?? null;

    try {
        $pdo->beginTransaction();

        // Update stok: kembalikan stok lama, kurangi stok baru
        $selisih = $data['jumlah'] - $jumlah_baru;
        $stmt = $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang = ?");
        $stmt->execute([$selisih, $id_barang]);

        $stmt = $pdo->prepare("UPDATE barang_keluar SET id_barang = ?, jumlah = ?, tanggal_keluar = ?, penerima = ?, keterangan = ? WHERE id_keluar = ?");
        $stmt->execute([$id_barang, $jumlah_baru, $tanggal_keluar, $penerima, $keterangan, $id]);

        $pdo->commit();
        header("Location: index.php?success=Data+berhasil+diupdate");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Gagal: " . $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <h2>Edit Peminjaman</h2>
    <a href="index.php" class="btn btn-secondary mb-1">← Kembali</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="id_barang">Barang *</label>
                <select class="form-control" id="id_barang" name="id_barang" required>
                    <option value="">Pilih Barang</option>
                    <?php foreach ($barang as $item): ?>
                        <option value="<?= $item['id_barang'] ?>" <?= $data['id_barang'] == $item['id_barang'] ? 'selected' : '' ?>><?= htmlspecialchars($item['kode_barang'] . ' - ' . $item['nama_barang']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="jumlah">Jumlah *</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" value="<?= $data['jumlah'] ?>" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="tanggal_keluar">Tanggal *</label>
                        <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar" value="<?= $data['tanggal_keluar'] ?>" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="penerima">Penerima/Peminjam *</label>
                <input type="text" class="form-control" id="penerima" name="penerima" value="<?= htmlspecialchars($data['penerima']) ?>" required>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
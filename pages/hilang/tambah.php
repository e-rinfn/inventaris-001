<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$barang = $pdo->query("SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $tanggal_hilang = $_POST['tanggal_hilang'];
    $keterangan = $_POST['keterangan'] ?? null;

    // Cek stok
    $stmt = $pdo->prepare("SELECT stok FROM barang WHERE id_barang = ?");
    $stmt->execute([$id_barang]);
    $stok = $stmt->fetchColumn();

    if ($jumlah > $stok) {
        $error = "Jumlah melebihi stok tersedia ($stok)";
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO barang_hilang (id_barang, jumlah, tanggal_hilang, keterangan, id_pengguna) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_barang, $jumlah, $tanggal_hilang, $keterangan, $_SESSION['id_pengguna']]);

            $stmt = $pdo->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ?");
            $stmt->execute([$jumlah, $id_barang]);

            $pdo->commit();
            header("Location: index.php?success=Data+berhasil+ditambahkan");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Gagal: " . $e->getMessage();
        }
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <h2>Tambah Barang Hilang</h2>
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
                        <option value="<?= $item['id_barang'] ?>"><?= htmlspecialchars($item['kode_barang'] . ' - ' . $item['nama_barang'] . ' (Stok: ' . $item['stok'] . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="jumlah">Jumlah *</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="tanggal_hilang">Tanggal *</label>
                        <input type="date" class="form-control" id="tanggal_hilang" name="tanggal_hilang" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Jelaskan kronologi/alasan hilang"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
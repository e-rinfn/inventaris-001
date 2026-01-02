<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$barang = $pdo->query("SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $tanggal_keluar = $_POST['tanggal_keluar'];
    $penerima = $_POST['penerima'];
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

            $stmt = $pdo->prepare("INSERT INTO barang_keluar (id_barang, jumlah, tanggal_keluar, penerima, keterangan, id_pengguna) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_barang, $jumlah, $tanggal_keluar, $penerima, $keterangan, $_SESSION['id_pengguna']]);

            $stmt = $pdo->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ?");
            $stmt->execute([$jumlah, $id_barang]);

            $pdo->commit();
            header("Location: index.php?success=Peminjaman+berhasil+ditambahkan");
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

    <h2>Tambah Peminjaman Barang</h2>
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
                        <label for="tanggal_keluar">Tanggal *</label>
                        <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="penerima">Penerima/Peminjam *</label>
                <input type="text" class="form-control" id="penerima" name="penerima" required>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
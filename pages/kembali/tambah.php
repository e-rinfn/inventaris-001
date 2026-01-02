<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$barang = $pdo->query("SELECT * FROM barang ORDER BY nama_barang")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $pengembalian_dari = $_POST['pengembalian_dari'];
    $kondisi = $_POST['kondisi'];
    $keterangan = $_POST['keterangan'] ?? null;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO barang_kembali (id_barang, jumlah, tanggal_kembali, pengembalian_dari, kondisi, keterangan, id_pengguna) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_barang, $jumlah, $tanggal_kembali, $pengembalian_dari, $kondisi, $keterangan, $_SESSION['id_pengguna']]);

        // Tambah stok jika kondisi baik
        if ($kondisi == 'baik') {
            $stmt = $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang = ?");
            $stmt->execute([$jumlah, $id_barang]);
        }

        $pdo->commit();
        header("Location: index.php?success=Pengembalian+berhasil+ditambahkan");
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

    <h2>Tambah Pengembalian Barang</h2>
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
                        <option value="<?= $item['id_barang'] ?>"><?= htmlspecialchars($item['kode_barang'] . ' - ' . $item['nama_barang']) ?></option>
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
                        <label for="tanggal_kembali">Tanggal *</label>
                        <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="pengembalian_dari">Pengembalian Dari *</label>
                <input type="text" class="form-control" id="pengembalian_dari" name="pengembalian_dari" required>
            </div>
            <div class="form-group">
                <label for="kondisi">Kondisi *</label>
                <select class="form-control" id="kondisi" name="kondisi" required>
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                </select>
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
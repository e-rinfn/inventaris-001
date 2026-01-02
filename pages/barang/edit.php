<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    header("Location: ../dashboard/index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=ID+tidak+valid");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM barang WHERE id_barang = ?");
$stmt->execute([$id]);
$barang = $stmt->fetch();

if (!$barang) {
    header("Location: index.php?error=Barang+tidak+ditemukan");
    exit();
}

$kategori = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
$lokasi = $pdo->query("SELECT * FROM lokasi ORDER BY nama_lokasi")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $id_lokasi = $_POST['id_lokasi'];
    $stok = $_POST['stok'] ?? 0;
    $satuan = $_POST['satuan'];
    $kondisi = $_POST['kondisi'] ?? 'baik';
    $keterangan = $_POST['keterangan'] ?? null;
    $gambar = $barang['gambar'];

    $uploadDir = dirname(__DIR__, 2) . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Hapus gambar jika dicentang
    if (isset($_POST['hapus_gambar']) && $_POST['hapus_gambar'] == "1") {
        if ($gambar && file_exists($uploadDir . $gambar)) {
            unlink($uploadDir . $gambar);
        }
        $gambar = null;
    }

    // Upload gambar baru
    if (!empty($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($_FILES['gambar']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('img_') . '.' . strtolower($ext);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $fileName)) {
                // Hapus gambar lama jika ada
                if ($barang['gambar'] && file_exists($uploadDir . $barang['gambar'])) {
                    unlink($uploadDir . $barang['gambar']);
                }
                $gambar = $fileName;
            }
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE barang SET kode_barang = ?, nama_barang = ?, id_kategori = ?, id_lokasi = ?, stok = ?, satuan = ?, kondisi = ?, keterangan = ?, gambar = ? WHERE id_barang = ?");
        $stmt->execute([$kode_barang, $nama_barang, $id_kategori, $id_lokasi, $stok, $satuan, $kondisi, $keterangan, $gambar, $id]);

        header("Location: index.php?success=Barang+berhasil+diupdate");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal mengupdate barang: " . $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <h2>Edit Barang</h2>
    <a href="index.php" class="btn btn-secondary mb-1">← Kembali</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" class="form-control" id="kode_barang" name="kode_barang" value="<?= htmlspecialchars($barang['kode_barang']) ?>" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang *</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="id_kategori">Kategori *</label>
                        <select class="form-control" id="id_kategori" name="id_kategori" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategori as $item): ?>
                                <option value="<?= $item['id_kategori'] ?>" <?= $barang['id_kategori'] == $item['id_kategori'] ? 'selected' : '' ?>><?= htmlspecialchars($item['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="id_lokasi">Lokasi *</label>
                        <select class="form-control" id="id_lokasi" name="id_lokasi" required>
                            <option value="">Pilih Lokasi</option>
                            <?php foreach ($lokasi as $item): ?>
                                <option value="<?= $item['id_lokasi'] ?>" <?= $barang['id_lokasi'] == $item['id_lokasi'] ? 'selected' : '' ?>><?= htmlspecialchars($item['nama_lokasi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" min="0" value="<?= htmlspecialchars($barang['stok']) ?>">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="satuan">Satuan *</label>
                        <input type="text" class="form-control" id="satuan" name="satuan" value="<?= htmlspecialchars($barang['satuan']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= htmlspecialchars($barang['keterangan']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="gambar">Gambar Barang</label>
                <?php if ($barang['gambar']): ?>
                    <div class="mb-1">
                        <img src="../../uploads/<?= htmlspecialchars($barang['gambar']) ?>" class="img-preview" alt="Gambar">
                        <br>
                        <label><input type="checkbox" name="hapus_gambar" value="1"> Hapus gambar</label>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
            </div>

            <input type="hidden" name="kondisi" value="<?= htmlspecialchars($barang['kondisi']) ?>">

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
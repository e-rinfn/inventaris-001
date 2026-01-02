<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    header("Location: ../dashboard/index.php");
    exit();
}

$kategori = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
$lokasi = $pdo->query("SELECT * FROM lokasi ORDER BY nama_lokasi")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = trim($_POST['kode_barang']);
    $nama_barang = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $id_lokasi = $_POST['id_lokasi'];
    $stok = $_POST['stok'] ?? 0;
    $satuan = $_POST['satuan'];
    $kondisi = $_POST['kondisi'] ?? 'baik';
    $keterangan = $_POST['keterangan'] ?? null;

    // Upload gambar
    $gambar = null;
    if (!empty($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__DIR__, 2) . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($_FILES['gambar']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('img_') . '.' . strtolower($ext);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $fileName)) {
                $gambar = $fileName;
            }
        }
    }

    try {
        if (empty($kode_barang)) {
            $prefix = 'BRG';
            $last_code = $pdo->query("SELECT MAX(kode_barang) as last_code FROM barang WHERE kode_barang LIKE '$prefix%'")->fetch();
            $last_num = $last_code['last_code'] ? intval(substr($last_code['last_code'], strlen($prefix))) : 0;
            $kode_barang = $prefix . str_pad($last_num + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $stmt_check = $pdo->prepare("SELECT id_barang FROM barang WHERE kode_barang = ?");
            $stmt_check->execute([$kode_barang]);
            if ($stmt_check->rowCount() > 0) {
                throw new Exception("Kode barang sudah digunakan.");
            }
        }

        $stmt = $pdo->prepare("INSERT INTO barang (kode_barang, nama_barang, id_kategori, id_lokasi, stok, satuan, kondisi, keterangan, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$kode_barang, $nama_barang, $id_kategori, $id_lokasi, $stok, $satuan, $kondisi, $keterangan, $gambar]);

        header("Location: index.php?success=Barang+berhasil+ditambahkan");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <h2>Tambah Barang</h2>
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
                        <input type="text" class="form-control" id="kode_barang" name="kode_barang" placeholder="Kosongkan untuk otomatis">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang *</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
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
                                <option value="<?= $item['id_kategori'] ?>"><?= htmlspecialchars($item['nama_kategori']) ?></option>
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
                                <option value="<?= $item['id_lokasi'] ?>"><?= htmlspecialchars($item['nama_lokasi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="stok">Stok Awal</label>
                        <input type="number" class="form-control" id="stok" name="stok" min="0" value="0">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="satuan">Satuan *</label>
                        <input type="text" class="form-control" id="satuan" name="satuan" value="pcs" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
            </div>

            <!-- <div class="form-group">
                <label for="gambar">Gambar Barang</label>
                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
            </div> -->

            <input type="hidden" name="kondisi" value="baik">

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
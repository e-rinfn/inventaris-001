<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

// Parameter pencarian dan halaman
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$selectedLokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(b.nama_barang LIKE :search OR b.kode_barang LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($selectedLokasi)) {
    $where[] = "b.id_lokasi = :lokasi";
    $params[':lokasi'] = $selectedLokasi;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total
$countQuery = "SELECT COUNT(*) FROM barang b 
               JOIN kategori k ON b.id_kategori = k.id_kategori 
               JOIN lokasi l ON b.id_lokasi = l.id_lokasi 
               $whereClause";
$stmt = $pdo->prepare($countQuery);
$stmt->execute($params);
$totalRows = $stmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Get data
$query = "SELECT b.*, k.nama_kategori, l.nama_lokasi 
          FROM barang b 
          JOIN kategori k ON b.id_kategori = k.id_kategori 
          JOIN lokasi l ON b.id_lokasi = l.id_lokasi 
          $whereClause 
          ORDER BY b.nama_barang 
          LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$barang = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all lokasi for filter
$allLokasi = $pdo->query("SELECT * FROM lokasi ORDER BY nama_lokasi")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <?php include '../../includes/navbar.php'; ?>

    <div class="page-header">
        <h2>Data Barang</h2>
        <div>
            <a href="tambah.php" class="btn btn-success">+ Tambah Barang</a>
            <a href="../laporan/stok.php" class="btn btn-warning">Laporan</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- Filter & Search -->
    <form method="GET" class="search-form">
        <select name="limit" onchange="this.form.submit()">
            <?php foreach ([5, 10, 25, 50, 100] as $opt): ?>
                <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?> data</option>
            <?php endforeach; ?>
        </select>

        <select name="lokasi" onchange="this.form.submit()">
            <option value="">Semua Lokasi</option>
            <?php foreach ($allLokasi as $lokasi): ?>
                <option value="<?= $lokasi['id_lokasi'] ?>" <?= $selectedLokasi == $lokasi['id_lokasi'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($lokasi['nama_lokasi']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="search" placeholder="Cari barang..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Cari</button>
        <a href="index.php" class="btn btn-secondary">Reset</a>
    </form>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <!-- <th>Gambar</th> -->
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($barang)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data barang</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($barang as $key => $item): ?>
                        <tr>
                            <td><?= ($offset + $key + 1) ?></td>
                            <td><?= htmlspecialchars($item['kode_barang']) ?></td>
                            <!-- <td class="text-center">
                                <?php if (!empty($item['gambar'])): ?>
                                    <img src="../../uploads/<?= htmlspecialchars($item['gambar']) ?>" class="img-thumbnail" alt="Gambar">
                                <?php else: ?>
                                    <span>-</span>
                                <?php endif; ?>
                            </td> -->
                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                            <td><?= htmlspecialchars($item['nama_kategori']) ?></td>
                            <td><?= htmlspecialchars($item['nama_lokasi']) ?></td>
                            <td><?= htmlspecialchars($item['stok']) ?> <?= htmlspecialchars($item['satuan']) ?></td>
                            <td class="text-center">
                                <div class="action-btns">
                                    <a href="edit.php?id=<?= $item['id_barang'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="hapus.php?id=<?= $item['id_barang'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus barang ini?')">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li><a href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page - 1 ?>&lokasi=<?= $selectedLokasi ?>">« Prev</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="<?= $i == $page ? 'active' : '' ?>">
                        <a href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $i ?>&lokasi=<?= $selectedLokasi ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li><a href="?search=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page + 1 ?>&lokasi=<?= $selectedLokasi ?>">Next »</a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
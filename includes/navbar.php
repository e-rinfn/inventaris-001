<div class="navbar">
    <div class="navbar-left">
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
            <span class="toggle-icon">☰</span>
        </button>
        <strong>Sistem Inventaris Barang</strong>
    </div>
    <div class="user-info">
        <span>Hai, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']) ?></span>
        <div class="avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
        <a href="<?= $base_url ?>/pages/auth/profile.php">Profil</a> |
        <a style="background-color: red;" href="<?= $base_url ?>/pages/auth/logout.php">Logout</a>
    </div>
</div>
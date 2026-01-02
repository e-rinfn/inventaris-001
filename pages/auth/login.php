<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  require_once '../../config/database.php';

  $username = $_POST['username'];
  $password = $_POST['password'];

  try {
    $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['id_pengguna'] = $user['id_pengguna'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
      $_SESSION['role'] = $user['role'];

      $update = $pdo->prepare("UPDATE pengguna SET terakhir_login = NOW() WHERE id_pengguna = ?");
      $update->execute([$user['id_pengguna']]);

      header("Location: ../dashboard/index.php");
      exit();
    } else {
      $error = "Username atau password salah!";
    }
  } catch (PDOException $e) {
    $error = "Terjadi kesalahan: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistem Inventaris</title>
  <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/favicon.ico">
  <link rel="stylesheet" href="../../assets/css/simple.css">
</head>

<body>
  <div class="login-wrapper">
    <div class="login-box">
      <div class="logo">
        <img src="../../assets/img/Logo.png" alt="Logo">
        <h1>INVENTARIS BARANG<br>Sekolah</h1>
      </div>

      <h2 class="text-center mb-2">LOGIN</h2>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username"
            placeholder="Masukan username" required autofocus
            value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password"
            placeholder="Masukan password" required>
        </div>

        <button type="submit" class="btn btn-warning" style="width: 100%;">Login</button>
      </form>
    </div>
  </div>
</body>

</html>
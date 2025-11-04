<?php
session_start();  // ← AKTIFKAN INI (sudah di-uncomment)
require_once '../../Connection/connect.php';

// AMBIL KONEKSI DARI connect.php
$conn = getConnection();  // <— WAJIB, biar $conn ada

$error = "";

// ====== CEK APAKAH SUDAH LOGIN ======
// Kalau sudah login, langsung redirect ke dashboard (index.php)
if (isset($_SESSION['admin_id']) && isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// ====== PROSES LOGIN ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username === '' || $password === '') {
    $error = 'Isi username dan password.';
  } else {
    $sql  = "SELECT admin_id, username, password FROM admins WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
 
      if (hash_equals($row['password'], $password)) {
        $_SESSION['admin_id']  = $row['admin_id'];   
        $_SESSION['username']  = $row['username'];
        session_regenerate_id(true);
        header("Location: index.php");
        exit;
      }
    }
    $error = 'Username atau password salah.';
  }
}

$conn->close();

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link rel="stylesheet" href="../cssadmin/login.css">
</head>
<body>
  
  <div class="login-container">
    <div class="login-box">
      <h2 id="greeting"></h2>

      <form method="POST" autocomplete="off">
        <div class="input-group">
          <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-group">
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn-login">LOGIN</button>

        <?php if ($error): ?>
          <div class="alert"><?= h($error) ?></div>
        <?php endif; ?>
      </form>
    </div>
  </div>
  <script src="../jsadmin/login.js"></script>
</body>
</html>
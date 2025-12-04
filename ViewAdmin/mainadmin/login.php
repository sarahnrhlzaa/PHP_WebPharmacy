<?php
session_start();
require_once '../../Connection/connect.php';

$conn = getConnection();
$error = "";

// Cek jika sudah login
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Isi username dan password.';
    } else {
        $stmt = $conn->prepare("SELECT admin_id, username, password FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            // Cek Password (Plain text sesuai database Anda)
            if ($password === $row['password']) {
                $_SESSION['admin_id']  = $row['admin_id'];
                
                // PERBAIKAN UTAMA DI SINI: Gunakan 'admin_username'
                $_SESSION['admin_username']  = $row['username'];
                
                header("Location: index.php");
                exit;
            }
        }
        $error = 'Username atau password salah.';
        $stmt->close();
    }
}

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
closeConnection($conn);
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
      <h2 id="greeting">Welcome Back</h2>
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
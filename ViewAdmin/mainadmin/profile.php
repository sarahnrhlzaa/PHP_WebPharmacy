<?php
require_once '../../Connection/connect.php';

/* --- start session via navbar, tapi tahan output --- */
ob_start();
include 'navbar.php';          // ini manggil session_start()
$NAVBAR_HTML = ob_get_clean(); // simpan HTML-nya

// sekarang sesi SUDAH aktif, baru cek guard
if (empty($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit;
}

$admin_id = $_SESSION['admin_id'];
$username = $_SESSION['username'] ?? 'Admin';

$conn = getConnection();

// ====== HANDLE POST (UPDATE) ======
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $new_email = trim($_POST['email'] ?? '');
  $new_phone = trim($_POST['phone'] ?? '');

  if ($new_email === '') {
    $sql = "UPDATE admins SET email = NULL, phone = ? WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_phone, $admin_id);
  } else {
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
      header("Location: profile.php?error=Email%20tidak%20valid");
      closeConnection($conn);
      exit;
    }
    $sql = "UPDATE admins SET email = ?, phone = ? WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $new_email, $new_phone, $admin_id);
  }

  if ($stmt->execute()) {
    $_SESSION['email'] = $new_email ?: null;
    $_SESSION['phone'] = $new_phone ?: null;
    header("Location: profile.php?updated=1");
    closeConnection($conn);
    exit;
  } else {
    header("Location: profile.php?error=Gagal%20update%20profile");
    closeConnection($conn);
    exit;
  }
}

// ====== GET DATA ======
$sql = "SELECT admin_id, username, email, phone FROM admins WHERE admin_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$admin = $res->fetch_assoc()) {
  session_destroy();
  header("Location: login.php");
  closeConnection($conn);
  exit;
}

closeConnection($conn);

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Profile</title>
  <link rel="stylesheet" href="../cssadmin/profile.css">
  <script>
    function showNotification(message, type) {
      const n = document.createElement('div');
      n.className = 'notification ' + (type || 'success');
      n.textContent = message;
      document.body.appendChild(n);
      setTimeout(() => { n.style.opacity = 0; setTimeout(()=>n.remove(), 400); }, 3000);
    }
    window.onload = function () {
      <?php if (isset($_GET['updated'])): ?> showNotification("Profile updated successfully!", "success"); <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>   showNotification("<?= h($_GET['error']) ?>", "error");        <?php endif; ?>
    };
  </script>
</head>
<body>
  <?= $NAVBAR_HTML ?>
  <div class="profile-container">
    <h2>Welcome, <?= h($username) ?>!</h2>

    <div class="profile-info">
      <h3>Contact Info</h3>
      <p><strong>Email:</strong> <?= h($admin['email']) ?></p>
      <p><strong>Phone:</strong> <?= h($admin['phone']) ?></p>
    </div>

    <h3>Update Your Info</h3>
    <form method="POST">
      <label for="email">New Email:</label>
      <input type="email" id="email" name="email" value="<?= h($admin['email']) ?>">

      <label for="phone">New Phone:</label>
      <input type="text" id="phone" name="phone" value="<?= h($admin['phone']) ?>">

      <button type="submit">Update</button>
    </form>
  </div>
</body>
</html>

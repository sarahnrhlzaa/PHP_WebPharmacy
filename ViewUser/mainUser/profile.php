<?php
session_start();

// Include koneksi ke database
include '../../Connection/connect.php'; 

// Ambil data dari session
$username = $_SESSION['username'] ?? 'user123';
$fullname = $_SESSION['fullname'] ?? '';
$email    = $_SESSION['email'] ?? '';
$phone    = $_SESSION['phone'] ?? '';
$birth    = $_SESSION['birth'] ?? '2000-01-01';
$gender   = $_SESSION['gender'] ?? 'Laki-laki';
$city     = $_SESSION['city'] ?? '';
$province = $_SESSION['province'] ?? '';
$address  = $_SESSION['address'] ?? '';

// Update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    // Simpan data ke session
    $_SESSION['fullname'] = $_POST['fullname'];
    $_SESSION['phone'] = $_POST['phone'];
    $_SESSION['birth'] = $_POST['birth'];
    $_SESSION['gender'] = $_POST['gender'];
    $_SESSION['city'] = $_POST['city'];
    $_SESSION['province'] = $_POST['province'];
    $_SESSION['address'] = $_POST['address'];

    // Update data di database
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone_number = ?, birth_date = ?, gender = ?, city = ?, province = ?, address = ? WHERE username = ?");
    $stmt->bind_param('ssssssss', $_SESSION['fullname'], $_SESSION['phone'], $_SESSION['birth'], $_SESSION['gender'], $_SESSION['city'], $_SESSION['province'], $_SESSION['address'], $username);

    if ($stmt->execute()) {
        // Jika update berhasil
        header('Location: profile.php'); // Refresh halaman setelah submit
        exit();
    } else {
        echo "Terjadi kesalahan saat mengupdate data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
  integrity="sha512-KzuW8vKJzF6yWvJmQH+O4RHE0Z4ZLkkQ9Y+MCQjA7MYxR6A9TDPkGl+K94Rn3Z6zJzMKP1XK0C9S2XG4dK5nKw==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="stylesheet" href="../cssuser/profile.css">
  <title>User Profile</title>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="profile-container">
  <h1>My Profile</h1>
  <form method="POST">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" readonly value="<?php echo htmlspecialchars($username); ?>">
    </div>

    <div class="form-group">
      <label for="fullname">Nama Lengkap</label>
      <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($fullname); ?>" readonly>
    </div>

    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" readonly value="<?php echo htmlspecialchars($email); ?>">
    </div>

    <div class="form-group">
      <label for="phone">Nomor Telepon</label>
      <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>" readonly>
    </div>

    <div class="form-group">
      <label for="birth">Tanggal Lahir</label>
      <input type="date" name="birth" id="birth" value="<?php echo htmlspecialchars($birth); ?>" readonly>
    </div>

    <div class="form-group">
      <label for="gender">Jenis Kelamin</label>
      <select name="gender" id="gender" disabled>
        <option value="Laki-laki" <?php if ($gender == 'Laki-laki') echo 'selected'; ?>>Laki-laki</option>
        <option value="Perempuan" <?php if ($gender == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
      </select>
    </div>

    <div class="form-group">
      <label for="city">Kota</label>
      <input type="text" name="city" id="city" value="<?php echo htmlspecialchars($city); ?>" readonly>
    </div>

    <div class="form-group">
      <label for="province">Provinsi</label>
      <input type="text" name="province" id="province" value="<?php echo htmlspecialchars($province); ?>" readonly>
    </div>

    <div class="form-group">
      <label for="address">Alamat</label>
      <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($address); ?>" readonly>
    </div>

    <div class="button-group">
      <button type="button" class="edit-btn" id="editBtn">
        <i class="fa fa-pen"></i> Edit
      </button>
      <button type="submit" name="save" class="save-btn" id="saveBtn" style="display: none;">
        <i class="fa fa-save"></i> Save
      </button>
      <button type="button" class="cancel-btn" id="cancelBtn" style="display: none;">
        <i class="fa fa-times"></i> Cancel
      </button>
    </div>
  </form>
</div>

<script src="../jsUser/profile.js"></script>
</body>
</html>

<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../../Connection/connect.php';
$conn = getConnection();

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

// PROSES UPDATE DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $p_fullname = trim($_POST['fullname'] ?? '');
    $p_phone    = trim($_POST['phone'] ?? '');
    $p_birth    = trim($_POST['birth'] ?? '');
    $p_gender   = trim($_POST['gender'] ?? '');
    $p_city     = trim($_POST['city'] ?? '');
    $p_province = trim($_POST['province'] ?? '');
    $p_address  = trim($_POST['address'] ?? '');

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone_number = ?, birth_date = ?, gender = ?, city = ?, province = ?, address = ? WHERE user_id = ?");
    
    $stmt->bind_param('ssssssss', $p_fullname, $p_phone, $p_birth, $p_gender, $p_city, $p_province, $p_address, $user_id);

    if ($stmt->execute()) {
        $message = "Profil berhasil diperbarui!";
        $messageType = "success";
        
        $_SESSION['user_full_name'] = $p_fullname;
    } else {
        $message = "Gagal memperbarui profil: " . $conn->error;
        $messageType = "error";
    }
    $stmt->close();
}

// AMBIL DATA TERBARU DARI DATABASE
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();
closeConnection($conn);

if (!$userData) {
    echo "User data not found.";
    exit();
}

$username = $userData['username'];
$email    = $userData['email'];
$fullname = $userData['full_name'] ?? '';
$phone    = $userData['phone_number'] ?? '';
$birth    = $userData['birth_date'] ?? '';
$gender   = $userData['gender'] ?? '';
$city     = $userData['city'] ?? '';
$province = $userData['province'] ?? '';
$address  = $userData['address'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
  <link rel="stylesheet" href="../cssuser/profile.css">
  <title>User Profile</title>
  <style>
      .alert {
          padding: 15px;
          margin-bottom: 20px;
          border-radius: 5px;
          text-align: center;
          font-weight: bold;
      }
      .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
      .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="profile-container">
  <h1>My Profile</h1>

  <?php if (!empty($message)): ?>
      <div class="alert <?= $messageType ?>">
          <?= htmlspecialchars($message) ?>
      </div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" value="<?php echo htmlspecialchars($username); ?>" readonly style="background-color: #e9ecef;">
    </div>

    <div class="form-group">
      <label for="fullname">Nama Lengkap</label>
      <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($fullname); ?>" readonly>
    </div>

    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly style="background-color: #e9ecef;">
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
        <option value="">-- Pilih --</option>
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
      <textarea name="address" id="address" rows="3" readonly><?php echo htmlspecialchars($address); ?></textarea>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const originalValues = {
        fullname: "<?= htmlspecialchars($fullname) ?>",
        phone: "<?= htmlspecialchars($phone) ?>",
        birth: "<?= htmlspecialchars($birth) ?>",
        gender: "<?= htmlspecialchars($gender) ?>",
        city: "<?= htmlspecialchars($city) ?>",
        province: "<?= htmlspecialchars($province) ?>",
        address: `<?= str_replace(["\r", "\n"], '', addslashes($address)) ?>`
    };

    document.getElementById('cancelBtn').addEventListener('click', function() {
        document.getElementById('fullname').value = originalValues.fullname;
        document.getElementById('phone').value = originalValues.phone;
        document.getElementById('birth').value = originalValues.birth;
        document.getElementById('gender').value = originalValues.gender;
        document.getElementById('city').value = originalValues.city;
        document.getElementById('province').value = originalValues.province;
        document.getElementById('address').value = originalValues.address;
    });
});
</script>

</body>
</html>
<?php
session_start();

// Include koneksi database - sesuaikan path
if (file_exists(__DIR__ . '/../connection/connect.php')) {
    include __DIR__ . '/../connection/connect.php';
} elseif (file_exists(__DIR__ . '/../../connection/connect.php')) {
    include __DIR__ . '/../../connection/connect.php';
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . '/project2_q1/connection/connect.php')) {
    include $_SERVER['DOCUMENT_ROOT'] . '/project2_q1/connection/connect.php';
} else {
    die("Error: File connect.php tidak ditemukan!");
}

if (!empty($_SESSION['user_id'])) {
    $next = $_GET['next'] ?? 'index.php';
    header('Location: ' . $next);
    exit();
}

$signin_error = '';
$signup_error = '';
$active_panel = '';

// ========== PROSES SIGN IN ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        // Cek apakah koneksi ada
        if (!isset($conn)) {
            $signin_error = 'Database connection error!';
        } else {
            // Query ke database
            $stmt = $conn->prepare("SELECT user_id, username, email, password, full_name FROM users WHERE username = ?");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Verifikasi password
                $passwordMatch = false;
                
                // Coba verify dengan hash dulu
                if (password_verify($password, $user['password'])) {
                    $passwordMatch = true;
                } 
                // Kalau gagal, coba compare plain text
                elseif ($password === $user['password']) {
                    $passwordMatch = true;
                }

                if ($passwordMatch) {
                    // Set session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['full_name'] = $user['full_name'] ?? '';

                    $next = $_GET['next'] ?? 'index.php';
                    header('Location: ' . $next);
                    exit();
                } else {
                    $signin_error = 'Incorrect username or password!';
                }
            } else {
                $signin_error = 'Incorrect username or password!';
            }
            $stmt->close();
        }
    } else {
        $signin_error = 'Please fill in all fields!';
    }
}

// ========== PROSES SIGN UP ==========

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = 'Invalid email format!';
        $active_panel = 'right-panel-active';
    } elseif (strlen($password) < 6) {
        $signup_error = 'Password must be at least 6 characters!';
        $active_panel = 'right-panel-active';
    } else {
        // Cek apakah username atau email sudah ada
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $signup_error = 'Username or email already exists!';
            $active_panel = 'right-panel-active';
        } else {
            // Generate user_id dengan format CUSxxx
            // Ambil nilai terakhir dari user_id dengan format 'CUSxxx'
            $query = "SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1";
            $result = $conn->query($query);
            $row = $result->fetch_assoc();

            // Jika sudah ada data, buat user_id baru dengan format CUS001, CUS002, dst
            if ($row) {
                $last_id = $row['user_id'];
                // Ambil angka terakhir setelah 'CUS'
                $last_number = (int) substr($last_id, 3);
                $new_id = 'CUS' . str_pad($last_number + 1, 3, '0', STR_PAD_LEFT); // Format CUS001, CUS002, ...
            } else {
                $new_id = 'CUS001'; // Jika belum ada data, mulai dari CUS001
            }

            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user baru ke database
            $stmt = $conn->prepare("INSERT INTO users (user_id, username, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param('ssss', $new_id, $username, $email, $password_hash);

            if ($stmt->execute()) {
                // Set session setelah berhasil register
                $_SESSION['user_id'] = $new_id;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;

                $next = $_GET['next'] ?? 'index.php';
                header('Location: ' . $next);
                exit();
            } else {
                $signup_error = 'Registration failed. Please try again!';
                $active_panel = 'right-panel-active';
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../cssuser/login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
    integrity="sha512-KzuW8vKJzF6yWvJmQH+O4RHE0Z4ZLkkQ9Y+MCQjA7MYxR6A9TDPkGl+K94Rn3Z6zJzMKP1XK0C9S2XG4dK5nKw==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Login Page</title>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container <?= $active_panel ?>" id="container">
    
 <!-- form -->
  <div class="container" id="container">
<!-- sign up-->
    <div class="form sign-up-container">
        <form action="" method="POST">
            <h1>Create An Account</h1>
            <!--icons-->
            <div class="social-container">
                <a href="#" class="social"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                <a href="#" class="social"><i class="fa-brands fa-google" aria-hidden="true"></i></a>
                <a href="#" class="social"><i class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
            </div>

            <!-- icons -->
            <span>or use your email for registration</span>

            <?php if (!empty($signup_error)): ?>
                <div class="error-message">
                    <i class="fa fa-exclamation-circle"></i>
                    <?= htmlspecialchars($signup_error) ?>
                </div>
            <?php endif; ?>

            <!-- input field -->
            <div class="input-field">
                <i class="fa fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-field">
                <i class="fa fa-envelope"></i>
                <input type="text" name="email" placeholder="Email" required>
            </div>

            <div class="input-field">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required minlength="6">
            </div>

            <button type="submit" name="signup">Sign Up</button>

        </form>    
    </div>

<!-- Sign In -->
    <div class="form sign-in-container">
        <form action="" method="POST">
            <h1>Login</h1>
            <!--icons-->
            <div class="social-container">
                <a href="#" class="social"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                <a href="#" class="social"><i class="fa-brands fa-google" aria-hidden="true"></i></a>
                <a href="#" class="social"><i class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
            </div>

            <!-- icons -->
             <span>or use your account</span>

            <!-- input field -->
            <div class="input-field">
                <i class="fa fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-field">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <!--forgot pass -->
            <a href="#" class="forgot-password">Forgot your password?</a>

             <!-- ERROR MESSAGE SIGNIN -->
            <?php if (!empty($signin_error)): ?>
                <div class="error-message">
                    <i class="fa fa-exclamation-circle"></i>
                    <?= htmlspecialchars($signin_error) ?>
                </div>
            <?php endif; ?>

            <button type="submit" name="signin">Log in</button>
        </form>    
    </div>


    <!-- overlay -->
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Welcome Back</h1>
                <p>To keep connected with us please login with your personal info</p>
                <img src="../../assets/signin.png" style="height: 25rem; width: 30rem; padding-top: 5%">
                <button class="btn" id="signin">Log in</button>
            </div>

            <div class="overlay-panel overlay-right">
                <h1>Hello User!</h1>
                <p>Enter your personal details and start your healthy with us</p>
                <img src="../../assets/signup.png" style="height: 25rem; width: 30rem; padding-top: 5%">
                <button class="btn" id="signup"> Sign up</button>
            </div>
        </div>
    </div>

  </div>

  <script src="../jsUser/login.js"></script>
</body>
</html>
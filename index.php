<?php
session_start();

// Cek jika ada parameter role dari URL
if (isset($_GET['role'])) {
    $role = $_GET['role'];
    
    if ($role === 'admin') {
        $_SESSION['user_role'] = 'admin';
        header('Location: ViewAdmin/mainadmin/index.php');
        exit();
    } elseif ($role === 'user') {
        $_SESSION['user_role'] = 'user';
        header('Location: ViewUser/mainUser/index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy System - Pilih Role</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">💊</div>
            <h1>Pharmacy Management System</h1>
            <p class="subtitle">Silakan pilih role Anda untuk melanjutkan</p>
        </div>

        <div class="role-container">
            <!-- Card Admin -->
            <a href="?role=admin" class="role-card-link">
                <div class="role-card admin">
                    <span class="role-icon">👨‍💼</span>
                    <div class="role-title">Admin</div>
                    <div class="role-description">
                        Kelola inventory, users, dan monitoring sistem secara lengkap
                    </div>
                </div>
            </a>

            <!-- Card User -->
            <a href="?role=user" class="role-card-link">
                <div class="role-card user">
                    <span class="role-icon">👤</span>
                    <div class="role-title">User</div>
                    <div class="role-description">
                        Akses katalog obat, buat pesanan, dan lacak riwayat transaksi
                    </div>
                </div>
            </a>
        </div>

        <div class="footer">
            <?php echo date('Y'); ?> Pharmacy System. All rights reserved.
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
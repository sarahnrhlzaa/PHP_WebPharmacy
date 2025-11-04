<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$isLoggedIn = !empty($_SESSION['user_id']);
$username  = $isLoggedIn ? ($_SESSION['username'] ?? '') : '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../cssuser/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Navbar</title>
</head>
<body>
    <!-- header -->
<header class = "header">

    <a class ="logo">
        <img src = "../../assets/logo.png" alt = "Logo">
    </a> 

    <!-- nav bar -->
    <nav class = "headbar">
        <a href = "index.php"> Home </a>
        <a href = "medicine.php"> Medicine </a>
        <a href = "contact.php"> Contact </a>
        <a href = "outlet.php"> Outlet </a>
    </nav>


<!-- MENU KANAN -->
<div class="nav-right">
    <?php if ($isLoggedIn): ?>
        <!-- ICON SEARCH, HISTORY, CART -->
    <div class="search-container">
            <button class="icon-btn" onclick="toggleSearch()" title="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

        <!-- jadi form GET ke medicine.php -->
        <form id="search-bar" class="search-bar" action="/php_webpharmacy/viewuser/mainuser/medicine.php" method="get">
          <input type="text" id="searchInput" name="q" placeholder="Cari obat..." />
          <button type="submit" id="searchBtn">Search</button>
        </form>
    </div>

            <!-- <div id="search-bar" class="search-bar">
                <input type="text" id="searchInput" placeholder="Search..." />
                <button id="searchBtn">Search</button>
            </div>
    </div> -->

        <a href="history.php" title="History">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </a>
        <a href="cart.php" title="Keranjang">
            <i class="fa-solid fa-cart-shopping"></i>
            <!-- <span class="cart-badge" style="display: none;">0</span> -->
        </a>

        <!-- PROFILE + NAMA USER -->
    <div class="user-menu">
          <div class="user-info" id="userDropdownToggle" title="Profile">
            <i class="fa-solid fa-user-circle"></i>
            <span>Hi, <?= htmlspecialchars($username) ?></span>
            <i class="fa-solid fa-caret-down"></i>
          </div>

          <div class="dropdown" id="userDropdown">
            <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
            <hr>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
          </div>
        </div>
    
    <?php else: ?>
        <!-- BELUM LOGIN = ADA BUTTON LOGIN -->
        <a href="login.php" class="login-btn">Login</a>
    <?php endif; ?>

</div>
</header>

<script src="../jsUser/navbar.js"></script>
</body>
</html>
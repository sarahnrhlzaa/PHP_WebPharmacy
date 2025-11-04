<?php
declare(strict_types=1);
session_start();

// Hapus semua session
$_SESSION = [];

// Hancurkan session
session_destroy();

// Redirect ke login
header('Location: login.php');
exit;
?>
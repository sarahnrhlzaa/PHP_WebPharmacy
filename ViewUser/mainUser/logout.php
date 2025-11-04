<?php
session_start();        // Mulai session
session_unset();        // Hapus semua variabel session
session_destroy();      // Hancurkan session

// Redirect ke halaman home / login
header("Location: index.php");
exit();
?>

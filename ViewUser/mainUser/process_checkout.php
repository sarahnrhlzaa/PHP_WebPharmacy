<?php
// BARIS 1: WAJIB MULAI SESI
die("Test")
session_start();

// BARIS 2: PANGGIL KONEKSI
// (Sesuaikan path ini agar SAMA PERSIS seperti di checkout.php)
require_once '../../Connection/connect.php'; 

// Cek apakah user sudah login
if (empty($_SESSION['user_id'])) {
    // Jika belum, tendang ke login
    header('Location: login.php?next=checkout.php');
    exit;
}

// Cek apakah tombol "place_order" ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {

    // Ambil metode pembayaran yang dipilih
    $payment_method = $_POST['payment_method'] ?? 'transfer'; // default 'transfer'
    
    //
    // --- Di sini kamu HARUS memasukkan order ke database ---
    // (Contoh sederhana, ini harus kamu sesuaikan)
    //
    // $conn = getConnection();
    // $user_id = $_SESSION['user_id'];
    // $total_price = 10000; // Ambil dari session cart
    // $status = ($payment_method === 'cod') ? 'processing' : 'pending_payment';
    //
    // $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, payment_method, status) VALUES (?, ?, ?, ?)");
    // $stmt->bind_param('sdss', $user_id, $total_price, $payment_method, $status);
    // $stmt->execute();
    // $order_id = $conn->insert_id; // Ambil ID order barunya
    //
    
    // --- INI LOGIKA REDIRECT-NYA ---
    
    if ($payment_method === 'cod') {
        // JIKA 'cod', langsung ke halaman sukses
        header('Location: success.php'); // Ganti ke halaman suksesmu
        exit; // WAJIB ada exit setelah header
        
    } else {
        // JIKA 'transfer' ATAU 'ewallet', ke halaman proses
        header('Location: process.php'); // Ganti ke halaman instruksi bayarmu
        exit; // WAJIB ada exit setelah header
    }

} else {
    // Jika file ini diakses langsung (bukan di-POST), tendang balik ke checkout
    header('Location: checkout.php');
    exit;
}
?>
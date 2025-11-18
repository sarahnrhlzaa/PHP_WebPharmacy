<?php

function getConnection() {
    $servername = getenv('DB_HOST') ?: 'mysql';
    $username = getenv('DB_USER') ?: 'appuser'; // Pastikan ini 'appuser'
    $password = getenv('DB_PASSWORD') ?: 'password';
    $dbname = getenv('DB_NAME') ?: 'webpharmacy';

    // Buat koneksi baru
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // PENTING: Kembalikan (return) objek koneksi
    return $conn;
}

function closeConnection($conn) {
    // Fungsi untuk menutup koneksi
    if ($conn) {
        $conn->close();
    }
}

// PENTING: HAPUS semua 'echo' atau 'print' dari file ini.
// File ini sekarang HANYA berisi DUA FUNGSI.
?>
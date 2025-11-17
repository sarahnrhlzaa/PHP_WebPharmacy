<?php
// File: /Connection/connect.php

function getConnection() {
    /**
     * 'mysql' adalah nama service database-mu di docker-compose.yml
     */
    $servername = getenv('DB_HOST') ?: 'mysql';

    // Kredensial ini diambil dari environment service 'mysql'
    $username = getenv('DB_USER') ?: 'appuser'; 
    $password = getenv('DB_PASSWORD') ?: 'password';
    $dbname = getenv('DB_NAME') ?: 'webpharmacy';

    // Buat koneksi baru
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Kembalikan (return) objek koneksi
    return $conn;
}

/**
 * Fungsi ini dipanggil oleh medicine.php
 */
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}



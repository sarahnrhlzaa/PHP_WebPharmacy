<?php
session_start();
require_once '../../Connection/connect.php';

// Validasi Akses
if (empty($_SESSION['user_id']) || empty($_SESSION['checkout_items']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit();
}

$conn = getConnection();

try {
    $conn->begin_transaction();
    
    $userId = $_SESSION['user_id'];
    $items = $_SESSION['checkout_items'];
    
    // 1. SETUP ALAMAT
    if (($_POST['address-option'] ?? '') === 'saved') {
        $stmt = $conn->prepare("SELECT address FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $userAddress = $res['address'];
        $stmt->close();
    } else {
        $userAddress = ($_POST['address'] ?? '') . ", " . ($_POST['city'] ?? '') . " " . ($_POST['postal_code'] ?? '');
    }
    
    // 2. HITUNG TOTAL
    $totalAmount = 10000; // Ongkir
    foreach ($items as $i) $totalAmount += $i['price'] * $i['qty'];
    
    // ============================================================
    // 🔥 LOGIKA BARU: RANDOM ORDER ID (Format: ORD-XXXXXX)
    // ============================================================
    // Total 10 Karakter (ORD- + 6 huruf/angka acak)
    
    $newOrderId = '';
    $foundUnique = false;
    $maxTries = 5; // Coba generate maksimal 5 kali jika kebetulan kembar
    
    for ($i = 0; $i < $maxTries; $i++) {
        // Generate 6 karakter acak (Angka & Huruf Besar)
        // Contoh hasil: 8K2P9M
        $randomString = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        $tempId = 'ORD-' . $randomString;
        
        // Cek apakah ID ini sudah ada di database?
        $stmtCheck = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ?");
        $stmtCheck->bind_param("s", $tempId);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        
        if ($stmtCheck->num_rows == 0) {
            // Jika belum ada (unik), pakai ID ini!
            $newOrderId = $tempId;
            $foundUnique = true;
            $stmtCheck->close();
            break;
        }
        $stmtCheck->close();
    }
    
    if (!$foundUnique) {
        throw new Exception("Gagal membuat Order ID unik. Silakan coba lagi.");
    }
    
    // ============================================================
    
    // 3. INSERT ORDER (Header)
    $stmt = $conn->prepare("INSERT INTO orders (order_id, user_id, user_name, user_email, user_phone, user_address, order_notes, total_amount, payment_method, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("sssssssds", $newOrderId, $userId, $_POST['user_name'], $_POST['user_email'], $_POST['user_phone'], $userAddress, $_POST['notes'], $totalAmount, $_POST['payment_method']);
    
    if (!$stmt->execute()) throw new Exception("Gagal membuat order: " . $stmt->error);
    $stmt->close();
    
    // 4. INSERT DETAIL BARANG
    // Generate ID Detail (OD-xxxx) - Ini boleh urut global karena internal sistem
    $qD = $conn->query("SELECT orderdetail_id FROM orders_detail ORDER BY orderdetail_id DESC LIMIT 1");
    $lastD = ($qD->num_rows > 0) ? $qD->fetch_assoc()['orderdetail_id'] : 'OD-00000';
    $numD = (int)substr($lastD, 3);
    
    $stmtD = $conn->prepare("INSERT INTO orders_detail (orderdetail_id, order_id, medicine_id, medicine_name, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $numD++;
        $newDetailId = 'OD-' . str_pad($numD, 5, '0', STR_PAD_LEFT);
        $sub = $item['price'] * $item['qty'];
        $stmtD->bind_param("ssssidd", $newDetailId, $newOrderId, $item['id'], $item['name'], $item['qty'], $item['price'], $sub);
        $stmtD->execute();
    }
    $stmtD->close();
    
    // 5. BERSIHKAN CART
    $boughtIds = array_keys($items);
    // Hapus DB
    try {
        if (!empty($boughtIds)) {
            $idsStr = "'" . implode("','", $boughtIds) . "'";
            $conn->query("DELETE FROM carts WHERE user_id = '$userId' AND medicine_id IN ($idsStr)");
        }
    } catch (Exception $e) {}
    
    // Hapus Session
    foreach ($boughtIds as $bid) {
        if (isset($_SESSION['cart'][$bid])) unset($_SESSION['cart'][$bid]);
    }
    unset($_SESSION['checkout_items']);
    
    $conn->commit();
    
    // Redirect Sukses
    header("Location: order_success.php?order_id=" . $newOrderId);
    exit();
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['checkout_error'] = $e->getMessage();
    header("Location: checkout.php");
    exit();
} finally {
    closeConnection($conn);
}
?>
<?php
session_start();
require_once '../../Connection/connect.php';

// Cek login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Cek checkout items
if (empty($_SESSION['checkout_items'])) {
    header('Location: cart.php');
    exit();
}

// Validasi form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit();
}

$conn = getConnection();

try {
    // Start transaction
    $conn->begin_transaction();
    
    $userId = $_SESSION['user_id'];
    $checkoutItems = $_SESSION['checkout_items'];
    
    // Ambil data dari form
    $userName = $_POST['user_name'] ?? '';
    $userEmail = $_POST['user_email'] ?? '';
    $userPhone = $_POST['user_phone'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? 'transfer';
    $notes = $_POST['notes'] ?? '';
    
    // Handle address
    $addressOption = $_POST['address-option'] ?? 'new';
    if ($addressOption === 'saved') {
        // Ambil address dari database user
        $stmt = $conn->prepare("SELECT address FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $userAddress = $userData['address'];
        $stmt->close();
    } else {
        // Gunakan address baru dari form
        $address = $_POST['address'] ?? '';
        $city = $_POST['city'] ?? '';
        $postalCode = $_POST['postal_code'] ?? '';
        
        if (empty($address) || empty($city) || empty($postalCode)) {
            throw new Exception('Please fill in all address fields');
        }
        
        $userAddress = $address . ", " . $city . " - " . $postalCode;
    }
    
    // Hitung total
    $subtotal = 0;
    $shipping = 10000;
    foreach ($checkoutItems as $item) {
        $subtotal += $item['price'] * $item['qty'];
    }
    $totalAmount = $subtotal + $shipping;
    
    // Insert ke tabel orders (status default: pending)
    $stmt = $conn->prepare("
        INSERT INTO orders (
            user_id, 
            user_name,
            user_email,
            user_phone,
            user_address,
            order_notes,
            total_amount, 
            payment_method, 
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->bind_param(
        "isssssds",
        $userId,
        $userName,
        $userEmail,
        $userPhone,
        $userAddress,
        $notes,
        $totalAmount,
        $paymentMethod
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create order: ' . $stmt->error);
    }
    
    $orderId = $conn->insert_id;
    $stmt->close();
    
    // Insert ke tabel orders_detail
    $stmt = $conn->prepare("
        INSERT INTO orders_detail (
            order_id, 
            medicine_id,
            medicine_name,
            quantity, 
            price, 
            subtotal
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($checkoutItems as $item) {
        $medicineId = $item['id'];
        $medicineName = $item['name'];
        $quantity = $item['qty'];
        $price = $item['price'];
        $itemSubtotal = $price * $quantity;
        
        $stmt->bind_param(
            "issidi",
            $orderId,
            $medicineId,
            $medicineName,
            $quantity,
            $price,
            $itemSubtotal
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to add order items: ' . $stmt->error);
        }
    }
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    // PENTING: Hapus items dari cart
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Ambil medicine_id dari checkout items
        $checkoutItemIds = array_keys($checkoutItems);
        
        // Hapus satu per satu dari cart
        foreach ($checkoutItemIds as $medicineId) {
            if (isset($_SESSION['cart'][$medicineId])) {
                unset($_SESSION['cart'][$medicineId]);
            }
        }
        
        // Update cart di session (pastikan perubahan tersimpan)
        $_SESSION['cart'] = array_filter($_SESSION['cart']);
    }
    
    // Clear checkout items
    unset($_SESSION['checkout_items']);
    
    // Set success message
    $_SESSION['order_success'] = true;
    $_SESSION['last_order_id'] = $orderId;
    
    // Redirect ke success page
    header('Location: order_success.php?order_id=' . $orderId);
    exit();
    
} catch (Exception $e) {
    // Rollback jika ada error
    $conn->rollback();
    
    $_SESSION['checkout_error'] = $e->getMessage();
    header('Location: checkout.php');
    exit();
    
} finally {
    closeConnection($conn);
}
?>
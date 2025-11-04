<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../Connection/connect.php';

echo "<h2>🔍 ORDER DEBUG TEST</h2>";
echo "<hr>";

// Test 1: Cek Session
echo "<h3>1. Session Check:</h3>";
echo "User ID: " . ($_SESSION['user_id'] ?? '<span style="color:red">EMPTY</span>') . "<br>";
echo "Checkout Items: <pre>" . print_r($_SESSION['checkout_items'] ?? [], true) . "</pre>";
echo "Cart: <pre>" . print_r($_SESSION['cart'] ?? [], true) . "</pre>";
echo "<hr>";

// Test 2: Cek Connection
echo "<h3>2. Database Connection:</h3>";
try {
    $conn = getConnection();
    echo "✅ Connection OK<br>";
    echo "Database: webpharmacy<br>";
    
    // Test 3: Cek tabel orders
    echo "<h3>3. Table 'orders' Check:</h3>";
    $result = $conn->query("DESCRIBE orders");
    if ($result) {
        echo "✅ Table 'orders' exists<br>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Table 'orders' NOT FOUND<br>";
        echo "Error: " . $conn->error;
    }
    
    echo "<hr>";
    
    // Test 4: Cek tabel orders_detail
    echo "<h3>4. Table 'orders_detail' Check:</h3>";
    $result = $conn->query("DESCRIBE orders_detail");
    if ($result) {
        echo "✅ Table 'orders_detail' exists<br>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Table 'orders_detail' NOT FOUND<br>";
        echo "Error: " . $conn->error;
    }
    
    echo "<hr>";
    
    // Test 5: Test Insert Order
    echo "<h3>5. Test Insert Order:</h3>";
    
    if (!isset($_SESSION['user_id'])) {
        echo "❌ Cannot test insert - No user logged in<br>";
    } else {
        $userId = $_SESSION['user_id'];
        
        // Cek user exists
        $stmt = $conn->prepare("SELECT user_id, full_name, email, phone_number FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($user) {
            echo "✅ User found: {$user['full_name']}<br>";
            
            // Test insert
            $testOrderQuery = "
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
            ";
            
            $stmt = $conn->prepare($testOrderQuery);
            
            if (!$stmt) {
                echo "❌ Prepare failed: " . $conn->error . "<br>";
            } else {
                $testName = $user['full_name'];
                $testEmail = $user['email'];
                $testPhone = $user['phone_number'];
                $testAddress = "Test Address";
                $testNotes = "Test Order";
                $testTotal = 50000.00;
                $testPayment = "transfer";
                
                $stmt->bind_param(
                    "isssssds",
                    $userId,
                    $testName,
                    $testEmail,
                    $testPhone,
                    $testAddress,
                    $testNotes,
                    $testTotal,
                    $testPayment
                );
                
                if ($stmt->execute()) {
                    $testOrderId = $conn->insert_id;
                    echo "✅ Test order inserted! Order ID: $testOrderId<br>";
                    
                    // Delete test order
                    $conn->query("DELETE FROM orders WHERE order_id = $testOrderId");
                    echo "✅ Test order deleted (cleanup)<br>";
                } else {
                    echo "❌ Insert failed: " . $stmt->error . "<br>";
                }
                $stmt->close();
            }
        } else {
            echo "❌ User not found<br>";
        }
    }
    
    closeConnection($conn);
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";
echo "<h3>6. Recommendations:</h3>";
echo "<ul>";
echo "<li>If tables NOT FOUND: Run database_schema.sql in phpMyAdmin</li>";
echo "<li>If user_id EMPTY: Login first at login.php</li>";
echo "<li>If insert failed: Check error message above</li>";
echo "</ul>";
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h2 { color: #333; }
h3 { color: #666; margin-top: 20px; }
pre { background: #fff; padding: 10px; border: 1px solid #ddd; }
table { background: #fff; border-collapse: collapse; }
</style>
<?php
session_start();
require_once '../../Connection/connect.php';

// Helper function
function getImagePath($image_path) {
    if (empty($image_path)) return '../assets/default.jpg';
    if (strpos($image_path, '../') === 0) return $image_path;
    if (strpos($image_path, '../../') === 0) return str_replace('../../', '../', $image_path);
    if (strpos($image_path, '/') === false) return '../assets/' . $image_path;
    return $image_path;
}

// Cek login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php?next=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$hasDirect = isset($_GET['medicine_id']);
$hasSelectedPost = isset($_POST['selected_items']) && (
    (is_array($_POST['selected_items']) && count($_POST['selected_items']) > 0) ||
    (is_string($_POST['selected_items']) && trim($_POST['selected_items']) !== '')
);

// Cek keranjang kosong
if (empty($_SESSION['cart']) && !$hasDirect && !$hasSelectedPost) {
    header('Location: cart.php');
    exit();
}

// Ambil selected items
$selectedItems = [];

// CASE 1: Order Now dari medicine.php
if ($hasDirect) {
    $medicineId = $_GET['medicine_id'];

    $conn_temp = getConnection();
    $stmt = $conn_temp->prepare("SELECT medicine_id, medicine_name, price, image_path FROM medicines WHERE medicine_id = ?");
    $stmt->bind_param("s", $medicineId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($product = $result->fetch_assoc()) {
        $selectedItems[$medicineId] = [
            'id'    => $product['medicine_id'],
            'name'  => $product['medicine_name'],
            'price' => $product['price'],
            'image' => getImagePath($product['image_path']),
            'qty'   => 1,
        ];
    }
    $stmt->close();
    closeConnection($conn_temp);
}
// CASE 2: Dari cart dengan selected items
elseif ($hasSelectedPost) {
    // === FIX #2: dukung JSON string maupun array ===
    $raw = $_POST['selected_items'];
    $selectedIds = is_array($raw) ? $raw : json_decode($raw, true);
    if (!is_array($selectedIds)) $selectedIds = [];

    if (!empty($selectedIds)) {
        foreach ($selectedIds as $id) {
            if (isset($_SESSION['cart'][$id])) {
                $selectedItems[$id] = $_SESSION['cart'][$id];
            }
        }
    }
}

/**
 * === FIX #3: Keputusan redirect SETELAH bangun $selectedItems ===
 */
if (empty($selectedItems)) {
    $_SESSION['checkout_error'] = 'Please select at least one item to checkout';
    header('Location: cart.php');
    exit();
}

// Simpan selected items ke session
$_SESSION['checkout_items'] = $selectedItems;

$conn = getConnection();

// Ambil data user
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Hitung total
$subtotal = 0;
$shipping = 10000;
foreach ($selectedItems as $item) {
    $subtotal += $item['price'] * $item['qty'];
}
$total = $subtotal + $shipping;

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../cssuser/checkout.css">
    <link href="https://fonts.googleapis.com/css2?family=Andika&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="checkout-container">
    <h1>Checkout</h1>
    <?php if (isset($_SESSION['checkout_error'])): ?>
        <div class="alert alert-error" style="background: #f44336; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
         <i class="fa fa-exclamation-circle"></i>
    <?php 
        echo htmlspecialchars($_SESSION['checkout_error']); 
        unset($_SESSION['checkout_error']);
    ?>
</div>
    <?php endif; ?>
    <form id="checkout-form" method="POST" action="order_confirmation.php">
        <div class="checkout-content">
            <!-- Left Section -->
            <div class="checkout-form-section">
                
                <!-- Contact Information -->
                <div class="form-section">
                    <h2><i class="fa fa-user"></i> Contact Information</h2>
                    <p class="section-note">Information from your account</p>
                    
                    <div class="info-display">
                        <div class="info-item">
                            <label>Full Name</label>
                            <div class="info-value">
                                <i class="fa fa-user-circle"></i>
                                <span><?php echo htmlspecialchars($user['full_name'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <label>Email</label>
                            <div class="info-value">
                                <i class="fa fa-envelope"></i>
                                <span><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <label>Phone Number</label>
                            <div class="info-value">
                                <i class="fa fa-phone"></i>
                                <span><?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                    <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                    <input type="hidden" name="user_phone" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                </div>
                
                <!-- Shipping Address -->
                <div class="form-section">
                    <h2><i class="fa fa-map-marker"></i> Shipping Address</h2>
                    <p class="section-note">Where should we deliver your order?</p>
                    
                    <?php if (!empty($user['address'])): ?>
                    <div class="saved-address-section">
                        <div class="saved-address">
                            <input type="radio" id="use-saved" name="address-option" value="saved" checked onchange="toggleAddressInput()">
                            <label for="use-saved">
                                <div class="address-header">
                                    <strong><i class="fa fa-home"></i> Default Address</strong>
                                    <span class="badge-primary">Primary</span>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($user['address'])); ?></p>
                            </label>
                        </div>
                        
                        <div class="saved-address">
                            <input type="radio" id="use-new" name="address-option" value="new" onchange="toggleAddressInput()">
                            <label for="use-new">
                                <strong><i class="fa fa-plus-circle"></i> Use Different Address</strong>
                                <p>Deliver to another location</p>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div id="address-input-section" style="<?php echo !empty($user['address']) ? 'display: none;' : ''; ?>">
                        <div class="form-group">
                            <label for="address">Full Address *</label>
                            <textarea id="address" name="address" rows="3" placeholder="Street address, building, apartment, etc."><?php echo empty($user['address']) ? '' : htmlspecialchars($user['address']); ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City *</label>
                                <input type="text" id="city" name="city" placeholder="e.g., Jakarta">
                            </div>
                            
                            <div class="form-group">
                                <label for="postal_code">Postal Code *</label>
                                <input type="text" id="postal_code" name="postal_code" placeholder="e.g., 12345">
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($user['address'])): ?>
                    <input type="hidden" id="saved_address" value="<?php echo htmlspecialchars($user['address']); ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="notes">Delivery Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="2" placeholder="e.g., Ring the doorbell, Leave at the door, etc."></textarea>
                    </div>
                </div>
                
                <!-- Payment Method (UPDATED sesuai enum database) -->
                <div class="form-section">
                    <h2><i class="fa fa-credit-card"></i> Payment Method</h2>
                    
                    <div class="payment-methods">
                        <div class="payment-option">
                            <input type="radio" id="payment-transfer" name="payment_method" value="transfer" checked>
                            <label for="payment-transfer">
                                <i class="fa fa-bank"></i>
                                <div>
                                    <strong>Bank Transfer</strong>
                                    <span>BCA, Mandiri, BNI</span>
                                </div>
                            </label>
                        </div>
                        
                        <div class="payment-option">
                            <input type="radio" id="payment-cod" name="payment_method" value="cod">
                            <label for="payment-cod">
                                <i class="fa fa-money"></i>
                                <div>
                                    <strong>Cash on Delivery</strong>
                                    <span>Pay when you receive</span>
                                </div>
                            </label>
                        </div>
                        
                        <div class="payment-option">
                            <input type="radio" id="payment-ewallet" name="payment_method" value="ewallet">
                            <label for="payment-ewallet">
                                <i class="fa fa-mobile"></i>
                                <div>
                                    <strong>E-Wallet</strong>
                                    <span>OVO, GoPay, Dana</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right: Order Summary -->
            <div class="order-summary-section">
                <div class="order-summary-sticky">
                    <h2>Order Summary</h2>
                    
                    <div style="background: #e8f5e9; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px;">
                        <i class="fa fa-info-circle" style="color: #4caf50;"></i>
                        <span style="color: #2e7d32;">
                            <?php 
                            if (isset($_GET['medicine_id'])) {
                                echo 'Direct Order - 1 item';
                            } else {
                                echo 'Showing ' . count($selectedItems) . ' selected item(s)';
                            }
                            ?>
                        </span>
                    </div>
                    
                    <div class="order-items">
                        <?php foreach ($selectedItems as $item): ?>
                        <div class="order-item">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 onerror="this.src='../assets/default.jpg'">
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p>Qty: <?php echo $item['qty']; ?></p>
                            </div>
                            <div class="item-price">
                                Rp <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-details">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>Rp <?php echo number_format($shipping, 0, ',', '.'); ?></span>
                        </div>
                        <hr>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-place-order">
                        <i class="fa fa-check-circle"></i> Place Order
                    </button>
                    
                    <a href="<?php echo isset($_GET['medicine_id']) ? 'medicine.php' : 'cart.php'; ?>" class="btn-back-to-cart">
                        <i class="fa fa-arrow-left"></i> <?php echo isset($_GET['medicine_id']) ? 'Back to Shop' : 'Back to Cart'; ?>
                    </a>
                </div>
            </div>
        </div>
        <?php $_SESSION['order_token'] = bin2hex(random_bytes(16));?>
        <form method="POST" action="order_confirmation.php" id="checkout-form">
        <input type="hidden" name="order_token" value="<?php echo htmlspecialchars($_SESSION['order_token'], ENT_QUOTES); ?>">
    </form>
</div>

<?php include 'footer.php'; ?>

<script src="../jsuser/checkout.js">
</script>
<!-- // // Toggle address input based on saved/new address selection
// function toggleAddressInput() {
//     const useNew = document.getElementById('use-new');
//     const addressSection = document.getElementById('address-input-section');
    
//     if (useNew && useNew.checked) {
//         addressSection.style.display = 'block';
//     } else {
//         addressSection.style.display = 'none';
//     }
// }

// // Form validation before submit
// document.getElementById('checkout-form').addEventListener('submit', function(e) {
//     const addressOption = document.querySelector('input[name="address-option"]:checked');
    
//     // If using new address, validate fields
//     if (!addressOption || addressOption.value === 'new') {
//         const address = document.getElementById('address').value.trim();
//         const city = document.getElementById('city').value.trim();
//         const postalCode = document.getElementById('postal_code').value.trim();
        
//         if (!address || !city || !postalCode) {
//             e.preventDefault();
//             alert('Please fill in all address fields (Address, City, Postal Code)');
//             return false;
//         }
//     }
    
//     return true;
// }); -->

</body>
</html>
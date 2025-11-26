<?php
session_start();
require_once '../../Connection/connect.php';

// Helper Image
function getCheckoutImagePath($image_path) {
    if (empty($image_path)) return '../assets/default.jpg';
    if (strpos($image_path, '../') === 0) return $image_path;
    if (strpos($image_path, '../../') === 0) return str_replace('../../', '../', $image_path);
    return '../assets/' . $image_path;
}

// Cek Login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php?next=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$selectedItems = [];
$hasDirect = isset($_GET['medicine_id']);
$hasSelectedPost = isset($_POST['selected_items']);

// === LOGIKA AGAR TIDAK MENTAL BALIK KE CART ===
if ($hasDirect) {
    // KASUS 1: Beli Langsung (Order Now)
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT medicine_id, medicine_name, price, image_path FROM medicines WHERE medicine_id = ?");
    $stmt->bind_param("s", $_GET['medicine_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($prod = $res->fetch_assoc()) {
        $selectedItems[$prod['medicine_id']] = [
            'id' => $prod['medicine_id'],
            'name' => $prod['medicine_name'],
            'price' => $prod['price'],
            'image' => getCheckoutImagePath($prod['image_path']),
            'qty' => 1
        ];
    }
    $stmt->close();
    closeConnection($conn);
    
    // Simpan ke session biar aman kalau refresh
    $_SESSION['checkout_items'] = $selectedItems;

} elseif ($hasSelectedPost) {
    // KASUS 2: Dari Cart (Centang barang)
    $raw = $_POST['selected_items'];
    $ids = is_array($raw) ? $raw : json_decode($raw, true);
    
    if (!empty($ids) && isset($_SESSION['cart'])) {
        foreach ($ids as $targetId) {
            // Cari item di cart session (baik via Key maupun Loop)
            if (isset($_SESSION['cart'][$targetId])) {
                $selectedItems[$targetId] = $_SESSION['cart'][$targetId];
            } else {
                foreach ($_SESSION['cart'] as $cItem) {
                    $cId = $cItem['id'] ?? $cItem['medicine_id'] ?? null;
                    if ($cId == $targetId) {
                        $selectedItems[$targetId] = $cItem;
                        break;
                    }
                }
            }
        }
    }
    // Simpan ke session
    $_SESSION['checkout_items'] = $selectedItems;

} elseif (!empty($_SESSION['checkout_items'])) {
    // KASUS 3: RETRY/REFRESH (Ambil dari backup session)
    $selectedItems = $_SESSION['checkout_items'];
}

// Kalau benar-benar kosong, baru tendang
if (empty($selectedItems)) {
    $_SESSION['checkout_error'] = 'Silakan pilih barang terlebih dahulu.';
    header('Location: cart.php');
    exit();
}

// Ambil Data User untuk Form
$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Hitung Total
$subtotal = 0;
foreach ($selectedItems as $item) $subtotal += $item['price'] * $item['qty'];
$shipping = 10000;
$total = $subtotal + $shipping;
closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
        <div class="alert alert-error" style="background:#f44336;color:white;padding:15px;margin-bottom:20px;border-radius:5px;">
             <i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['checkout_error']); unset($_SESSION['checkout_error']); ?>
        </div>
    <?php endif; ?>

    <form id="checkout-form" method="POST" action="order_confirmation.php">
        <div class="checkout-content">
            <div class="checkout-form-section">
                <div class="form-section">
                    <h2><i class="fa fa-user"></i> Contact Information</h2>
                    <div class="info-display">
                        <div class="info-item"><label>Name</label><div class="info-value"><?= htmlspecialchars($user['full_name'] ?? '-') ?></div></div>
                        <div class="info-item"><label>Phone</label><div class="info-value"><?= htmlspecialchars($user['phone_number'] ?? '-') ?></div></div>
                    </div>
                    <input type="hidden" name="user_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
                    <input type="hidden" name="user_email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    <input type="hidden" name="user_phone" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>">
                </div>

                <div class="form-section">
                    <h2><i class="fa fa-map-marker"></i> Shipping Address</h2>
                    <?php if (!empty($user['address'])): ?>
                    <div class="saved-address-section">
                        <div class="saved-address">
                            <input type="radio" id="use-saved" name="address-option" value="saved" checked onchange="toggleAddressInput()">
                            <label for="use-saved"><strong>Default Address</strong><p><?= htmlspecialchars($user['address']) ?></p></label>
                        </div>
                        <div class="saved-address">
                            <input type="radio" id="use-new" name="address-option" value="new" onchange="toggleAddressInput()">
                            <label for="use-new"><strong>New Address</strong></label>
                        </div>
                    </div>
                    <?php else: ?><input type="hidden" name="address-option" value="new"><?php endif; ?>
                    
                    <div id="address-input-section" style="<?= !empty($user['address']) ? 'display:none' : 'display:block' ?>">
                        <div class="form-group"><label>Address *</label><textarea name="address" id="address" rows="3"></textarea></div>
                        <div class="form-row">
                            <div class="form-group"><label>City *</label><input type="text" name="city" id="city"></div>
                            <div class="form-group"><label>Postal Code *</label><input type="text" name="postal_code" id="postal_code"></div>
                        </div>
                    </div>
                    <div class="form-group"><label>Notes</label><textarea name="notes" rows="2"></textarea></div>
                </div>

                <div class="form-section">
                    <h2><i class="fa fa-credit-card"></i> Payment Method</h2>
                    <div class="payment-methods">
                        <div class="payment-option">
                            <input type="radio" id="payment-transfer" name="payment_method" value="transfer" checked>
                            <label for="payment-transfer"><i class="fa fa-bank"></i> Bank Transfer </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="payment-cod" name="payment_method" value="cod">
                            <label for="payment-cod"><i class="fa fa-money"></i> COD </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="payment-ewallet" name="payment_method" value="ewallet">
                            <label for="payment-ewallet"><i class="fa fa-money"></i> E-Wallet </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-summary-section">
                <div class="order-summary-sticky">
                    <h2>Order Summary</h2>
                    <div class="order-items">
                        <?php foreach ($selectedItems as $item): ?>
                        <div class="order-item">
                            <img src="<?= htmlspecialchars($item['image']) ?>" onerror="this.src='../assets/default.jpg'">
                            <div class="item-info"><h4><?= htmlspecialchars($item['name']) ?></h4><p>Qty: <?= $item['qty'] ?></p></div>
                            <div class="item-price">Rp <?= number_format($item['price']*$item['qty'],0,',','.') ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="summary-details">
                        <div class="summary-row"><span>Subtotal:</span><span>Rp <?= number_format($subtotal,0,',','.') ?></span></div>
                        <div class="summary-row"><span>Shipping:</span><span>Rp <?= number_format($shipping,0,',','.') ?></span></div>
                        <hr><div class="summary-row total"><span>Total:</span><span>Rp <?= number_format($total,0,',','.') ?></span></div>
                    </div>
                    <button type="submit" class="btn-place-order">Place Order</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
<script src="../jsUser/checkout.js"></script>
</body>
</html>
<?php
session_start();
require_once '../../Connection/connect.php';

// Helper function untuk gambar
function getImagePath($image_path) {
    if (empty($image_path)) {
        return '../assets/default.jpg';
    }
    if (strpos($image_path, '../') === 0) {
        return $image_path;
    }
    if (strpos($image_path, '../../') === 0) {
        return str_replace('../../', '../', $image_path);
    }
    if (strpos($image_path, '/') === false) {
        return '../assets/' . $image_path;
    }
    return $image_path;
}

// Cek login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Cek order_id
if (!isset($_GET['order_id'])) {
    header('Location: medicine.php');
    exit();
}

$orderId = $_GET['order_id'];
$conn = getConnection();

// Ambil data order
$stmt = $conn->prepare("
    SELECT o.*
    FROM orders o
    WHERE o.order_id = ? AND o.user_id = ?
");
$stmt->bind_param("is", $orderId, $_SESSION['user_id']); // Changed from "ii" to "is"
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// Jika order tidak ditemukan
if (!$order) {
    closeConnection($conn);
    header('Location: medicine.php');
    exit();
}

// Ambil order items
$stmt = $conn->prepare("
    SELECT 
        od.*,
        m.image_path
    FROM orders_detail od
    LEFT JOIN medicines m ON od.medicine_id = m.medicine_id
    WHERE od.order_id = ?
");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

closeConnection($conn);

// Hitung subtotal
$subtotal = 0;
foreach ($orderItems as $item) {
    $subtotal += $item['subtotal'];
}
$shipping = 10000;
$total = $order['total_amount'];

// Generate order number untuk ditampilkan
$orderNumber = 'O-' . str_pad($order['order_id'], 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - <?php echo htmlspecialchars($orderNumber); ?></title>
    <link rel="stylesheet" href="../cssuser/order_success.css">
    <link href="https://fonts.googleapis.com/css2?family=Andika&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="success-container">
    <!-- Success Header -->
    <div class="success-header">
        <div class="success-icon">
            <i class="fa fa-check-circle"></i>
        </div>
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for your order. We've received your order and will process it soon.</p>
        <div class="order-number">
            Order Number: <strong><?php echo htmlspecialchars($orderNumber); ?></strong>
        </div>
    </div>

    <div class="success-content">
        <!-- Order Details -->
        <div class="order-details-section">
            <!-- Order Information -->
            <div class="detail-card">
                <h2><i class="fa fa-info-circle"></i> Order Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Order Date:</span>
                        <span class="value"><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Payment Method:</span>
                        <span class="value">
                            <?php 
                            $paymentLabels = [
                                'transfer' => 'Bank Transfer',
                                'cod' => 'Cash on Delivery',
                                'ewallet' => 'E-Wallet'
                            ];
                            echo $paymentLabels[$order['payment_method']] ?? ucfirst($order['payment_method']);
                            ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">Order Status:</span>
                        <span class="badge badge-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="detail-card">
                <h2><i class="fa fa-user"></i> Customer Information</h2>
                <div class="info-list">
                    <div class="info-row">
                        <i class="fa fa-user-circle"></i>
                        <div>
                            <span class="label">Full Name</span>
                            <span class="value"><?php echo htmlspecialchars($order['user_name']); ?></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <i class="fa fa-envelope"></i>
                        <div>
                            <span class="label">Email</span>
                            <span class="value"><?php echo htmlspecialchars($order['user_email']); ?></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <i class="fa fa-phone"></i>
                        <div>
                            <span class="label">Phone</span>
                            <span class="value"><?php echo htmlspecialchars($order['user_phone']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="detail-card">
                <h2><i class="fa fa-map-marker"></i> Shipping Address</h2>
                <div class="address-content">
                    <p><?php echo nl2br(htmlspecialchars($order['user_address'])); ?></p>
                </div>
                <?php if (!empty($order['order_notes'])): ?>
                <div class="notes-section">
                    <strong>Delivery Notes:</strong>
                    <p><?php echo nl2br(htmlspecialchars($order['order_notes'])); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Order Items -->
            <div class="detail-card">
                <h2><i class="fa fa-shopping-bag"></i> Order Items</h2>
                <div class="order-items-list">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars(getImagePath($item['image_path'])); ?>" 
                             alt="<?php echo htmlspecialchars($item['medicine_name']); ?>"
                             onerror="this.src='../assets/default.jpg'">
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($item['medicine_name']); ?></h4>
                            <p>Quantity: <?php echo $item['quantity']; ?> × Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                        </div>
                        <div class="item-total">
                            Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="detail-card payment-summary">
                <h2><i class="fa fa-calculator"></i> Payment Summary</h2>
                <div class="summary-rows">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping Fee:</span>
                        <span>Rp <?php echo number_format($shipping, 0, ',', '.'); ?></span>
                    </div>
                    <hr>
                    <div class="summary-row total">
                        <span>Total Payment:</span>
                        <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>

            <?php if ($order['payment_method'] === 'transfer' && $order['status'] === 'pending'): ?>
            <!-- Payment Instructions untuk Bank Transfer -->
            <div class="detail-card payment-instructions">
                <h2><i class="fa fa-info-circle"></i> Payment Instructions</h2>
                <div class="alert alert-info">
                    <i class="fa fa-exclamation-circle"></i>
                    <p>Please complete your payment within 24 hours to avoid order cancellation.</p>
                </div>
                <div class="bank-accounts">
                    <div class="bank-item">
                        <strong>BCA</strong>
                        <p>Account: 1234567890</p>
                        <p>Name: Apotek XYZ</p>
                    </div>
                    <div class="bank-item">
                        <strong>Mandiri</strong>
                        <p>Account: 0987654321</p>
                        <p>Name: Apotek XYZ</p>
                    </div>
                    <div class="bank-item">
                        <strong>BNI</strong>
                        <p>Account: 5555666677</p>
                        <p>Name: Apotek XYZ</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="order_history.php" class="btn btn-primary">
                <i class="fa fa-history"></i> View Order History
            </a>
            <a href="medicine.php" class="btn btn-secondary">
                <i class="fa fa-shopping-cart"></i> Continue Shopping
            </a>
            <button onclick="window.print()" class="btn btn-outline">
                <i class="fa fa-print"></i> Print Order
            </button>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
// Auto hide success message after 5 seconds
<?php if (isset($_SESSION['order_success'])): ?>
setTimeout(function() {
    <?php unset($_SESSION['order_success']); ?>
}, 5000);
<?php endif; ?>
</script>

</body>
</html>
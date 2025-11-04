<?php
session_start();
require_once '../../Connection/connect.php';

// Cek login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = getConnection();
$userId = $_SESSION['user_id'];

// Ambil semua orders user dengan jumlah items
$stmt = $conn->prepare("
    SELECT 
        o.*,
        COUNT(od.orderdetail_id) as total_items
    FROM orders o
    LEFT JOIN orders_detail od ON o.order_id = od.order_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");
$stmt->bind_param("s", $userId); // Changed from "i" to "s"
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="../cssuser/order_history.css">
    <link href="https://fonts.googleapis.com/css2?family=Andika&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="history-container">
    <div class="page-header">
        <h1><i class="fa fa-history"></i> Order History</h1>
        <p>Track and manage all your orders</p>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fa fa-shopping-bag"></i>
            <h2>No Orders Yet</h2>
            <p>You haven't placed any orders. Start shopping now!</p>
            <a href="medicine.php" class="btn-shop">
                <i class="fa fa-shopping-cart"></i> Start Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): 
                $orderNumber = 'O-' . str_pad($order['order_id'], 4, '0', STR_PAD_LEFT);
            ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-info">
                        <h3><?php echo htmlspecialchars($orderNumber); ?></h3>
                        <span class="order-date">
                            <i class="fa fa-calendar"></i>
                            <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?>
                        </span>
                    </div>
                    <div class="order-badges">
                        <span class="badge badge-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                </div>

                <div class="order-body">
                    <div class="order-details">
                        <div class="detail-item">
                            <i class="fa fa-shopping-bag"></i>
                            <span><?php echo $order['total_items']; ?> item(s)</span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-credit-card"></i>
                            <span>
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
                        <div class="detail-item total">
                            <i class="fa fa-money"></i>
                            <strong>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong>
                        </div>
                    </div>

                    <div class="order-actions">
                        <a href="order_success.php?order_id=<?php echo $order['order_id']; ?>" class="btn-view">
                            <i class="fa fa-eye"></i> View Details
                        </a>
                        <?php if ($order['status'] === 'pending'): ?>
                        <a href="order_success.php?order_id=<?php echo $order['order_id']; ?>#payment" class="btn-pay">
                            <i class="fa fa-credit-card"></i> Complete Payment
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Progress Tracker -->
                <div class="order-progress">
                    <div class="progress-step <?php echo in_array($order['status'], ['pending', 'completed']) ? 'active' : ''; ?>">
                        <div class="step-icon"><i class="fa fa-clock-o"></i></div>
                        <span>Pending</span>
                    </div>
                    <div class="progress-line <?php echo $order['status'] === 'completed' ? 'active' : ''; ?>"></div>
                    <div class="progress-step <?php echo $order['status'] === 'completed' ? 'active' : ''; ?>">
                        <div class="step-icon"><i class="fa fa-check-circle"></i></div>
                        <span>Completed</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
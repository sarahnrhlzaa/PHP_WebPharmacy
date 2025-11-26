<?php
session_start();
require_once '../../Connection/connect.php';

// 1. Validasi Akses (Harus Login & Ada ID Order)
if (empty($_SESSION['user_id']) || empty($_GET['order_id'])) {
    header('Location: medicine.php');
    exit();
}

$orderId = $_GET['order_id'];
$userId = $_SESSION['user_id'];
$conn = getConnection();

// 2. Ambil Data Order Utama (Header)
// Kita pakai bind "ss" karena order_id & user_id formatnya string
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ss", $orderId, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    // Jika order tidak ditemukan atau bukan milik user ini
    header('Location: medicine.php');
    exit();
}

// 3. Ambil Detail Barang (Items)
$stmt = $conn->prepare("
    SELECT od.*, m.image_path 
    FROM orders_detail od 
    LEFT JOIN medicines m ON od.medicine_id = m.medicine_id 
    WHERE od.order_id = ?
");
$stmt->bind_param("s", $orderId);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

closeConnection($conn);

// Helper Image
function getSuccessImg($path) {
    if (empty($path)) return '../assets/default.jpg';
    if (strpos($path, '../') === 0) return $path;
    if (strpos($path, '../../') === 0) return str_replace('../../', '../', $path);
    return '../assets/' . $path;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link rel="stylesheet" href="../cssuser/order_success.css">
    <link href="https://fonts.googleapis.com/css2?family=Andika&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="success-container" style="margin-top: 125px;">
    <div class="success-header">
        <div class="success-icon">
            <i class="fa fa-check-circle"></i>
        </div>
        <h1>Terima Kasih!</h1>
        <p>Pesanan Anda berhasil dibuat.</p>
        <div class="order-number">
            ID Order: <strong><?= htmlspecialchars($order['order_id']) ?></strong>
        </div>
    </div>

    <div class="success-content">
        
        <div class="detail-card">
            <h2><i class="fa fa-shopping-bag"></i> Barang yang Dipesan</h2>
            <div class="order-items-list">
                <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <img src="<?= htmlspecialchars(getSuccessImg($item['image_path'])) ?>" 
                         onerror="this.src='../assets/default.jpg'">
                    
                    <div class="item-details">
                        <h4><?= htmlspecialchars($item['medicine_name']) ?></h4>
                        <p>
                            <?= $item['quantity'] ?> x 
                            Rp <?= number_format($item['price'], 0, ',', '.') ?>
                        </p>
                    </div>
                    
                    <div class="item-total">
                        Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="detail-card" style="margin-top: 25px;">
            <h2><i class="fa fa-map-marker"></i> Detail Pengiriman</h2>
            
            <div class="address-content">
                <p><strong>Penerima:</strong> <?= htmlspecialchars($order['user_name']) ?> (<?= htmlspecialchars($order['user_phone']) ?>)</p>
                <p><strong>Alamat:</strong> <?= nl2br(htmlspecialchars($order['user_address'])) ?></p>
                
                <?php if (!empty($order['order_notes'])): ?>
                    <div class="notes-section" style="margin-top: 15px; padding: 10px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                        <strong style="color: #856404; font-size: 13px;">📝 Catatan untuk Kurir/Penjual:</strong>
                        <p style="margin: 5px 0 0; color: #856404; font-style: italic;">
                            "<?= nl2br(htmlspecialchars($order['order_notes'])) ?>"
                        </p>
                    </div>
                <?php endif; ?>
                
                <hr>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; font-size: 16px;">
                    <span>Metode Pembayaran:</span>
                    <strong style="text-transform: uppercase; color: #3498db;">
                        <?= htmlspecialchars($order['payment_method']) ?>
                    </strong>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 5px;">
                    <span style="font-size: 16px; font-weight: bold;">Total Bayar:</span>
                    <span style="font-size: 16px; font-weight: bold; color: #2c3e50;">
                        Rp <?= number_format($order['total_amount'], 0, ',', '.') ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="history.php" class="btn btn-primary">
                <i class="fa fa-history"></i> Lihat Riwayat
            </a>
            <a href="medicine.php" class="btn btn-secondary">
                <i class="fa fa-shopping-cart"></i> Belanja Lagi
            </a>
            <button onclick="window.print()" class="btn btn-outline" style="background:#fff; color:#333; border:1px solid #ccc; padding: 12px 25px; border-radius: 25px; cursor: pointer;">
                <i class="fa fa-print"></i> Cetak Nota
            </button>
        </div>
        
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
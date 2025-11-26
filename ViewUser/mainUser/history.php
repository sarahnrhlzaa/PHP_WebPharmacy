<?php
session_start();
require_once '../../Connection/connect.php';

// Cek Login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$conn = getConnection();

// 1. Ambil Data Order dari Database
// Kita ambil juga satu produk sample untuk ditampilkan gambarnya di card
$query = "
    SELECT 
        o.order_id, 
        o.total_amount, 
        o.status, 
        o.created_at,
        (SELECT m.medicine_name FROM orders_detail od JOIN medicines m ON od.medicine_id = m.medicine_id WHERE od.order_id = o.order_id LIMIT 1) as item_name,
        (SELECT m.image_path FROM orders_detail od JOIN medicines m ON od.medicine_id = m.medicine_id WHERE od.order_id = o.order_id LIMIT 1) as item_image,
        (SELECT COUNT(*) FROM orders_detail WHERE order_id = o.order_id) as total_items
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
closeConnection($conn);

// Helper untuk gambar
function getHistoryImg($path) {
    if (empty($path)) return '../assets/default.jpg';
    if (strpos($path, '../') === 0) return $path;
    if (strpos($path, '../../') === 0) return str_replace('../../', '../', $path);
    return '../assets/' . $path;
}

// Helper untuk mapping status DB ke Tampilan UI
function getStatusLabel($status) {
    switch ($status) {
        case 'pending': return 'Menunggu';
        case 'processing': return 'Dikemas';
        case 'shipped': return 'Dikirim';
        case 'completed': return 'Selesai';
        case 'cancelled': return 'Dibatalkan';
        default: return $status;
    }
}

// Helper untuk class CSS status
function getStatusClass($status) {
    switch ($status) {
        case 'pending': return 'dikemas'; // Pakai style dikemas (kuning/orange)
        case 'processing': return 'dikemas';
        case 'shipped': return 'dikirim';
        case 'completed': return 'selesai';
        case 'cancelled': return 'dibatalkan';
        default: return '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order History - PharmaCare</title>
  <link rel="stylesheet" href="../cssuser/history.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <?php include 'navbar.php'; ?>
  
  <div class="container">
    <h1>Your Order History</h1>

    <div class="status-filter" id="statusFilter">
      <button class="pill active" data-filter="semua">Semua</button>
      <button class="pill" data-filter="menunggu">Menunggu</button>
      <button class="pill" data-filter="dikemas">Dikemas</button>
      <button class="pill" data-filter="selesai">Selesai</button>
      <button class="pill" data-filter="dibatalkan">Dibatalkan</button>
    </div>

    <div class="order-list" id="orderList">
      
      <?php if (empty($orders)): ?>
          <div style="text-align:center; padding: 40px; color: #666;">
              <i class="fa fa-receipt" style="font-size: 48px; margin-bottom: 10px; color: #ccc;"></i>
              <p>Belum ada riwayat pesanan.</p>
              <a href="medicine.php" class="btn-primary" style="display:inline-block; margin-top:10px; text-decoration:none;">Mulai Belanja</a>
          </div>
      <?php else: ?>
          <?php foreach ($orders as $ord): 
              $statusLabel = getStatusLabel($ord['status']);
              $statusClass = getStatusClass($ord['status']);
              $filterKey   = strtolower($statusLabel); // untuk filter JS
          ?>
            <div class="order-card" data-status="<?= $filterKey ?>">
              <div class="order-header">
                <span class="order-id">No. Transaksi: <?= htmlspecialchars($ord['order_id']) ?></span>
                <span class="order-status pill-badge <?= $statusClass ?>">
                    <?= $statusLabel ?>
                </span>
              </div>
              
              <div class="order-body">
                <img src="<?= htmlspecialchars(getHistoryImg($ord['item_image'])) ?>" 
                     alt="Product Image"
                     onerror="this.src='../assets/default.jpg'">
                
                <div class="order-info">
                  <h4><?= htmlspecialchars($ord['item_name']) ?></h4>
                  
                  <p style="font-size: 12px; color: #888;">
                    <?php if ($ord['total_items'] > 1): ?>
                        + <?= $ord['total_items'] - 1 ?> barang lainnya
                    <?php endif; ?>
                  </p>
                  
                  <p class="date"><i class="fa fa-calendar"></i> <?= date('d M Y, H:i', strtotime($ord['created_at'])) ?></p>
                </div>
              </div>
              
              <div class="order-footer">
                <span class="total">Total: <strong>Rp <?= number_format($ord['total_amount'], 0, ',', '.') ?></strong></span>
                <div class="btns">
                  <a href="order_success.php?order_id=<?= $ord['order_id'] ?>" class="btn-outline" style="text-decoration:none; font-size:12px;">
                    Lihat Detail
                  </a>
                  
                  <?php if ($ord['status'] == 'completed'): ?>
                    <a href="medicine.php" class="btn-primary" style="text-decoration:none; font-size:12px;">Beli Lagi</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </div>  
  <script>
    document.addEventListener('DOMContentLoaded', () => {
        const filterWrap = document.getElementById('statusFilter');
        const pills = filterWrap.querySelectorAll('.pill');
        const cards = document.querySelectorAll('.order-card');

        const applyFilter = (status) => {
            cards.forEach(c => {
                const s = c.getAttribute('data-status');
                // Jika 'semua', atau statusnya cocok (partial match untuk handle variasi string)
                if (status === 'semua' || s === status) {
                    c.style.display = 'block';
                } else {
                    c.style.display = 'none';
                }
            });
        };

        pills.forEach(btn => {
            btn.addEventListener('click', () => {
                pills.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                applyFilter(btn.dataset.filter);
            });
        });
    });
  </script>
<?php include 'footer.php'; ?>
</body>
</html>
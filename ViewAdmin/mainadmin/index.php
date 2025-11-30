<?php
session_start();
require_once '../../Connection/connect.php';
$conn = getConnection();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Query untuk Stock Alerts (ambil 5 obat dengan stok terendah)
$alertQuery = "SELECT 
    medicine_id,
    medicine_name,
    stock,
    CASE 
        WHEN stock <= 50 THEN 'critical'
        WHEN stock <= 100 THEN 'low'
        ELSE 'safe'
    END as alert_level
FROM medicines 
ORDER BY stock ASC
LIMIT 5";
$alertResult = $conn->query($alertQuery);

// Hitung jumlah alert (obat dengan stok <= 100)
$alertCountQuery = "SELECT COUNT(*) as count FROM medicines WHERE stock <= 100";
$alertCountResult = $conn->query($alertCountQuery);
$alertCount = $alertCountResult->fetch_assoc()['count'];

// Query untuk Today's Transaction (dari orders dan purchases)
$todayTransactionQuery = "
    SELECT COUNT(*) as count FROM purchases WHERE DATE(created_at) = CURDATE()
    UNION ALL
    SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()
";
$todayTransactionResult = $conn->query($todayTransactionQuery);
$todayTransactionTotal = 0;
while($row = $todayTransactionResult->fetch_assoc()) {
    $todayTransactionTotal += $row['count'];
}

// Query untuk Total Medicine
$totalMedicineQuery = "SELECT COUNT(*) as count FROM medicines";
$totalMedicineResult = $conn->query($totalMedicineQuery);
$totalMedicine = $totalMedicineResult->fetch_assoc()['count'];

// Query untuk User Online (total user yang terdaftar)
$userOnlineQuery = "SELECT COUNT(*) as count FROM users";
$userOnlineResult = $conn->query($userOnlineQuery);
$userOnline = $userOnlineResult->fetch_assoc()['count'];

// Query untuk Recent Transactions (gabungan purchases dan orders)
$recentTransactionQuery = "
(SELECT 
    p.purchase_id as transaction_id,
    'Masuk' as type,
    m.medicine_name as item,
    pd.quantity,
    p.created_at as date,
    p.supplier_name as partner
FROM purchases p
JOIN purchase_details pd ON p.purchase_id = pd.purchase_id
JOIN medicines m ON pd.medicine_id = m.medicine_id
ORDER BY p.created_at DESC
LIMIT 3)
UNION ALL
(SELECT 
    o.order_id as transaction_id,
    'Keluar' as type,
    m.medicine_name as item,
    od.quantity,
    o.created_at as date,
    u.username as partner
FROM orders o
JOIN orders_detail od ON o.order_id = od.order_id
JOIN medicines m ON od.medicine_id = m.medicine_id
JOIN users u ON o.user_id = u.user_id
ORDER BY o.created_at DESC
LIMIT 3)
ORDER BY date DESC
LIMIT 5";
$recentTransactionResult = $conn->query($recentTransactionQuery);

// Query untuk Chart Data (7 hari terakhir dari purchases dan orders)
$chartQuery = "
SELECT 
    DATE(created_at) as date,
    'in' as type,
    COUNT(*) as count
FROM purchases 
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
GROUP BY DATE(created_at)
UNION ALL
SELECT 
    DATE(created_at) as date,
    'out' as type,
    COUNT(*) as count
FROM orders 
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
GROUP BY DATE(created_at)
ORDER BY date ASC";
$chartResult = $conn->query($chartQuery);

// Proses data chart
$chartData = [];
for($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartData[$date] = ['in' => 0, 'out' => 0];
}

while($row = $chartResult->fetch_assoc()) {
    if(isset($chartData[$row['date']])) {
        $chartData[$row['date']][$row['type']] = $row['count'];
    }
}

include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - PharmaCare Dashboard</title>
    <link rel="stylesheet" href="../cssadmin/index.css">
</head>
<body>
    <main class="main-content">
        <!-- Slider-->
        <div class="slider-container">
            <div class="slider">
                <div class="slide active" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <div class="slide-content">
                        <h3>Welcome to PharmaCare</h3>
                        <p>Sistem manajemen farmasi terpadu untuk kemudahan pengelolaan obat</p>
                    </div>
                </div>
                <div class="slide" style="background: linear-gradient(135deg, #10b981, #047857);">
                    <div class="slide-content">
                        <h3>Kelola Stok Dengan Mudah</h3>
                        <p>Monitor stok obat secara real-time dan dapatkan alert otomatis</p>
                    </div>
                </div>
                <div class="slide" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                    <div class="slide-content">
                        <h3>Transaksi Cepat & Akurat</h3>
                        <p>Catat transaksi masuk dan keluar dengan sistem yang terintegrasi</p>
                    </div>
                </div>
            </div>
            <div class="slider-dots">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card red">
                <div class="stat-info">
                    <p class="stat-label">Stock Alerts</p>
                    <p class="stat-value"><?php echo $alertCount; ?></p>
                </div>
                <div class="stat-icon red-bg">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>
            </div>

            <div class="stat-card blue">
                <div class="stat-info">
                    <p class="stat-label">Today's Transaction</p>
                    <p class="stat-value"><?php echo $todayTransactionTotal; ?></p>
                </div>
                <div class="stat-icon blue-bg">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-info">
                    <p class="stat-label">Total Users</p>
                    <p class="stat-value"><?php echo $userOnline; ?></p>
                </div>
                <div class="stat-icon green-bg">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-info">
                    <p class="stat-label">Total Medicine</p>
                    <p class="stat-value"><?php echo number_format($totalMedicine); ?></p>
                </div>
                <div class="stat-icon purple-bg">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stock Alerts -->
        <div class="card">
            <div class="card-header">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <h3>Stock Alerts - Lowest Stock</h3>
            </div>
            <div class="alert-list">
                <?php 
                if($alertResult->num_rows > 0) {
                    while($alert = $alertResult->fetch_assoc()) {
                        $alertLevel = $alert['alert_level'];
                        $badgeText = $alertLevel == 'critical' ? 'Critical' : ($alertLevel == 'low' ? 'Low Stock' : 'Safe');
                        ?>
                        <div class="alert-item">
                            <div class="alert-info">
                                <p class="alert-name"><?php echo htmlspecialchars($alert['medicine_name']); ?></p>
                                <p class="alert-min">Stock Level: <?php echo $badgeText; ?></p>
                            </div>
                            <div class="alert-status">
                                <p class="alert-stock <?php echo $alertLevel; ?>">
                                    <?php echo $alert['stock']; ?> unit
                                </p>
                                <span class="badge <?php echo $alertLevel; ?>">
                                    <?php echo $badgeText; ?>
                                </span>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p style="text-align: center; color: #10b981; padding: 20px;">✓ Semua stok obat tersedia</p>';
                }
                ?>
            </div>
        </div>

        <!-- Chart Transaction -->
        <div class="card">
            <div class="card-header">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                <h3>Transaction Chart (Weekly)</h3>
            </div>
            <canvas id="transactionChart"></canvas>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Transactions</h3>
            </div>
            <div class="table-container">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Type</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Partner</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($recentTransactionResult && $recentTransactionResult->num_rows > 0) {
                            while($transaction = $recentTransactionResult->fetch_assoc()) {
                                $typeClass = $transaction['type'] == 'Masuk' ? 'in' : 'out';
                                $formattedDate = date('d M Y', strtotime($transaction['date']));
                                ?>
                                <tr>
                                    <td class="td-bold">TRX<?php echo str_pad($transaction['transaction_id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><span class="badge-type <?php echo $typeClass; ?>"><?php echo $transaction['type']; ?></span></td>
                                    <td><?php echo htmlspecialchars($transaction['item']); ?></td>
                                    <td><?php echo $transaction['quantity']; ?> unit</td>
                                    <td class="td-gray"><?php echo $formattedDate; ?></td>
                                    <td class="td-gray"><?php echo htmlspecialchars($transaction['partner']); ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="6" style="text-align: center; color: #999; padding: 40px;">Belum ada data transaksi</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data chart dari PHP
        const chartLabels = <?php echo json_encode(array_keys($chartData)); ?>;
        const chartDataIn = <?php echo json_encode(array_column($chartData, 'in')); ?>;
        const chartDataOut = <?php echo json_encode(array_column($chartData, 'out')); ?>;
        
        // Format labels
        const formattedLabels = chartLabels.map(date => {
            const d = new Date(date);
            const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            return days[d.getDay()];
        });
    </script>
    <script src="../jsadmin/home.js"></script>
    <?php
    closeConnection($conn);
    ?>
</body>
</html>
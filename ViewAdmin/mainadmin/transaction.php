<?php
// transactions.php - Main page for transaction management
session_start();
require_once '../../Connection/connect.php';

// Pakai getConnection() yang return mysqli
$conn = getConnection();

// Query untuk mengambil semua transaksi (orders dan purchases)
$query = "
    SELECT 
        'order' as transaction_type,
        o.order_id as transaction_id,
        o.order_id as reference_number,
        COALESCE(u.full_name, u.username, 'N/A') as party_name,
        COALESCE(u.email, 'N/A') as contact,
        COALESCE(u.phone_number, '-') as phone,
        o.total_amount,
        o.payment_method,
        o.status,
        o.created_at,
        o.updated_at,
        o.order_notes as notes
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    
    UNION ALL
    
    SELECT 
        'purchase' as transaction_type,
        p.purchase_id as transaction_id,
        p.purchase_id as reference_number,
        p.supplier_name as party_name,
        p.supplier_phone as contact,
        '' as phone,
        p.total_amount,
        p.payment_method,
        p.status,
        p.created_at,
        p.updated_at,
        p.purchase_notes as notes
    FROM purchases p
    
    ORDER BY created_at DESC
";

$result = $conn->query($query);

if (!$result) {
    die("Query Error: " . $conn->error);
}

// Fetch semua data ke array
$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Transaksi - Web Pharmacy</title>
    <link rel="stylesheet" href="../cssadmin/transaction.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="main-content">
        <div class="container">
            <h1>📊 Manajemen Transaksi</h1>
            <p class="subtitle">Kelola semua transaksi orders dan purchases</p>

            <div class="header-actions">
                <div style="display: flex; gap: 10px;">
                    <a href="process_transaction.php?action=add&type=order" class="btn btn-primary">+ Tambah Order</a>
                    <a href="process_transaction.php?action=add&type=purchase" class="btn btn-secondary">+ Tambah Purchase</a>
                </div>
            </div>

            <?php
            // Hitung statistik
            $total_orders = 0;
            $total_purchases = 0;
            $total_revenue = 0;
            $total_spending = 0;

            foreach ($transactions as $t) {
                if ($t['transaction_type'] == 'order') {
                    $total_orders++;
                    $total_revenue += $t['total_amount'];
                } else {
                    $total_purchases++;
                    $total_spending += $t['total_amount'];
                }
            }
            ?>

            <div class="stats-row">
                <div class="stat-card blue">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?php echo $total_orders; ?></div>
                </div>
                <div class="stat-card green">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-label">Total Purchases</div>
                    <div class="stat-value"><?php echo $total_purchases; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Spending</div>
                    <div class="stat-value">Rp <?php echo number_format($total_spending, 0, ',', '.'); ?></div>
                </div>
            </div>

            <div class="filters">
                <div class="filter-group">
                    <label>Tipe Transaksi</label>
                    <select id="filterType" onchange="filterTable()">
                        <option value="all">Semua</option>
                        <option value="order">Order</option>
                        <option value="purchase">Purchase</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Status</label>
                    <select id="filterStatus" onchange="filterTable()">
                        <option value="all">Semua</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="received">Received</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Cari</label>
                    <input type="text" id="searchInput" placeholder="Cari transaksi..." onkeyup="filterTable()">
                </div>
            </div>

            <?php if (empty($transactions)): ?>
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3>Belum ada transaksi</h3>
                    <p>Mulai dengan menambahkan order atau purchase baru</p>
                </div>
            <?php else: ?>
                <table id="transactionTable">
                    <thead>
                        <tr>
                            <th>Tipe</th>
                            <th>ID</th>
                            <th>Nama Pihak</th>
                            <th>Kontak</th>
                            <th>Total Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td>
                                    <span class="badge badge-<?php echo $transaction['transaction_type']; ?>">
                                        <?php echo strtoupper($transaction['transaction_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($transaction['reference_number']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['party_name']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['contact']); ?></td>
                                <td><strong>Rp <?php echo number_format($transaction['total_amount'], 2, ',', '.'); ?></strong></td>
                                <td><?php echo htmlspecialchars($transaction['payment_method'] ?: '-'); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $transaction['status']; ?>">
                                        <?php echo ucfirst($transaction['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="process_transaction.php?action=view&type=<?php echo $transaction['transaction_type']; ?>&id=<?php echo $transaction['transaction_id']; ?>" class="btn-view">
                                            👁️ Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../jsadmin/transaction.js"></script>
    
    <?php
    closeConnection($conn);
    ?>
</body>
</html>
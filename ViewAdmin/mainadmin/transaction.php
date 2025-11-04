<?php
// transactions.php - Halaman utama untuk menampilkan semua transaksi

// Start session FIRST before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi database
require_once '../../Connection/connect.php';

// Jika connect.php tidak mendefinisikan $pdo, buat koneksi manual
if (!isset($pdo)) {
    $host = 'localhost';
    $dbname = 'webpharmacy';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

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

// Execute the query
$stmt = $pdo->prepare($query);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Transaksi - Web Pharmacy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        /* Main content wrapper - NO margin, let navbar handle the spacing */
        .main-content {
            padding: 20px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* When sidebar is open, add margin */
        body:not(.sidebar-closed) .main-content {
            margin-left: 250px;
        }

        /* When sidebar is closed, no margin */
        body.sidebar-closed .main-content {
            margin-left: 0;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .container h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
        }

        .btn-secondary {
            background: #2196F3;
            color: white;
        }

        .btn-secondary:hover {
            background: #0b7dda;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 13px;
        }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }

        select, input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-label {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            font-size: 13px;
            text-transform: uppercase;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .badge-order {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-purchase {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .badge-completed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-pending {
            background: #fff3e0;
            color: #e65100;
        }

        .badge-received {
            background: #e0f2f1;
            color: #00695c;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-view {
            background: #2196F3;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
        }

        .btn-view:hover {
            background: #0b7dda;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state svg {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
                padding: 15px;
            }

            body.sidebar-closed .main-content,
            body:not(.sidebar-closed) .main-content {
                margin-left: 0 !important;
            }

            .container {
                padding: 15px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 8px 6px;
            }
        }
    </style>
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
                                        <a href="view_transaction.php?type=<?php echo $transaction['transaction_type']; ?>&id=<?php echo $transaction['transaction_id']; ?>" class="btn-view">
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

    <script>
        // Sync sidebar state with body class for proper margin handling
        document.addEventListener('DOMContentLoaded', function() {
            // Check if sidebar exists and monitor its state
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                // Initial state check
                if (sidebar.classList.contains('closed')) {
                    document.body.classList.add('sidebar-closed');
                } else {
                    document.body.classList.remove('sidebar-closed');
                }

                // Watch for sidebar toggle (using MutationObserver)
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'class') {
                            if (sidebar.classList.contains('closed')) {
                                document.body.classList.add('sidebar-closed');
                            } else {
                                document.body.classList.remove('sidebar-closed');
                            }
                        }
                    });
                });

                observer.observe(sidebar, { attributes: true });
            }
        });

        function filterTable() {
            const typeFilter = document.getElementById('filterType').value.toLowerCase();
            const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const table = document.getElementById('transactionTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const type = row.cells[0].textContent.toLowerCase();
                const status = row.cells[6].textContent.toLowerCase();
                const allText = row.textContent.toLowerCase();

                let showRow = true;

                if (typeFilter !== 'all' && !type.includes(typeFilter)) {
                    showRow = false;
                }

                if (statusFilter !== 'all' && !status.includes(statusFilter)) {
                    showRow = false;
                }

                if (searchInput && !allText.includes(searchInput)) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            }
        }
    </script>
</body>
</html>
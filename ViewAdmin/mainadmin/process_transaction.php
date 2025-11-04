<?php
// process_transaction.php - Handle semua operasi transaksi (view, add, edit, delete)

// Koneksi database
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

// DEBUG MODE - Hapus setelah selesai
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo "<h3>Debug: Struktur Tabel Suppliers</h3>";
    $columns = $pdo->query("DESCRIBE suppliers")->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    echo "<h3>Sample Data Suppliers</h3>";
    $sample = $pdo->query("SELECT * FROM suppliers LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($sample);
    echo "</pre>";
    
    echo "<h3>Debug: Struktur Tabel Users</h3>";
    $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    exit();
}

// Tentukan action (view, add, edit, delete)
$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$type = isset($_GET['type']) ? $_GET['type'] : 'order';
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Handle DELETE
if ($action == 'delete' && !empty($id)) {
    try {
        $pdo->beginTransaction();
        
        if ($type == 'order') {
            $pdo->prepare("DELETE FROM orders_detail WHERE order_id = :id")->execute([':id' => $id]);
            $pdo->prepare("DELETE FROM orders WHERE order_id = :id")->execute([':id' => $id]);
        } else {
            $pdo->prepare("DELETE FROM purchase_details WHERE purchase_id = :id")->execute([':id' => $id]);
            $pdo->prepare("DELETE FROM purchases WHERE purchase_id = :id")->execute([':id' => $id]);
        }
        
        $pdo->commit();
        header("Location: transactions.php?success=delete");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error deleting: " . $e->getMessage();
    }
}

// Handle FORM SUBMISSION (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();
        
        $items = json_decode($_POST['items'], true);
        
        if ($action == 'edit') {
            // UPDATE existing transaction
            if ($type == 'order') {
                $stmt = $pdo->prepare("
                    UPDATE orders SET 
                        user_id = :user_id, user_name = :user_name, user_email = :user_email,
                        user_phone = :user_phone, user_address = :user_address,
                        total_amount = :total_amount, payment_method = :payment_method,
                        status = :status, order_notes = :order_notes, updated_at = NOW()
                    WHERE order_id = :id
                ");
                $stmt->execute([
                    ':id' => $id,
                    ':user_id' => $_POST['user_id'],
                    ':user_name' => $_POST['user_name'],
                    ':user_email' => $_POST['user_email'],
                    ':user_phone' => $_POST['user_phone'],
                    ':user_address' => $_POST['user_address'],
                    ':total_amount' => $_POST['total_amount'],
                    ':payment_method' => $_POST['payment_method'],
                    ':status' => $_POST['status'],
                    ':order_notes' => $_POST['notes']
                ]);
                
                // Delete old details and insert new ones
                $pdo->prepare("DELETE FROM orders_detail WHERE order_id = :id")->execute([':id' => $id]);
                $stmt_detail = $pdo->prepare("
                    INSERT INTO orders_detail (order_id, medicine_id, medicine_name, quantity, price, subtotal)
                    VALUES (:order_id, :medicine_id, :medicine_name, :quantity, :price, :subtotal)
                ");
                
                foreach ($items as $item) {
                    $stmt_detail->execute([
                        ':order_id' => $id,
                        ':medicine_id' => $item['medicine_id'],
                        ':medicine_name' => $item['medicine_name'],
                        ':quantity' => $item['quantity'],
                        ':price' => $item['price'],
                        ':subtotal' => $item['subtotal']
                    ]);
                }
            } else {
                $stmt = $pdo->prepare("
                    UPDATE purchases SET 
                        supplier_id = :supplier_id, supplier_name = :supplier_name,
                        supplier_phone = :supplier_phone, supplier_address = :supplier_address,
                        total_amount = :total_amount, payment_method = :payment_method,
                        status = :status, purchase_notes = :purchase_notes, updated_at = NOW()
                    WHERE purchase_id = :id
                ");
                $stmt->execute([
                    ':id' => $id,
                    ':supplier_id' => $_POST['supplier_id'],
                    ':supplier_name' => $_POST['supplier_name'],
                    ':supplier_phone' => $_POST['supplier_phone'],
                    ':supplier_address' => $_POST['supplier_address'],
                    ':total_amount' => $_POST['total_amount'],
                    ':payment_method' => $_POST['payment_method'],
                    ':status' => $_POST['status'],
                    ':purchase_notes' => $_POST['notes']
                ]);
                
                // Delete old details and insert new ones
                $pdo->prepare("DELETE FROM purchase_details WHERE purchase_id = :id")->execute([':id' => $id]);
                $stmt_detail = $pdo->prepare("
                    INSERT INTO purchase_details (purchase_id, medicine_id, medicine_name, quantity, price, subtotal)
                    VALUES (:purchase_id, :medicine_id, :medicine_name, :quantity, :price, :subtotal)
                ");
                
                foreach ($items as $item) {
                    $stmt_detail->execute([
                        ':purchase_id' => $id,
                        ':medicine_id' => $item['medicine_id'],
                        ':medicine_name' => $item['medicine_name'],
                        ':quantity' => $item['quantity'],
                        ':price' => $item['price'],
                        ':subtotal' => $item['subtotal']
                    ]);
                }
            }
            
            $pdo->commit();
            header("Location: process_transaction.php?action=view&type=$type&id=$id&success=edit");
            exit();
            
        } else {
            // INSERT new transaction
            if ($type == 'order') {
                $stmt = $pdo->prepare("
                    INSERT INTO orders (order_id, user_id, user_name, user_email, user_phone, user_address, 
                                       total_amount, payment_method, status, order_notes, created_at, updated_at)
                    VALUES (:order_id, :user_id, :user_name, :user_email, :user_phone, :user_address,
                            :total_amount, :payment_method, :status, :order_notes, NOW(), NOW())
                ");
                
                $order_id = $_POST['transaction_id'];
                $stmt->execute([
                    ':order_id' => $order_id,
                    ':user_id' => $_POST['user_id'],
                    ':user_name' => $_POST['user_name'],
                    ':user_email' => $_POST['user_email'],
                    ':user_phone' => $_POST['user_phone'],
                    ':user_address' => $_POST['user_address'],
                    ':total_amount' => $_POST['total_amount'],
                    ':payment_method' => $_POST['payment_method'],
                    ':status' => $_POST['status'],
                    ':order_notes' => $_POST['notes']
                ]);
                
                $stmt_detail = $pdo->prepare("
                    INSERT INTO orders_detail (order_id, medicine_id, medicine_name, quantity, price, subtotal)
                    VALUES (:order_id, :medicine_id, :medicine_name, :quantity, :price, :subtotal)
                ");
                
                foreach ($items as $item) {
                    $stmt_detail->execute([
                        ':order_id' => $order_id,
                        ':medicine_id' => $item['medicine_id'],
                        ':medicine_name' => $item['medicine_name'],
                        ':quantity' => $item['quantity'],
                        ':price' => $item['price'],
                        ':subtotal' => $item['subtotal']
                    ]);
                }
                
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO purchases (purchase_id, supplier_id, supplier_name, supplier_phone, supplier_address,
                                          total_amount, payment_method, status, purchase_notes, created_at, updated_at, admin_id)
                    VALUES (:purchase_id, :supplier_id, :supplier_name, :supplier_phone, :supplier_address,
                            :total_amount, :payment_method, :status, :purchase_notes, NOW(), NOW(), :admin_id)
                ");
                
                $purchase_id = $_POST['transaction_id'];
                $stmt->execute([
                    ':purchase_id' => $purchase_id,
                    ':supplier_id' => $_POST['supplier_id'],
                    ':supplier_name' => $_POST['supplier_name'],
                    ':supplier_phone' => $_POST['supplier_phone'],
                    ':supplier_address' => $_POST['supplier_address'],
                    ':total_amount' => $_POST['total_amount'],
                    ':payment_method' => $_POST['payment_method'],
                    ':status' => $_POST['status'],
                    ':purchase_notes' => $_POST['notes'],
                    ':admin_id' => 'ADM001' // Ganti dengan session admin
                ]);
                
                $stmt_detail = $pdo->prepare("
                    INSERT INTO purchase_details (purchase_id, medicine_id, medicine_name, quantity, price, subtotal)
                    VALUES (:purchase_id, :medicine_id, :medicine_name, :quantity, :price, :subtotal)
                ");
                
                foreach ($items as $item) {
                    $stmt_detail->execute([
                        ':purchase_id' => $purchase_id,
                        ':medicine_id' => $item['medicine_id'],
                        ':medicine_name' => $item['medicine_name'],
                        ':quantity' => $item['quantity'],
                        ':price' => $item['price'],
                        ':subtotal' => $item['subtotal']
                    ]);
                }
            }
            
            $pdo->commit();
            header("Location: process_transaction.php?action=view&type=$type&id=" . ($type == 'order' ? $order_id : $purchase_id) . "&success=add");
            exit();
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}

// Load data untuk form (Add/Edit) atau View
$transaction = null;
$items = [];

if ($action == 'view' || $action == 'edit') {
    if (!empty($id)) {
        if ($type == 'order') {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :id");
            $stmt->execute([':id' => $id]);
            $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($transaction) {
                $stmt_details = $pdo->prepare("SELECT * FROM orders_detail WHERE order_id = :id");
                $stmt_details->execute([':id' => $id]);
                $items = $stmt_details->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            $stmt = $pdo->prepare("SELECT * FROM purchases WHERE purchase_id = :id");
            $stmt->execute([':id' => $id]);
            $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($transaction) {
                $stmt_details = $pdo->prepare("SELECT * FROM purchase_details WHERE purchase_id = :id");
                $stmt_details->execute([':id' => $id]);
                $items = $stmt_details->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        
        if (!$transaction) {
            header("Location: transactions.php");
            exit();
        }
    }
}

// Generate new ID untuk add
$new_id = '';
if ($action == 'add') {
    $prefix = $type == 'order' ? 'O-' : 'PUR-';
    $table = $type == 'order' ? 'orders' : 'purchases';
    $id_field = $type == 'order' ? 'order_id' : 'purchase_id';
    
    $last_id = $pdo->query("SELECT MAX(CAST(SUBSTRING($id_field, " . (strlen($prefix) + 1) . ") AS UNSIGNED)) as max_id FROM $table")->fetch()['max_id'];
    $new_number = str_pad(($last_id ? $last_id + 1 : 1), 4, '0', STR_PAD_LEFT);
    $new_id = $prefix . $new_number;
}

// Load master data
$medicines = $pdo->query("SELECT medicine_id, medicine_name, price FROM medicines ORDER BY medicine_name")->fetchAll(PDO::FETCH_ASSOC);

// Load suppliers - cek kolom yang tersedia
try {
    // Coba query dengan supplier_name terlebih dahulu
    $suppliers = $pdo->query("SELECT supplier_id, supplier_name, supplier_phone FROM suppliers ORDER BY supplier_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Jika gagal, coba dengan kolom 'name' saja
    try {
        $suppliers = $pdo->query("SELECT supplier_id, name as supplier_name, phone as supplier_phone FROM suppliers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e2) {
        // Jika tetap gagal, ambil semua kolom dan map manual
        $suppliers = [];
        $suppliersRaw = $pdo->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($suppliersRaw as $sup) {
            $suppliers[] = [
                'supplier_id' => $sup['supplier_id'] ?? $sup['id'] ?? '',
                'supplier_name' => $sup['supplier_name'] ?? $sup['name'] ?? '',
                'supplier_phone' => $sup['supplier_phone'] ?? $sup['phone'] ?? $sup['contact'] ?? ''
            ];
        }
    }
}

// Load users
try {
    $users = $pdo->query("SELECT user_id, user_name, user_email, user_phone FROM users ORDER BY user_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback jika kolom berbeda
    $users = [];
    $usersRaw = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($usersRaw as $usr) {
        $users[] = [
            'user_id' => $usr['user_id'] ?? $usr['id'] ?? '',
            'user_name' => $usr['user_name'] ?? $usr['name'] ?? $usr['username'] ?? '',
            'user_email' => $usr['user_email'] ?? $usr['email'] ?? '',
            'user_phone' => $usr['user_phone'] ?? $usr['phone'] ?? $usr['contact'] ?? ''
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php 
        if ($action == 'view') echo 'Detail';
        elseif ($action == 'edit') echo 'Edit';
        else echo 'Tambah';
    ?> <?php echo ucfirst($type); ?> - Web Pharmacy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: <?php echo $action == 'view' ? '1000px' : '1200px'; ?>;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, <?php echo $type == 'order' ? '#667eea 0%, #764ba2' : '#f093fb 0%, #f5576c'; ?> 100%);
            color: white;
            padding: 30px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .back-btn {
            display: inline-block;
            color: white;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .back-btn:hover {
            opacity: 1;
        }

        .content {
            padding: 30px;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            font-weight: 500;
        }

        input, select, textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #2196F3;
        }

        input:read-only {
            background: #e9ecef;
        }

        .item-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
            margin-bottom: 10px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #da190b;
        }

        .btn-secondary {
            background: #2196F3;
            color: white;
        }

        .btn-secondary:hover {
            background: #0b7dda;
        }

        .btn-warning {
            background: #ff9800;
            color: white;
        }

        .btn-warning:hover {
            background: #e68900;
        }

        .total-section {
            margin-top: 20px;
            padding: 20px;
            background: #e3f2fd;
            border-radius: 8px;
            text-align: right;
        }

        .total-label {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .total-amount {
            font-size: 32px;
            font-weight: bold;
            color: #1976d2;
            margin-top: 10px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 13px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            font-size: 13px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .total-row {
            background: #e3f2fd;
            font-weight: 600;
        }

        .total-row td {
            padding: 15px 12px;
            font-size: 16px;
            color: #1976d2;
        }

        .notes-box {
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 5px;
            margin-top: 20px;
        }

        .notes-label {
            font-weight: 600;
            color: #856404;
            margin-bottom: 5px;
        }

        .notes-text {
            color: #856404;
            line-height: 1.6;
        }

        .timestamp {
            color: #666;
            font-size: 13px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .form-actions, .back-btn {
                display: none;
            }

            .container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <a href="transactions.php" class="back-btn">← Kembali ke Daftar Transaksi</a>
            <h1>
                <?php 
                if ($action == 'view') echo '📄 Detail';
                elseif ($action == 'edit') echo '✏️ Edit';
                else echo '➕ Tambah';
                ?> <?php echo ucfirst($type); ?>
            </h1>
            <?php if ($action == 'view'): ?>
                <div style="margin-top: 10px;">
                    ID: <?php echo htmlspecialchars($id); ?> | 
                    <span class="badge badge-<?php echo $type; ?>">
                        <?php echo strtoupper($type); ?>
                    </span>
                    <span class="badge badge-<?php echo $transaction['status']; ?>">
                        <?php echo ucfirst($transaction['status']); ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <div class="content">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    if ($_GET['success'] == 'add') echo '✓ Transaksi berhasil ditambahkan!';
                    elseif ($_GET['success'] == 'edit') echo '✓ Transaksi berhasil diupdate!';
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($action == 'view'): ?>
                <!-- VIEW MODE -->
                <div class="info-grid">
                    <?php if ($type == 'order'): ?>
                        <div class="info-card">
                            <div class="info-label">👤 Customer</div>
                            <div class="info-value"><?php echo htmlspecialchars($transaction['user_name']); ?></div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">📧 Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($transaction['user_email']); ?></div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">📞 Telepon</div>
                            <div class="info-value"><?php echo htmlspecialchars($transaction['user_phone']); ?></div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">📍 Alamat</div>
                            <div class="info-value"><?php echo htmlspecialchars($transaction['user_address']); ?></div>
                        </div>
                    <?php else: ?>
                        <div class="info-card">
                            <div class="info-label">🏢 Supplier</div>
                            <div class="info-value"><?php echo htmlspecialchars($transaction['supplier_name']); ?></div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">📞 Telepon Supplier</div>
                            <div class="info-value"><?php echo htmlspecialchars($transaction['supplier_phone']); ?></div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">📍 Alamat Supplier</div>
                            <div class="info-value"><?php echo htmlspecialchars($transaction['supplier_address']); ?></div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">👨‍💼 Admin ID</div>
                            <div class="info-value"><?php echo htmlspecialchars($transaction['admin_id']); ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="info-card">
                        <div class="info-label">💳 Metode Pembayaran</div>
                        <div class="info-value"><?php echo htmlspecialchars($transaction['payment_method'] ?: '-'); ?></div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">📅 Tanggal Dibuat</div>
                        <div class="info-value timestamp">
                            <?php echo date('d F Y, H:i', strtotime($transaction['created_at'])); ?>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">🔄 Terakhir Diupdate</div>
                        <div class="info-value timestamp">
                            <?php echo date('d F Y, H:i', strtotime($transaction['updated_at'])); ?>
                        </div>
                    </div>
                </div>

                <div class="section-title">📦 Detail Items</div>
                
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Obat</th>
                            <th>Nama Obat</th>
                            <th>Quantity</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($items as $item): 
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($item['medicine_id']); ?></td>
                                <td><?php echo htmlspecialchars($item['medicine_name']); ?></td>
                                <td><?php echo number_format($item['quantity'], 0); ?></td>
                                <td>Rp <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="5" style="text-align: right;"><strong>TOTAL AMOUNT:</strong></td>
                            <td><strong>Rp <?php echo number_format($transaction['total_amount'], 2, ',', '.'); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>

                <?php 
                $notes_field = $type == 'order' ? 'order_notes' : 'purchase_notes';
                if (!empty($transaction[$notes_field])): 
                ?>
                    <div class="notes-box">
                        <div class="notes-label">📝 Catatan:</div>
                        <div class="notes-text">
                            <?php echo nl2br(htmlspecialchars($transaction[$notes_field])); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <button onclick="window.print()" class="btn btn-secondary">🖨️ Print</button>
                    <a href="process_transaction.php?action=edit&type=<?php echo $type; ?>&id=<?php echo $id; ?>" class="btn btn-warning">✏️ Edit</a>
                    <button onclick="if(confirm('Yakin ingin menghapus transaksi ini?')) location.href='process_transaction.php?action=delete&type=<?php echo $type; ?>&id=<?php echo $id; ?>'" class="btn btn-danger">🗑️ Hapus</button>
                    <a href="transactions.php" class="btn btn-secondary">← Kembali</a>
                </div>

            <?php else: ?>
                <!-- FORM MODE (Add/Edit) -->
                <form id="transactionForm" method="POST">
                    <input type="hidden" name="items" id="itemsInput">
                    <input type="hidden" name="total_amount" id="totalAmountInput">

                    <div class="form-section">
                        <div class="section-title">Informasi Umum</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>ID <?php echo ucfirst($type); ?></label>
                                <input type="text" name="transaction_id" value="<?php echo $action == 'edit' ? $id : $new_id; ?>" readonly>
                            </div>

                            <?php if ($type == 'order'): ?>
                                <div class="form-group">
                                    <label>User *</label>
                                    <select name="user_id" id="userSelect" required onchange="fillUserData()">
                                        <option value="">Pilih User</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?php echo $user['user_id']; ?>" 
                                                    data-name="<?php echo htmlspecialchars($user['user_name']); ?>"
                                                    data-email="<?php echo htmlspecialchars($user['user_email']); ?>"
                                                    data-phone="<?php echo htmlspecialchars($user['user_phone']); ?>"
                                                    <?php echo ($action == 'edit' && $transaction['user_id'] == $user['user_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($user['user_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nama User *</label>
                                    <input type="text" name="user_name" id="userName" required value="<?php echo $action == 'edit' ? htmlspecialchars($transaction['user_name']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" name="user_email" id="userEmail" required value="<?php echo $action == 'edit' ? htmlspecialchars($transaction['user_email']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Telepon *</label>
                                    <input type="text" name="user_phone" id="userPhone" required value="<?php echo $action == 'edit' ? htmlspecialchars($transaction['user_phone']) : ''; ?>">
                                </div>
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label>Alamat *</label>
                                    <textarea name="user_address" rows="2" required><?php echo $action == 'edit' ? htmlspecialchars($transaction['user_address']) : ''; ?></textarea>
                                </div>
                            <?php else: ?>
                                <div class="form-group">
                                    <label>Supplier *</label>
                                    <select name="supplier_id" id="supplierSelect" required onchange="fillSupplierData()">
                                        <option value="">Pilih Supplier</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?php echo $supplier['supplier_id']; ?>" 
                                                    data-name="<?php echo htmlspecialchars($supplier['supplier_name']); ?>"
                                                    data-phone="<?php echo htmlspecialchars($supplier['supplier_phone']); ?>"
                                                    <?php echo ($action == 'edit' && $transaction['supplier_id'] == $supplier['supplier_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nama Supplier *</label>
                                    <input type="text" name="supplier_name" id="supplierName" required value="<?php echo $action == 'edit' ? htmlspecialchars($transaction['supplier_name']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Telepon Supplier *</label>
                                    <input type="text" name="supplier_phone" id="supplierPhone" required value="<?php echo $action == 'edit' ? htmlspecialchars($transaction['supplier_phone']) : ''; ?>">
                                </div>
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label>Alamat Supplier *</label>
                                    <textarea name="supplier_address" rows="2" required><?php echo $action == 'edit' ? htmlspecialchars($transaction['supplier_address']) : ''; ?></textarea>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Metode Pembayaran *</label>
                                <select name="payment_method" required>
                                    <option value="">Pilih Metode</option>
                                    <option value="transfer" <?php echo ($action == 'edit' && $transaction['payment_method'] == 'transfer') ? 'selected' : ''; ?>>Transfer Bank</option>
                                    <option value="transfer_bank" <?php echo ($action == 'edit' && $transaction['payment_method'] == 'transfer_bank') ? 'selected' : ''; ?>>Transfer Bank</option>
                                    <option value="cash" <?php echo ($action == 'edit' && $transaction['payment_method'] == 'cash') ? 'selected' : ''; ?>>Cash</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required>
                                    <option value="pending" <?php echo ($action == 'edit' && $transaction['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="completed" <?php echo ($action == 'edit' && $transaction['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <?php if ($type == 'purchase'): ?>
                                        <option value="received" <?php echo ($action == 'edit' && $transaction['status'] == 'received') ? 'selected' : ''; ?>>Received</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label>Catatan</label>
                                <textarea name="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."><?php 
                                    if ($action == 'edit') {
                                        echo htmlspecialchars($transaction[$type == 'order' ? 'order_notes' : 'purchase_notes']);
                                    }
                                ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">📦 Item Obat</div>
                        
                        <div id="itemsContainer">
                            <?php if ($action == 'edit' && !empty($items)): ?>
                                <?php foreach ($items as $item): ?>
                                    <div class="item-row">
                                        <div class="form-group">
                                            <label>Obat</label>
                                            <select class="medicine-select" onchange="updateItem(this)" required>
                                                <option value="">Pilih Obat</option>
                                                <?php foreach ($medicines as $med): ?>
                                                    <option value="<?php echo $med['medicine_id']; ?>" 
                                                            data-name="<?php echo htmlspecialchars($med['medicine_name']); ?>"
                                                            data-price="<?php echo $med['price']; ?>"
                                                            <?php echo $item['medicine_id'] == $med['medicine_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($med['medicine_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <input type="number" class="item-quantity" min="1" value="<?php echo $item['quantity']; ?>" onchange="updateItem(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Harga</label>
                                            <input type="number" class="item-price" step="0.01" min="0" value="<?php echo $item['price']; ?>" onchange="updateItem(this)" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Subtotal</label>
                                            <input type="number" class="item-subtotal" value="<?php echo $item['subtotal']; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-danger" onclick="removeItem(this)">🗑️</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="item-row">
                                    <div class="form-group">
                                        <label>Obat</label>
                                        <select class="medicine-select" onchange="updateItem(this)" required>
                                            <option value="">Pilih Obat</option>
                                            <?php foreach ($medicines as $med): ?>
                                                <option value="<?php echo $med['medicine_id']; ?>" 
                                                        data-name="<?php echo htmlspecialchars($med['medicine_name']); ?>"
                                                        data-price="<?php echo $med['price']; ?>">
                                                    <?php echo htmlspecialchars($med['medicine_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" class="item-quantity" min="1" value="1" onchange="updateItem(this)" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Harga</label>
                                        <input type="number" class="item-price" step="0.01" min="0" onchange="updateItem(this)" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Subtotal</label>
                                        <input type="number" class="item-subtotal" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger" onclick="removeItem(this)">🗑️</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="button" class="btn btn-secondary" onclick="addItem()">+ Tambah Item</button>

                        <div class="total-section">
                            <div class="total-label">Total Amount:</div>
                            <div class="total-amount" id="totalDisplay">Rp 0</div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="transactions.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            💾 <?php echo $action == 'edit' ? 'Update' : 'Simpan'; ?> <?php echo ucfirst($type); ?>
                        </button>
                    </div>
                </form>

                <script>
                    const medicines = <?php echo json_encode($medicines); ?>;

                    function fillUserData() {
                        const select = document.getElementById('userSelect');
                        const option = select.options[select.selectedIndex];
                        
                        if (option.value) {
                            document.getElementById('userName').value = option.dataset.name;
                            document.getElementById('userEmail').value = option.dataset.email;
                            document.getElementById('userPhone').value = option.dataset.phone;
                        } else {
                            document.getElementById('userName').value = '';
                            document.getElementById('userEmail').value = '';
                            document.getElementById('userPhone').value = '';
                        }
                    }

                    function fillSupplierData() {
                        const select = document.getElementById('supplierSelect');
                        const option = select.options[select.selectedIndex];
                        
                        if (option.value) {
                            document.getElementById('supplierName').value = option.dataset.name;
                            document.getElementById('supplierPhone').value = option.dataset.phone;
                        } else {
                            document.getElementById('supplierName').value = '';
                            document.getElementById('supplierPhone').value = '';
                        }
                    }

                    function updateItem(element) {
                        const row = element.closest('.item-row');
                        const medicineSelect = row.querySelector('.medicine-select');
                        const quantityInput = row.querySelector('.item-quantity');
                        const priceInput = row.querySelector('.item-price');
                        const subtotalInput = row.querySelector('.item-subtotal');

                        if (element.classList.contains('medicine-select')) {
                            const option = medicineSelect.options[medicineSelect.selectedIndex];
                            if (option.value) {
                                priceInput.value = option.dataset.price;
                            }
                        }

                        const quantity = parseFloat(quantityInput.value) || 0;
                        const price = parseFloat(priceInput.value) || 0;
                        const subtotal = quantity * price;
                        
                        subtotalInput.value = subtotal.toFixed(2);
                        calculateTotal();
                    }

                    function calculateTotal() {
                        let total = 0;
                        const subtotals = document.querySelectorAll('.item-subtotal');
                        
                        subtotals.forEach(input => {
                            total += parseFloat(input.value) || 0;
                        });

                        document.getElementById('totalDisplay').textContent = 
                            'Rp ' + total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        
                        document.getElementById('totalAmountInput').value = total.toFixed(2);
                    }

                    function addItem() {
                        const container = document.getElementById('itemsContainer');
                        const newRow = document.createElement('div');
                        newRow.className = 'item-row';
                        newRow.innerHTML = `
                            <div class="form-group">
                                <label>Obat</label>
                                <select class="medicine-select" onchange="updateItem(this)" required>
                                    <option value="">Pilih Obat</option>
                                    ${medicines.map(med => 
                                        `<option value="${med.medicine_id}" data-name="${med.medicine_name}" data-price="${med.price}">
                                            ${med.medicine_name}
                                        </option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" class="item-quantity" min="1" value="1" onchange="updateItem(this)" required>
                            </div>
                            <div class="form-group">
                                <label>Harga</label>
                                <input type="number" class="item-price" step="0.01" min="0" onchange="updateItem(this)" required>
                            </div>
                            <div class="form-group">
                                <label>Subtotal</label>
                                <input type="number" class="item-subtotal" readonly>
                            </div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger" onclick="removeItem(this)">🗑️</button>
                            </div>
                        `;
                        container.appendChild(newRow);
                    }

                    function removeItem(button) {
                        const container = document.getElementById('itemsContainer');
                        if (container.children.length > 1) {
                            button.closest('.item-row').remove();
                            calculateTotal();
                        } else {
                            alert('Minimal harus ada 1 item!');
                        }
                    }

                    document.getElementById('transactionForm').addEventListener('submit', function(e) {
                        e.preventDefault();

                        const items = [];
                        const rows = document.querySelectorAll('.item-row');
                        
                        rows.forEach(row => {
                            const medicineSelect = row.querySelector('.medicine-select');
                            const option = medicineSelect.options[medicineSelect.selectedIndex];
                            
                            if (option.value) {
                                items.push({
                                    medicine_id: option.value,
                                    medicine_name: option.dataset.name,
                                    quantity: row.querySelector('.item-quantity').value,
                                    price: row.querySelector('.item-price').value,
                                    subtotal: row.querySelector('.item-subtotal').value
                                });
                            }
                        });

                        if (items.length === 0) {
                            alert('Tambahkan minimal 1 item!');
                            return;
                        }

                        document.getElementById('itemsInput').value = JSON.stringify(items);
                        this.submit();
                    });

                    // Initialize
                    calculateTotal();
                </script>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
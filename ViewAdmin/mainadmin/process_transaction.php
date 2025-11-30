<?php
// process_transaction.php - WITH AUTO-INCREMENT ID
session_start();

require_once '../../Connection/connect.php';

$conn = getConnection();

// Ambil parameter dari URL
$action = $_GET['action'] ?? 'view';
$type = $_GET['type'] ?? 'order';
$id = $_GET['id'] ?? null;

// ====== FUNGSI GENERATE ORDER ID OTOMATIS ======
function generateOrderId($conn) {
    $result = $conn->query("SELECT order_id FROM orders ORDER BY order_id DESC LIMIT 1");
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['order_id'];
        // Extract number from O-0001 format
        $number = intval(str_replace('O-', '', $lastId)) + 1;
    } else {
        $number = 1;
    }
    
    $newId = 'O-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    return $newId;
}

// ====== FUNGSI GENERATE PURCHASE ID OTOMATIS ======
function generatePurchaseId($conn) {
    $result = $conn->query("SELECT purchase_id FROM purchases ORDER BY purchase_id DESC LIMIT 1");
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['purchase_id'];
        // Extract number from PUR-0005 format
        $number = intval(str_replace('PUR-', '', $lastId)) + 1;
    } else {
        $number = 1;
    }
    
    $newId = 'PUR-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    return $newId;
}

// ====== HANDLE AJAX REQUEST UNTUK GENERATE ID ======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    $ajax_action = $_POST['ajax_action'];
    
    if ($ajax_action === 'generate_order_id') {
        $newId = generateOrderId($conn);
        echo 'success|' . $newId;
        closeConnection($conn);
        exit();
    } elseif ($ajax_action === 'generate_purchase_id') {
        $newId = generatePurchaseId($conn);
        echo 'success|' . $newId;
        closeConnection($conn);
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? null;
    $postType = $_POST['type'] ?? null;
    
    if ($postAction === 'add') {
        if ($postType === 'order') {
            addOrder($conn);
        } else {
            addPurchase($conn);
        }
        exit();
    } elseif ($postAction === 'edit') {
        if ($postType === 'order') {
            editOrder($conn, $_POST['id']);
        } else {
            editPurchase($conn, $_POST['id']);
        }
        exit();
    } elseif ($postAction === 'delete') {
        if ($postType === 'order') {
            deleteOrder($conn, $_POST['id']);
        } else {
            deletePurchase($conn, $_POST['id']);
        }
        exit();
    }
}

// Fungsi untuk menambah Order
function addOrder($conn) {
    // GENERATE ORDER ID OTOMATIS
    $order_id = generateOrderId($conn);
    
    $user_id = $_POST['user_id'] ?? '';
    $user_name = $_POST['user_name'] ?? '';
    $user_email = $_POST['user_email'] ?? '';
    $user_phone = $_POST['user_phone'] ?? '';
    $user_address = $_POST['user_address'] ?? '';
    $total_amount = $_POST['total_amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? null;
    $status = $_POST['status'] ?? 'pending';
    $order_notes = $_POST['order_notes'] ?? '';
    
    if (empty($payment_method)) {
        $payment_method = null;
    }
    
    $query = "INSERT INTO orders (order_id, user_id, user_name, user_email, user_phone, user_address, total_amount, payment_method, status, order_notes, created_at, updated_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        $_SESSION['error'] = "Prepare statement error: " . $conn->error;
        header("Location: process_transaction.php?action=add&type=order");
        exit();
    }
    
    $stmt->bind_param("ssssssdss", $order_id, $user_id, $user_name, $user_email, $user_phone, $user_address, $total_amount, $payment_method, $status, $order_notes);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Order berhasil ditambahkan dengan ID: " . $order_id;
        header("Location: transaction.php");
    } else {
        $_SESSION['error'] = "❌ Gagal menambahkan order: " . $stmt->error;
        header("Location: process_transaction.php?action=add&type=order");
    }
    $stmt->close();
}

// Fungsi untuk menambah Purchase
function addPurchase($conn) {
    // GENERATE PURCHASE ID OTOMATIS
    $purchase_id = generatePurchaseId($conn);
    
    $supplier_id = $_POST['supplier_id'] ?? '';
    $supplier_name = $_POST['supplier_name'] ?? '';
    $supplier_phone = $_POST['supplier_phone'] ?? '';
    $supplier_address = $_POST['supplier_address'] ?? '';
    $total_amount = $_POST['total_amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    $purchase_notes = $_POST['purchase_notes'] ?? '';
    $admin_id = $_POST['admin_id'] ?? null;
    
    if (empty($admin_id)) {
        $admin_id = null;
    }
    
    $query = "INSERT INTO purchases (purchase_id, supplier_id, supplier_name, supplier_phone, supplier_address, total_amount, payment_method, status, purchase_notes, admin_id, created_at, updated_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        $_SESSION['error'] = "Prepare statement error: " . $conn->error;
        header("Location: process_transaction.php?action=add&type=purchase");
        exit();
    }
    
    $stmt->bind_param("sssssdssss", $purchase_id, $supplier_id, $supplier_name, $supplier_phone, $supplier_address, $total_amount, $payment_method, $status, $purchase_notes, $admin_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Purchase berhasil ditambahkan dengan ID: " . $purchase_id;
        header("Location: transaction.php");
    } else {
        $_SESSION['error'] = "❌ Gagal menambahkan purchase: " . $stmt->error;
        header("Location: process_transaction.php?action=add&type=purchase");
    }
    $stmt->close();
}

// Fungsi untuk edit Order
function editOrder($conn, $id) {
    $user_id = $_POST['user_id'] ?? '';
    $user_name = $_POST['user_name'] ?? '';
    $user_email = $_POST['user_email'] ?? '';
    $user_phone = $_POST['user_phone'] ?? '';
    $user_address = $_POST['user_address'] ?? '';
    $total_amount = $_POST['total_amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? null;
    $status = $_POST['status'] ?? 'pending';
    $order_notes = $_POST['order_notes'] ?? '';
    
    if (empty($payment_method)) {
        $payment_method = null;
    }
    
    $query = "UPDATE orders SET user_id = ?, user_name = ?, user_email = ?, user_phone = ?, user_address = ?, total_amount = ?, payment_method = ?, status = ?, order_notes = ?, updated_at = NOW() 
              WHERE order_id = ?";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        $_SESSION['error'] = "Prepare statement error: " . $conn->error;
        header("Location: process_transaction.php?action=edit&type=order&id=" . $id);
        exit();
    }
    
    $stmt->bind_param("sssssdssss", $user_id, $user_name, $user_email, $user_phone, $user_address, $total_amount, $payment_method, $status, $order_notes, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Order berhasil diupdate!";
        header("Location: transaction.php");
    } else {
        $_SESSION['error'] = "❌ Gagal mengupdate order: " . $stmt->error;
        header("Location: process_transaction.php?action=edit&type=order&id=" . $id);
    }
    $stmt->close();
}

// Fungsi untuk edit Purchase
function editPurchase($conn, $id) {
    $supplier_id = $_POST['supplier_id'] ?? '';
    $supplier_name = $_POST['supplier_name'] ?? '';
    $supplier_phone = $_POST['supplier_phone'] ?? '';
    $supplier_address = $_POST['supplier_address'] ?? '';
    $total_amount = $_POST['total_amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    $purchase_notes = $_POST['purchase_notes'] ?? '';
    $admin_id = $_POST['admin_id'] ?? null;
    
    if (empty($admin_id)) {
        $admin_id = null;
    }
    
    $query = "UPDATE purchases SET supplier_id = ?, supplier_name = ?, supplier_phone = ?, supplier_address = ?, total_amount = ?, payment_method = ?, status = ?, purchase_notes = ?, admin_id = ?, updated_at = NOW() 
              WHERE purchase_id = ?";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        $_SESSION['error'] = "Prepare statement error: " . $conn->error;
        header("Location: process_transaction.php?action=edit&type=purchase&id=" . $id);
        exit();
    }
    
    $stmt->bind_param("ssssdsssss", $supplier_id, $supplier_name, $supplier_phone, $supplier_address, $total_amount, $payment_method, $status, $purchase_notes, $admin_id, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Purchase berhasil diupdate!";
        header("Location: transaction.php");
    } else {
        $_SESSION['error'] = "❌ Gagal mengupdate purchase: " . $stmt->error;
        header("Location: process_transaction.php?action=edit&type=purchase&id=" . $id);
    }
    $stmt->close();
}

// Fungsi untuk delete Order
function deleteOrder($conn, $id) {
    $query = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        $_SESSION['error'] = "Prepare statement error: " . $conn->error;
        header("Location: transaction.php");
        exit();
    }
    
    $stmt->bind_param("s", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Order berhasil dihapus!";
    } else {
        $_SESSION['error'] = "❌ Gagal menghapus order: " . $stmt->error;
    }
    
    $stmt->close();
    header("Location: transaction.php");
}

// Fungsi untuk delete Purchase
function deletePurchase($conn, $id) {
    $query = "DELETE FROM purchases WHERE purchase_id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        $_SESSION['error'] = "Prepare statement error: " . $conn->error;
        header("Location: transaction.php");
        exit();
    }
    
    $stmt->bind_param("s", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Purchase berhasil dihapus!";
    } else {
        $_SESSION['error'] = "❌ Gagal menghapus purchase: " . $stmt->error;
    }
    
    $stmt->close();
    header("Location: transaction.php");
}

// Ambil data untuk view/edit
$transaction = null;
if ($action === 'view' || $action === 'edit') {
    if (!$id) {
        $_SESSION['error'] = "ID transaksi tidak ditemukan!";
        header("Location: transaction.php");
        exit();
    }
    
    if ($type === 'order') {
        $query = "SELECT * FROM orders WHERE order_id = ?";
    } else {
        $query = "SELECT * FROM purchases WHERE purchase_id = ?";
    }
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        $_SESSION['error'] = "Prepare statement error: " . $conn->error;
        header("Location: transaction.php");
        exit();
    }
    
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();
    
    if (!$transaction) {
        $_SESSION['error'] = "Transaksi tidak ditemukan!";
        header("Location: transaction.php");
        exit();
    }
    $stmt->close();
}

$current_page = 'transaction';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($action); ?> <?php echo ucfirst($type); ?> - Web Pharmacy</title>
    <link rel="stylesheet" href="../cssadmin/process_transaction.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1>
                <?php 
                    if ($action === 'add') echo "➕ Tambah ";
                    elseif ($action === 'edit') echo "✏️ Edit ";
                    elseif ($action === 'view') echo "👁️ Detail ";
                    echo ucfirst($type);
                ?>
            </h1>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($action === 'view'): ?>
                <!-- VIEW MODE -->
                <div class="detail-view">
                    <?php if ($type === 'order'): ?>
                        <div class="detail-row">
                            <div class="detail-label">Order ID:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['order_id']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">User ID:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['user_id']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Customer Name:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['user_name']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Email:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['user_email']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Phone:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['user_phone'] ?: '-'); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Address:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['user_address'] ?: '-'); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Total Amount:</div>
                            <div class="detail-value"><strong>Rp <?php echo number_format($transaction['total_amount'], 2, ',', '.'); ?></strong></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Payment Method:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['payment_method'] ?: '-'); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">
                                <span class="badge badge-<?php echo $transaction['status']; ?>">
                                    <?php echo ucfirst($transaction['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Notes:</div>
                            <div class="detail-value"><?php echo nl2br(htmlspecialchars($transaction['order_notes'] ?: '-')); ?></div>
                        </div>
                    <?php else: ?>
                        <div class="detail-row">
                            <div class="detail-label">Purchase ID:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['purchase_id']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Supplier ID:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['supplier_id']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Supplier Name:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['supplier_name']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Supplier Phone:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['supplier_phone']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Supplier Address:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['supplier_address'] ?: '-'); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Total Amount:</div>
                            <div class="detail-value"><strong>Rp <?php echo number_format($transaction['total_amount'], 2, ',', '.'); ?></strong></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Payment Method:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['payment_method']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">
                                <span class="badge badge-<?php echo $transaction['status']; ?>">
                                    <?php echo ucfirst($transaction['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Admin ID:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($transaction['admin_id'] ?: '-'); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Notes:</div>
                            <div class="detail-value"><?php echo nl2br(htmlspecialchars($transaction['purchase_notes'] ?: '-')); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="detail-row">
                        <div class="detail-label">Created At:</div>
                        <div class="detail-value"><?php echo date('d/m/Y H:i:s', strtotime($transaction['created_at'])); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Updated At:</div>
                        <div class="detail-value"><?php echo date('d/m/Y H:i:s', strtotime($transaction['updated_at'])); ?></div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="process_transaction.php?action=edit&type=<?php echo $type; ?>&id=<?php echo $id; ?>" class="btn-submit">Edit</a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="type" value="<?php echo $type; ?>">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <button type="submit" class="btn-delete">Hapus</button>
                        </form>
                        <a href="transaction.php" class="btn-cancel">Kembali</a>
                    </div>
                </div>
                
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <!-- ADD/EDIT FORM -->
                <div class="form-container">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <input type="hidden" name="type" value="<?php echo $type; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <?php endif; ?>
                        
                        <?php if ($type === 'order'): ?>
                            <!-- ORDER FORM -->
                            
                            <!-- <div class="form-group">
                                <label>User ID *</label>
                                
                            </div> -->
                            
                            <div class="form-group">
                                <label>Customer Name *</label>
                                <input type="text" name="user_name" required 
                                    value="<?php echo $action === 'edit' ? htmlspecialchars($transaction['user_name']) : ''; ?>"
                                    placeholder="Nama lengkap customer">
                            </div>
                            
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="user_email" required 
                                    value="<?php echo $action === 'edit' ? htmlspecialchars($transaction['user_email']) : ''; ?>"
                                    placeholder="email@example.com">
                            </div>
                            
                            <div class="form-group">
                                <label>Phone Number *</label>
                                <input type="text" name="user_phone" required 
                                    value="<?php echo $action === 'edit' ? htmlspecialchars($transaction['user_phone']) : ''; ?>"
                                    placeholder="0812-xxxx-xxxx">
                            </div>
                            
                            <div class="form-group">
                                <label>Address *</label>
                                <textarea name="user_address" required placeholder="Alamat lengkap customer"><?php echo $action === 'edit' ? htmlspecialchars($transaction['user_address']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Total Amount *</label>
                                <input type="number" name="total_amount" step="0.01" required 
                                    value="<?php echo $action === 'edit' ? $transaction['total_amount'] : ''; ?>"
                                    placeholder="0.00">
                            </div>
                            
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select name="payment_method">
                                    <option value="">Pilih Metode (Opsional)</option>
                                    <option value="transfer" <?php echo ($action === 'edit' && $transaction['payment_method'] == 'transfer') ? 'selected' : ''; ?>>Transfer</option>
                                    <option value="cash" <?php echo ($action === 'edit' && $transaction['payment_method'] == 'cash') ? 'selected' : ''; ?>>Cash</option>
                                    <option value="credit_card" <?php echo ($action === 'edit' && $transaction['payment_method'] == 'credit_card') ? 'selected' : ''; ?>>Credit Card</option>
                                    <option value="e-wallet" <?php echo ($action === 'edit' && $transaction['payment_method'] == 'e-wallet') ? 'selected' : ''; ?>>E-Wallet</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required>
                                    <option value="pending" <?php echo ($action === 'edit' && $transaction['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="completed" <?php echo ($action === 'edit' && $transaction['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo ($action === 'edit' && $transaction['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Order Notes</label>
                                <textarea name="order_notes" placeholder="Catatan tambahan (opsional)"><?php echo $action === 'edit' ? htmlspecialchars($transaction['order_notes']) : ''; ?></textarea>
                            </div>
                            
                        <?php else: ?>
                            <!-- PURCHASE FORM -->
                            <!-- <div class="form-group">
                               
                            </div> -->
                            
                            <div class="form-group">
                                <label>Supplier Name *</label>
                                <input type="text" name="supplier_name" required 
                                    value="<?php echo $action === 'edit' ? htmlspecialchars($transaction['supplier_name']) : ''; ?>"
                                    placeholder="Nama supplier">
                            </div>
                            
                            <div class="form-group">
                                <label>Supplier Phone *</label>
                                <input type="text" name="supplier_phone" required 
                                    value="<?php echo $action === 'edit' ? htmlspecialchars($transaction['supplier_phone']) : ''; ?>"
                                    placeholder="0xx-xxxx-xxxx">
                            </div>
                            
                            <div class="form-group">
                                <label>Supplier Address *</label>
                                <textarea name="supplier_address" required placeholder="Alamat lengkap supplier"><?php echo $action === 'edit' ? htmlspecialchars($transaction['supplier_address']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Total Amount *</label>
                                <input type="number" name="total_amount" step="0.01" required 
                                    value="<?php echo $action === 'edit' ? $transaction['total_amount'] : ''; ?>"
                                    placeholder="0.00">
                            </div>
                            
                            <div class="form-group">
                                <label>Payment Method *</label>
                                <select name="payment_method" required>
                                    <option value="">Pilih Metode</option>
                                    <option value="transfer_bank" <?php echo ($action === 'edit' && $transaction['payment_method'] == 'transfer_bank') ? 'selected' : ''; ?>>Transfer Bank</option>
                                    <option value="cash" <?php echo ($action === 'edit' && $transaction['payment_method'] == 'cash') ? 'selected' : ''; ?>>Cash</option>
                                    <option value="credit" <?php echo ($action === 'edit' && $transaction['payment_method'] == 'credit') ? 'selected' : ''; ?>>Credit</option>
                                </select>
                            </div>
                            
                           <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required>
                                    <option value="pending" <?php echo ($action === 'edit' && $transaction['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="received" <?php echo ($action === 'edit' && $transaction['status'] == 'received') ? 'selected' : ''; ?>>Received</option>
                                    <option value="cancelled" <?php echo ($action === 'edit' && $transaction['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Admin ID</label>
                                <input type="text" name="admin_id" 
                                    value="<?php echo $action === 'edit' ? htmlspecialchars($transaction['admin_id'] ?? '') : ''; ?>"
                                    placeholder="ID Admin (opsional)">
                            </div>
                            
                            <div class="form-group">
                                <label>Purchase Notes</label>
                                <textarea name="purchase_notes" placeholder="Catatan tambahan (opsional)"><?php echo $action === 'edit' ? htmlspecialchars($transaction['purchase_notes']) : ''; ?></textarea>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-submit">
                                <?php echo $action === 'add' ? 'Tambah' : 'Update'; ?>
                            </button>
                            <a href="transaction.php" class="btn-cancel">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
    closeConnection($conn);
    ?>
</body>
</html>
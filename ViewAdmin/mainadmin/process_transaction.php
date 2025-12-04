<?php
session_start();
require_once '../../Connection/connect.php';

$conn = getConnection();

// Ambil parameter dari URL
$action = $_GET['action'] ?? 'view';
$type = $_GET['type'] ?? 'order';
$id = $_GET['id'] ?? null;

// ====== FUNGSI GENERATE ID (SAFE LOOP) ======
function generateOrderId($conn) {
    $query = "SELECT MAX(CAST(SUBSTRING(order_id, 3) AS UNSIGNED)) as max_num FROM orders WHERE order_id LIKE 'O-%'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $nextNum = ($row['max_num'] ?? 0) + 1;

    do {
        $newId = 'O-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        $check = $conn->query("SELECT 1 FROM orders WHERE order_id = '$newId'");
        if ($check && $check->num_rows > 0) $nextNum++;
        else break;
    } while (true);
    return $newId;
}

function generatePurchaseId($conn) {
    $query = "SELECT MAX(CAST(SUBSTRING(purchase_id, 5) AS UNSIGNED)) as max_num FROM purchases WHERE purchase_id LIKE 'PUR-%'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $nextNum = ($row['max_num'] ?? 0) + 1;

    do {
        $newId = 'PUR-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        $check = $conn->query("SELECT 1 FROM purchases WHERE purchase_id = '$newId'");
        if ($check && $check->num_rows > 0) $nextNum++;
        else break;
    } while (true);
    return $newId;
}

// ====== HANDLE POST REQUEST ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? null;
    $postType = $_POST['type'] ?? null;
    
    if ($postAction === 'add') {
        if ($postType === 'order') {
            $newId = generateOrderId($conn);
            $stmt = $conn->prepare("INSERT INTO orders (order_id, user_id, user_name, user_email, user_phone, user_address, total_amount, payment_method, status, order_notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            
            // PERBAIKAN 1: Cek isset untuk user_id agar tidak error Warning
            $uid = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
            
            // PERBAIKAN 2: Pastikan data payment_method sesuai ENUM (cod, transfer, ewallet)
            // Jika user memilih Cash, kita mapping ke COD untuk database
            $paymentMap = $_POST['payment_method'];
            if($paymentMap == 'cash') $paymentMap = 'cod';

            $stmt->bind_param("ssssssdsss", $newId, $uid, $_POST['user_name'], $_POST['user_email'], $_POST['user_phone'], $_POST['user_address'], $_POST['total_amount'], $paymentMap, $_POST['status'], $_POST['order_notes']);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Order berhasil ditambahkan!";
                header("Location: transaction.php");
            } else {
                $_SESSION['error'] = "Gagal: " . $stmt->error;
                header("Location: process_transaction.php?action=add&type=order");
            }
        } 
        elseif ($postType === 'purchase') {
            $newId = generatePurchaseId($conn);
            $stmt = $conn->prepare("INSERT INTO purchases (purchase_id, supplier_id, supplier_name, supplier_phone, supplier_address, total_amount, payment_method, status, purchase_notes, admin_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $admId = $_SESSION['admin_id'] ?? null;
            
            $stmt->bind_param("ssssssdsss", $newId, $_POST['supplier_id'], $_POST['supplier_name'], $_POST['supplier_phone'], $_POST['supplier_address'], $_POST['total_amount'], $_POST['payment_method'], $_POST['status'], $_POST['purchase_notes'], $admId);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Purchase berhasil ditambahkan!";
                header("Location: transaction.php");
            } else {
                $_SESSION['error'] = "Gagal: " . $stmt->error;
                header("Location: process_transaction.php?action=add&type=purchase");
            }
        }
        exit();
    }
    
    // --- EDIT & DELETE LOGIC SAMA SEPERTI SEBELUMNYA ---
    elseif ($postAction === 'edit') {
        $editId = $_POST['id'];
        if ($postType === 'order') {
            $stmt = $conn->prepare("UPDATE orders SET user_name=?, user_email=?, user_phone=?, user_address=?, total_amount=?, payment_method=?, status=?, order_notes=?, updated_at=NOW() WHERE order_id=?");
            $stmt->bind_param("ssssdssss", $_POST['user_name'], $_POST['user_email'], $_POST['user_phone'], $_POST['user_address'], $_POST['total_amount'], $_POST['payment_method'], $_POST['status'], $_POST['order_notes'], $editId);
        } else {
            $stmt = $conn->prepare("UPDATE purchases SET supplier_id=?, supplier_name=?, supplier_phone=?, supplier_address=?, total_amount=?, payment_method=?, status=?, purchase_notes=?, updated_at=NOW() WHERE purchase_id=?");
            $stmt->bind_param("ssssdssss", $_POST['supplier_id'], $_POST['supplier_name'], $_POST['supplier_phone'], $_POST['supplier_address'], $_POST['total_amount'], $_POST['payment_method'], $_POST['status'], $_POST['purchase_notes'], $editId);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Data berhasil diupdate!";
            header("Location: transaction.php");
        } else {
            $_SESSION['error'] = "Gagal update: " . $stmt->error;
            header("Location: process_transaction.php?action=edit&type=$postType&id=$editId");
        }
        exit();
    }
    elseif ($postAction === 'delete') {
        $delId = $_POST['id'];
        $table = ($postType === 'order') ? 'orders' : 'purchases';
        $col = ($postType === 'order') ? 'order_id' : 'purchase_id';
        $conn->query("DELETE FROM $table WHERE $col = '$delId'");
        $_SESSION['success'] = "Data berhasil dihapus!";
        header("Location: transaction.php");
        exit();
    }
}

// ====== PREPARE VIEW DATA ======
$transaction = [];
if (($action === 'view' || $action === 'edit') && $id) {
    if ($type === 'order') {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM purchases WHERE purchase_id = ?");
    }
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $transaction = $stmt->get_result()->fetch_assoc();
}

$suppliers = [];
if ($type === 'purchase' && ($action === 'add' || $action === 'edit')) {
    $supQ = $conn->query("SELECT * FROM suppliers ORDER BY company_name ASC");
    while($row = $supQ->fetch_assoc()) $suppliers[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($action) . " " . ucfirst($type) ?></title>
    <link rel="stylesheet" href="../cssadmin/process_transaction.css">
    <script>
    function updateSupplierInfo(select) {
        var option = select.options[select.selectedIndex];
        if(option.value) {
            document.getElementById('supplier_name').value = option.getAttribute('data-name');
            document.getElementById('supplier_phone').value = option.getAttribute('data-phone');
            document.getElementById('supplier_address').value = option.getAttribute('data-address');
        } else {
            document.getElementById('supplier_name').value = '';
            document.getElementById('supplier_phone').value = '';
            document.getElementById('supplier_address').value = '';
        }
    }
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="main-content">
        <div class="container">
            <h1><?php if ($action === 'add') echo "➕ Tambah "; elseif ($action === 'edit') echo "✏️ Edit "; elseif ($action === 'view') echo "👁️ Detail "; echo ucfirst($type); ?></h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <?php if ($action === 'view'): ?>
                <div class="detail-view">
                    <?php foreach ($transaction as $key => $val): ?>
                        <div class="detail-row">
                            <div class="detail-label"><?= ucwords(str_replace('_', ' ', $key)) ?></div>
                            <div class="detail-value"><?= htmlspecialchars($val ?? '-') ?></div>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-actions">
                        <a href="process_transaction.php?action=edit&type=<?= $type ?>&id=<?= $id ?>" class="btn-submit">Edit</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus permanen?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="type" value="<?= $type ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <button class="btn-delete">Hapus</button>
                        </form>
                        <a href="transaction.php" class="btn-cancel">Kembali</a>
                    </div>
                </div>

            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <div class="form-container">
                    <form method="POST">
                        <input type="hidden" name="action" value="<?= $action ?>">
                        <input type="hidden" name="type" value="<?= $type ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?= $id ?>">
                        <?php endif; ?>

                        <?php if ($type === 'purchase'): ?>
                            <div class="form-group">
                                <label>Pilih Supplier *</label>
                                <select name="supplier_id" required onchange="updateSupplierInfo(this)" style="width:100%; padding:10px;">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach($suppliers as $sup): $selected = ($action == 'edit' && $transaction['supplier_id'] == $sup['supplier_id']) ? 'selected' : ''; ?>
                                        <option value="<?= $sup['supplier_id'] ?>" data-name="<?= htmlspecialchars($sup['company_name']) ?>" data-phone="<?= htmlspecialchars($sup['phone_number']) ?>" data-address="<?= htmlspecialchars($sup['address']) ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($sup['company_name']) ?> (<?= $sup['supplier_id'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <input type="hidden" name="supplier_name" id="supplier_name" value="<?= $transaction['supplier_name'] ?? '' ?>">
                            <div class="form-group"><label>No Telepon</label><input type="text" name="supplier_phone" id="supplier_phone" readonly style="background:#eee;" value="<?= $transaction['supplier_phone'] ?? '' ?>"></div>
                            <div class="form-group"><label>Alamat</label><textarea name="supplier_address" id="supplier_address" readonly style="background:#eee;"><?= $transaction['supplier_address'] ?? '' ?></textarea></div>
                            <div class="form-group"><label>Total Amount</label><input type="number" name="total_amount" required value="<?= $transaction['total_amount'] ?? '' ?>"></div>
                            <div class="form-group"><label>Payment Method</label>
                                <select name="payment_method">
                                    <option value="transfer_bank">Transfer Bank</option>
                                    <option value="cash">Cash</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Status</label><select name="status"><option value="pending">Pending</option><option value="received">Received</option><option value="cancelled">Cancelled</option></select></div>
                            <div class="form-group"><label>Notes</label><textarea name="purchase_notes"><?= $transaction['purchase_notes'] ?? '' ?></textarea></div>

                        <?php else: ?>
                            <div class="form-group"><label>Nama Customer *</label><input type="text" name="user_name" required value="<?= $transaction['user_name'] ?? '' ?>"></div>
                            <div class="form-group"><label>Email</label><input type="email" name="user_email" value="<?= $transaction['user_email'] ?? '' ?>"></div>
                            <div class="form-group"><label>Telepon</label><input type="text" name="user_phone" value="<?= $transaction['user_phone'] ?? '' ?>"></div>
                            <div class="form-group"><label>Alamat</label><textarea name="user_address"><?= $transaction['user_address'] ?? '' ?></textarea></div>
                            <div class="form-group"><label>Total Amount *</label><input type="number" name="total_amount" required value="<?= $transaction['total_amount'] ?? '' ?>"></div>
                            <div class="form-group"><label>Status</label><select name="status"><option value="pending">Pending</option><option value="processing">Processing</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select name="payment_method">
                                    <option value="cod" <?= ($transaction['payment_method']??'') == 'cod' ? 'selected' : '' ?>>Cash (COD)</option>
                                    <option value="transfer" <?= ($transaction['payment_method']??'') == 'transfer' ? 'selected' : '' ?>>Transfer</option>
                                    <option value="ewallet" <?= ($transaction['payment_method']??'') == 'ewallet' ? 'selected' : '' ?>>E-Wallet</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Notes</label><textarea name="order_notes"><?= $transaction['order_notes'] ?? '' ?></textarea></div>
                        <?php endif; ?>

                        <div class="form-actions"><button type="submit" class="btn-submit">Simpan</button><a href="transaction.php" class="btn-cancel">Batal</a></div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php closeConnection($conn); ?>
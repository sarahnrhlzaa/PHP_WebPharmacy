<?php
session_start();
require_once '../../Connection/connect.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Session expired. Please login again.',
            'redirect' => 'login.php'
        ]);
        exit();
    }
    
    header('Location: login.php');
    exit();
}

// Initialize cart in session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ========== AJAX HANDLER ==========
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($isAjax && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    
    // ADD TO CART
    if ($action === 'add') {
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $image = $_POST['image'] ?? '';
        $quantity = intval($_POST['quantity'] ?? 1);
        
        if (empty($id) || empty($name) || $price <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product data']);
            exit;
        }
        
        // Add or update cart
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $quantity;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'image' => $image,
                'qty' => $quantity
            ];
        }
        
        // Calculate total items
        $totalItems = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalItems += $item['qty'];
        }
        
        echo json_encode([
            'success' => true,
            'message' => $name . ' added to cart!',
            'cartCount' => $totalItems,
            'cart' => $_SESSION['cart']
        ]);
        exit;
    }
    
    // GET CART DATA
    if ($action === 'get_cart') {
        $totalItems = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalItems += $item['qty'];
        }
        
        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart'],
            'cartCount' => $totalItems
        ]);
        exit;
    }
    
    // UPDATE QUANTITY
    if ($action === 'update_qty') {
        $id = $_POST['id'] ?? '';
        $qty = intval($_POST['qty'] ?? 1);
        
        if (isset($_SESSION['cart'][$id]) && $qty > 0) {
            $_SESSION['cart'][$id]['qty'] = $qty;
        }
        
        $totalItems = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalItems += $item['qty'];
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Quantity updated',
            'cartCount' => $totalItems
        ]);
        exit;
    }
    
    // REMOVE FROM CART
    if ($action === 'remove') {
        $id = $_POST['id'] ?? '';
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        
        $totalItems = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalItems += $item['qty'];
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart',
            'cartCount' => $totalItems
        ]);
        exit;
    }
    
    // CLEAR CART
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        
        echo json_encode([
            'success' => true,
            'message' => 'Cart cleared',
            'cartCount' => 0
        ]);
        exit;
    }
}

// ========== CALCULATE TOTALS FOR DISPLAY ==========
$conn = getConnection();

$subtotal = 0;
$shipping = 10000; // Rp 10.000

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['qty'];
}

$total = $subtotal + $shipping;

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../cssuser/cart.css">
    <link href="https://fonts.googleapis.com/css2?family=Andika&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- TAMBAH INI (MULAI) -->
<?php if (isset($_SESSION['checkout_error'])): ?>
<div style="background: #ffebee; color: #c62828; padding: 15px; text-align: center; margin: 20px; border-radius: 5px;">
    <i class="fa fa-exclamation-triangle"></i>
    <?php 
    echo $_SESSION['checkout_error']; 
    unset($_SESSION['checkout_error']);
    ?>
</div>
<?php endif; ?>
<!-- TAMBAH INI (SELESAI) -->

<div class="cart-container">
    <h1>Shopping Cart</h1>
    
    <div class="cart-content">
        <!-- Cart Items Section -->
        <div class="cart-items-section">
            
            <?php if (empty($_SESSION['cart'])): ?>
                <!-- EMPTY CART -->
                <div id="empty-cart-message" style="text-align: center; padding: 40px;">
                    <i class="fa fa-shopping-cart" style="font-size: 64px; color: #ccc;"></i>
                    <h3>Your cart is empty</h3>
                    <a href="medicine.php" class="btn-continue-shopping">Continue Shopping</a>
                </div>
                
            <?php else: ?>
                <!-- CART HAS ITEMS -->
                <div class="select-all-container">
                    <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                    <label for="select-all">Select All</label>
                    <button class="btn-clear" onclick="clearCart()" style="margin-left: auto;">
                        <i class="fa fa-trash"></i> Clear Cart
                    </button>
                </div>
                
                <div id="cart-items-list">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="cart-item" data-id="<?php echo htmlspecialchars($item['id']); ?>">
                            <input type="checkbox" class="item-checkbox" onchange="updateSummary()">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 onerror="this.src='../assets/default.jpg'">
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="item-price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                            </div>
                            <div class="item-quantity">
                                <button class="qty-btn" onclick="updateQuantity('<?php echo $item['id']; ?>', -1)">-</button>
                                <input type="number" 
                                       class="qty-input" 
                                       value="<?php echo $item['qty']; ?>" 
                                       min="1" 
                                       readonly>
                                <button class="qty-btn" onclick="updateQuantity('<?php echo $item['id']; ?>', 1)">+</button>
                            </div>
                            <div class="item-subtotal" data-price="<?php echo $item['price']; ?>" data-qty="<?php echo $item['qty']; ?>">
                                <span>Rp <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?></span>
                            </div>
                            <button class="btn-remove" onclick="removeItem('<?php echo $item['id']; ?>')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Order Summary Section -->
        <div class="order-summary">
            <h2>Order Summary</h2>
            
            <div class="summary-details">
                <div class="summary-row">
                    <span>Selected Items:</span>
                    <span id="selected-count">0</span>
                </div>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal-price">Rp 0</span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span id="shipping-cost">Rp 0</span>
                </div>
                <hr>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span id="total-price">Rp 0</span>
                </div>
            </div>
            
            <?php if (!empty($_SESSION['cart'])): ?>
                <button class="btn-checkout" onclick="proceedToCheckout()" id="checkout-btn">
                    Proceed to Checkout
                </button>
                <p id="checkout-warning" style="color: #e74c3c; font-size: 12px; margin-top: 10px; display: none;">
                    Please select at least one item
                </p>
            <?php endif; ?>
            
            <a href="medicine.php" class="btn-continue-shopping">Continue Shopping</a>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast-notification" id="toast-notification">
    <i class="fa fa-check-circle"></i>
    <span id="toast-message">Success!</span>
</div>

<?php include 'footer.php'; ?>

<script src="../jsuser/cart.js">
</script>

</body>
</html>
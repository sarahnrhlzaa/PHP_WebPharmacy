<!-- 
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login first'
    ]);
    exit();
}

// Get action
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle different actions
switch ($action) {
    
    // ADD TO CART
    case 'add':
        $medicine_id = isset($_POST['medicine_id']) ? intval($_POST['medicine_id']) : 0;
        $medicine_name = isset($_POST['medicine_name']) ? $_POST['medicine_name'] : '';
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
        $image = isset($_POST['image']) ? $_POST['image'] : '';
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        
        // Validate
        if ($medicine_id <= 0 || empty($medicine_name) || $price <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid product data'
            ]);
            exit();
        }
        
        // Check if item exists
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['medicine_id'] == $medicine_id) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        
        // Add new item if not found
        if (!$found) {
            $_SESSION['cart'][] = [
                'medicine_id' => $medicine_id,
                'medicine_name' => $medicine_name,
                'price' => $price,
                'image' => $image,
                'quantity' => $quantity
            ];
        }
        
        // Calculate total items
        $total_items = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_items += $item['quantity'];
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => $total_items
        ]);
        break;
    
    
    // GET CART
    case 'get':
        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart']
        ]);
        break;
    
    
    // GET CART COUNT
    case 'count':
        $total_items = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_items += $item['quantity'];
        }
        
        echo json_encode([
            'success' => true,
            'count' => $total_items
        ]);
        break;
    
    
    // UPDATE QUANTITY
    case 'update':
        $medicine_id = isset($_POST['medicine_id']) ? intval($_POST['medicine_id']) : 0;
        $update_action = isset($_POST['update_action']) ? $_POST['update_action'] : '';
        
        if ($medicine_id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid product ID'
            ]);
            exit();
        }
        
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['medicine_id'] == $medicine_id) {
                $found = true;
                
                if ($update_action === 'increase') {
                    $item['quantity']++;
                } elseif ($update_action === 'decrease') {
                    if ($item['quantity'] > 1) {
                        $item['quantity']--;
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Minimum quantity is 1'
                        ]);
                        exit();
                    }
                }
                break;
            }
        }
        
        if (!$found) {
            echo json_encode([
                'success' => false,
                'message' => 'Item not found in cart'
            ]);
            exit();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Cart updated successfully'
        ]);
        break;
    
    
    // REMOVE FROM CART
    case 'remove':
        $medicine_id = isset($_POST['medicine_id']) ? intval($_POST['medicine_id']) : 0;
        
        if ($medicine_id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid product ID'
            ]);
            exit();
        }
        
        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['medicine_id'] == $medicine_id) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            echo json_encode([
                'success' => false,
                'message' => 'Item not found in cart'
            ]);
            exit();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
        break;
    
    
    // PREPARE CHECKOUT
    case 'checkout':
        $selected_items = isset($_POST['selected_items']) ? json_decode($_POST['selected_items'], true) : [];
        
        if (empty($selected_items)) {
            echo json_encode([
                'success' => false,
                'message' => 'No items selected'
            ]);
            exit();
        }
        
        // Filter selected items
        $checkout_items = [];
        foreach ($_SESSION['cart'] as $item) {
            if (in_array($item['medicine_id'], $selected_items)) {
                $checkout_items[] = $item;
            }
        }
        
        if (empty($checkout_items)) {
            echo json_encode([
                'success' => false,
                'message' => 'Selected items not found in cart'
            ]);
            exit();
        }
        
        // Store checkout items
        $_SESSION['checkout_items'] = $checkout_items;
        
        echo json_encode([
            'success' => true,
            'message' => 'Ready to checkout'
        ]);
        break;
    
    
    // CLEAR CART
    case 'clear':
        $_SESSION['cart'] = [];
        echo json_encode([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
        break;
    
    
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
} -->

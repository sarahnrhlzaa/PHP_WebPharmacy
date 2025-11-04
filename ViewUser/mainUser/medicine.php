<?php
session_start();
require_once '../../Connection/connect.php';

// Get all medicines from database
$conn = getConnection();

// Get wellness products
$sql_wellness = "SELECT medicine_id, medicine_name, category, price, image_path, description, 
                 benefits, dosage, warnings, rating, tag
                 FROM medicines
                 WHERE category = 'wellness' 
                 ORDER BY medicine_id";
$result_wellness = $conn->query($sql_wellness);
$wellness_products = $result_wellness->fetch_all(MYSQLI_ASSOC);

// Get medicine products
$sql_medicine = "SELECT medicine_id, medicine_name, category, price, image_path, description, 
                 benefits, dosage, warnings, rating, tag
                 FROM medicines
                 WHERE category = 'medicine' 
                 ORDER BY medicine_id";
$result_medicine = $conn->query($sql_medicine);
$medicine_products = $result_medicine->fetch_all(MYSQLI_ASSOC);

closeConnection($conn);

// Function to display star rating
function displayStars($rating) {
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
    $empty_stars = 5 - $full_stars - $half_star;
    
    $html = '';
    for ($i = 0; $i < $full_stars; $i++) {
        $html .= '<i class="fa fa-star"></i>';
    }
    if ($half_star) {
        $html .= '<i class="fa fa-star-half"></i>';
    }
    for ($i = 0; $i < $empty_stars; $i++) {
        $html .= '<i class="fa fa-star-o"></i>';
    }
    return $html;
}

// Function to format price
function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

// Function to get correct image path - FIXED to prevent infinite reload
function getImagePath($image_path) {
    // If path is empty or null, return default
    if (empty($image_path)) {
        return '../assets/default.jpg';
    }
    
    // If path already starts with ../, return as is
    if (strpos($image_path, '../') === 0) {
        return $image_path;
    }
    
    // If path starts with ../../, fix it to ../
    if (strpos($image_path, '../../') === 0) {
        return str_replace('../../', '../', $image_path);
    }
    
    // If only filename, add ../assets/ prefix
    if (strpos($image_path, '/') === false) {
        return '../assets/' . $image_path;
    }
    
    // Otherwise return as is
    return $image_path;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Page</title>
    <link rel="stylesheet" href="../cssuser/medicine.css">
    <link href="https://fonts.googleapis.com/css2?family=Andika&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<header>
    <div class="icon">
        <div class="fa fa-upload" id="up-btn"></div>
    </div>
    <div class="upload-form">
        <input class="default-btn" type="file" hidden>
        <div class="btn">
            <button onclick="active()" class="default-btn">Upload Prescription</button>
            <i class="fa fa-upload"></i>
        </div>
        <div class="file-name">No file chosen</div>
    </div>
</header>

<!-- WELLNESS SECTION -->
<section class="top" id="top">
    <div class="container">
        <h1 class="heading">WELLNESS</h1><hr>
    </div>

    <div class="box-container">
        <?php foreach ($wellness_products as $product): ?>
        <div class="box">
            <div class="slide-img">
                <img src="<?php echo htmlspecialchars(getImagePath($product['image_path'])); ?>" 
                     alt="<?php echo htmlspecialchars($product['medicine_name']); ?>">
                <div class="overlay">
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <a href="#" class="learn-btn" 
                       data-id="<?php echo $product['medicine_id']; ?>"
                       data-name="<?php echo htmlspecialchars($product['medicine_name']); ?>"
                       data-description="<?php echo htmlspecialchars($product['description']); ?>"
                       data-benefits="<?php echo htmlspecialchars($product['benefits']); ?>"
                       data-dosage="<?php echo htmlspecialchars($product['dosage']); ?>"
                       data-warnings="<?php echo htmlspecialchars($product['warnings']); ?>"
                       data-img="<?php echo htmlspecialchars(getImagePath($product['image_path'])); ?>">
                       Learn More
                    </a>
                </div>
            </div>
            <div class="star">
                <?php echo displayStars($product['rating']); ?>
            </div>
            <div class="detail-box">
                <div class="type">
                    <a href="#"><?php echo htmlspecialchars($product['medicine_name']); ?></a>
                    <span><?php echo htmlspecialchars($product['tag']); ?></span>
                </div>
                <a href="#" class="price"><?php echo formatPrice($product['price']); ?></a>
            </div>
            <a href="checkout.php?medicine_id=<?php echo $product['medicine_id']; ?>" class="my-button order-now-btn">Order Now</a>
            <a href="#" class="my-button add-to-cart-btn" 
               data-id="<?php echo $product['medicine_id']; ?>" 
               data-name="<?php echo htmlspecialchars($product['medicine_name']); ?>" 
               data-price="<?php echo $product['price']; ?>" 
               data-img="<?php echo htmlspecialchars(getImagePath($product['image_path'])); ?>">
               Add to Cart
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- MEDICINE SECTION -->
<section class="bottom" id="bottom">
    <div class="container">
        <h1 class="heading">MEDICINE</h1><hr>
    </div>

    <div class="box-container">
        <?php foreach ($medicine_products as $product): ?>
        <div class="box">
            <div class="slide-img">
                <img src="<?php echo htmlspecialchars(getImagePath($product['image_path'])); ?>" 
                     alt="<?php echo htmlspecialchars($product['medicine_name']); ?>">
                <div class="overlay">
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <a href="#" class="learn-btn"
                       data-id="<?php echo $product['medicine_id']; ?>"
                       data-name="<?php echo htmlspecialchars($product['medicine_name']); ?>"
                       data-description="<?php echo htmlspecialchars($product['description']); ?>"
                       data-benefits="<?php echo htmlspecialchars($product['benefits']); ?>"
                       data-dosage="<?php echo htmlspecialchars($product['dosage']); ?>"
                       data-warnings="<?php echo htmlspecialchars($product['warnings']); ?>"
                       data-img="<?php echo htmlspecialchars(getImagePath($product['image_path'])); ?>">
                       Learn More
                    </a>
                </div>
            </div>
            <div class="star">
                <?php echo displayStars($product['rating']); ?>
            </div>
            <div class="detail-box">
                <div class="type">
                    <a href="#"><?php echo htmlspecialchars($product['medicine_name']); ?></a>
                    <span><?php echo htmlspecialchars($product['tag']); ?></span>
                </div>
                <a href="#" class="price"><?php echo formatPrice($product['price']); ?></a>
            </div>
            <a href="checkout.php?medicine_id=<?php echo $product['medicine_id']; ?>" class="my-button order-now-btn">Order Now</a>
            <a href="#" class="my-button add-to-cart-btn" 
               data-id="<?php echo $product['medicine_id']; ?>" 
               data-name="<?php echo htmlspecialchars($product['medicine_name']); ?>" 
               data-price="<?php echo $product['price']; ?>" 
               data-img="<?php echo htmlspecialchars(getImagePath($product['image_path'])); ?>">
               Add to Cart
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>
    
<!-- Modal Pop-up untuk Learn More -->
<div class="modal-overlay" id="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" id="close-modal">&times;</span>
        <h2 id="modal-title">Product Name</h2>
        <div class="modal-body">
            <img id="modal-image" src="" alt="">
            <div class="modal-details">
                <h3>Product Description</h3>
                <p id="modal-description"></p>
                <h3>Benefits</h3>
                <p id="modal-benefits"></p>
                <h3>Dosage</h3>
                <p id="modal-dosage"></p>
                <h3>Warnings</h3>
                <p id="modal-warnings"></p>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast-notification" id="toast-notification">
    <i class="fa fa-check-circle"></i>
    <span id="toast-message">Item added to cart!</span>
</div>
<script src="../jsUser/medicine.js"></script>
</body>
</html>
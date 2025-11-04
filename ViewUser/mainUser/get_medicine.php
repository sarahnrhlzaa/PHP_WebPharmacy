<?php
require_once '../../Connection/connect.php';

// Function to fix image path
function fixImagePath($image_path) {
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

// Get category from query parameter (wellness or medicine)
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Get database connection
$conn = getConnection();

// Prepare SQL query - NO supplier/stock info for user view
if ($category === 'all') {
    $sql = "SELECT medicine_id, medicine_name, category, price, image_path, description, 
            benefits, dosage, warnings, rating, tag
            FROM medicines 
            ORDER BY category, medicine_id";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT medicine_id, medicine_name, category, price, image_path, description, 
            benefits, dosage, warnings, rating, tag
            FROM medicines 
            WHERE category = ? 
            ORDER BY medicine_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
}

// Execute query
$stmt->execute();
$result = $stmt->get_result();

// Fetch all medicines and fix image paths
$medicines = array();
while ($row = $result->fetch_assoc()) {
    // Fix image path before adding to array
    $row['image_path'] = fixImagePath($row['image_path']);
    $medicines[] = $row;
}

// Close connection
$stmt->close();
closeConnection($conn);

// Return as JSON (untuk AJAX calls) atau return array (untuk include dalam PHP)
if (isset($_GET['json']) && $_GET['json'] == 'true') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $medicines,
        'count' => count($medicines)
    ]);
} else {
    return $medicines;
}
?>
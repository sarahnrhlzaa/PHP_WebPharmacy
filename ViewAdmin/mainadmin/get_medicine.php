<?php
require_once '../config/database.php';

// Get category from query parameter (wellness or medicine)
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Get database connection
$conn = getDBConnection();

// Prepare SQL query with JOIN to get supplier information
if ($category === 'all') {
    $sql = "SELECT m.*, s.company_name, s.phone_number, s.address as supplier_address 
            FROM medicines m 
            LEFT JOIN suppliers s ON m.supplier_id = s.supplier_id 
            ORDER BY m.category, m.medicine_id";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT m.*, s.company_name, s.phone_number, s.address as supplier_address 
            FROM medicines m 
            LEFT JOIN suppliers s ON m.supplier_id = s.supplier_id 
            WHERE m.category = ? 
            ORDER BY m.medicine_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
}

// Execute query
$stmt->execute();
$result = $stmt->get_result();

// Fetch all medicines
$medicines = array();
while ($row = $result->fetch_assoc()) {
    $medicines[] = $row;
}

// Close connection
$stmt->close();
closeDBConnection($conn);

// Return as JSON (untuk AJAX calls) atau return array (untuk include dalam PHP)
if (isset($_GET['json']) && $_GET['json'] == 'true') {
    header('Content-Type: application/json');
    echo json_encode($medicines);
} else {
    return $medicines;
}
?>
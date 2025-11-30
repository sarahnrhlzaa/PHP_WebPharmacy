<?php
require_once '../../Connection/connect.php';
$conn = getConnection();

echo "<style>
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #4f46e5; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    h2 { color: #333; margin-top: 30px; }
</style>";

// Lihat data ORDERS
echo "<h2>📦 Data Orders:</h2>";
$result = $conn->query("SELECT * FROM orders");
if ($result->num_rows > 0) {
    echo "<table>";
    // Header
    $first = true;
    while($row = $result->fetch_assoc()) {
        if ($first) {
            echo "<tr>";
            foreach(array_keys($row) as $col) {
                echo "<th>$col</th>";
            }
            echo "</tr>";
            $first = false;
        }
        echo "<tr>";
        foreach($row as $val) {
            echo "<td>" . htmlspecialchars($val ?? '-') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Tidak ada data orders</p>";
}

// Lihat data PURCHASES
echo "<h2>🛒 Data Purchases:</h2>";
$result = $conn->query("SELECT * FROM purchases");
if ($result->num_rows > 0) {
    echo "<table>";
    $first = true;
    while($row = $result->fetch_assoc()) {
        if ($first) {
            echo "<tr>";
            foreach(array_keys($row) as $col) {
                echo "<th>$col</th>";
            }
            echo "</tr>";
            $first = false;
        }
        echo "<tr>";
        foreach($row as $val) {
            echo "<td>" . htmlspecialchars($val ?? '-') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Tidak ada data purchases</p>";
}

// Lihat data USERS (beberapa kolom saja)
echo "<h2>👥 Data Users:</h2>";
$result = $conn->query("SELECT user_id, username, full_name, email, phone_number FROM users");
if ($result->num_rows > 0) {
    echo "<table>";
    $first = true;
    while($row = $result->fetch_assoc()) {
        if ($first) {
            echo "<tr>";
            foreach(array_keys($row) as $col) {
                echo "<th>$col</th>";
            }
            echo "</tr>";
            $first = false;
        }
        echo "<tr>";
        foreach($row as $val) {
            echo "<td>" . htmlspecialchars($val ?? '-') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Tidak ada data users</p>";
}

closeConnection($conn);
?>
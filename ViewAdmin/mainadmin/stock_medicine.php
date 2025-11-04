<?php
// session_start();
require_once '../../Connection/connect.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../login.php');
//     exit();
// }

// Fetch medicines with supplier info
$query = "SELECT m.*, s.company_name as supplier_name 
          FROM medicines m 
          LEFT JOIN suppliers s ON m.supplier_id = s.supplier_id 
          ORDER BY m.medicine_name ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../cssadmin/stock_medicine.css">
    <title>Stock Medicine - PharmaCare</title>
</head>
<body>

<?php include 'navbar.php'?>
    <div class="container">
        <div class="title-stock">
            <h1>📦 Stock Medicine</h1>
            <div class="search-add-container">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" placeholder="Cari obat...">
                </div>
                <button class="btn-add" onclick="openModal()">
                    <span style="font-size: 20px;">+</span>
                    Tambah Obat
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
        </div>
        <?php endif; ?>

        <div class="table-container">
            <table id="medicineTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Obat</th>
                        <th>Supplier</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stock</th>
                        <th>Rating</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['medicine_id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['medicine_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['supplier_name'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge <?php echo $row['category'] == 'wellness' ? 'badge-wellness' : 'badge-medicine'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($row['category'])); ?>
                                    </span>
                                </td>
                                <td class="price">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php
                                    $stock = $row['stock'];
                                    $stockClass = $stock > 50 ? 'stock-high' : ($stock > 20 ? 'stock-medium' : 'stock-low');
                                    ?>
                                    <span class="stock-badge <?php echo $stockClass; ?>">
                                        <?php echo $stock; ?> unit
                                    </span>
                                </td>
                                <td>⭐ <?php echo number_format($row['rating'], 1); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-edit" onclick="editMedicine('<?php echo $row['medicine_id']; ?>')">Edit</button>
                                        <button class="btn-delete" onclick="deleteMedicine('<?php echo $row['medicine_id']; ?>')">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">
                                <div style="font-size: 48px;">📦</div>
                                <h3>Belum ada data obat</h3>
                                <p>Klik tombol "Tambah Obat" untuk menambahkan obat baru</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="medicineModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <iframe id="modalFrame" src="" style="width: 100%; height: 85vh; border: none;"></iframe>
        </div>
    </div>

    <script src="../jsadmin/stock_medicine.js"></script>
</body>
</html>
<?php
session_start();
require_once '../../Connection/connect.php';

// Dapatkan koneksi database
$conn = getConnection();

header('Content-Type: text/plain');

$action = $_POST['action'] ?? '';

switch($action) {
    case 'add':
        addSupplier($conn);
        break;
    case 'update':
        updateSupplier($conn);
        break;
    case 'delete':
        deleteSupplier($conn);
        break;
    case 'get':
        getSupplier($conn);
        break;
    case 'list':
        listSuppliers($conn);
        break;
    case 'generate_id':
        generateSupplierId($conn);
        break;
    default:
        echo 'error|Invalid action';
}

// Tutup koneksi setelah selesai
closeConnection($conn);

// ====== FUNGSI TAMBAH SUPPLIER ======
function addSupplier($conn) {
    $company_name = trim($_POST['company_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    if (empty($company_name) || empty($phone_number) || empty($address)) {
        echo 'error|Semua field wajib diisi';
        return;
    }
    
    // Generate ID otomatis
    $result = $conn->query("SELECT supplier_id FROM suppliers ORDER BY supplier_id DESC LIMIT 1");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['supplier_id'];
        $number = intval(substr($lastId, 3)) + 1;
    } else {
        $number = 1;
    }
    
    $supplier_id = 'SUP' . str_pad($number, 3, '0', STR_PAD_LEFT);
    
    // Insert supplier baru
    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_id, company_name, phone_number, address) 
                           VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $supplier_id, $company_name, $phone_number, $address);
    
    if ($stmt->execute()) {
        echo 'success|Supplier berhasil ditambahkan dengan ID: ' . $supplier_id;
    } else {
        echo 'error|Gagal menambahkan supplier: ' . $conn->error;
    }
    
    $stmt->close();
}

// ====== FUNGSI UPDATE SUPPLIER ======
function updateSupplier($conn) {
    $supplier_id = trim($_POST['supplier_id'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    if (empty($supplier_id) || empty($company_name) || empty($phone_number) || empty($address)) {
        echo 'error|Data tidak lengkap';
        return;
    }
    
    $stmt = $conn->prepare("UPDATE suppliers SET company_name=?, phone_number=?, address=? 
                           WHERE supplier_id=?");
    $stmt->bind_param("ssss", $company_name, $phone_number, $address, $supplier_id);
    
    if ($stmt->execute()) {
        echo 'success|Supplier berhasil diupdate!';
    } else {
        echo 'error|Gagal mengupdate supplier: ' . $conn->error;
    }
    
    $stmt->close();
}

// ====== FUNGSI HAPUS SUPPLIER ======
function deleteSupplier($conn) {
    $supplier_id = $_POST['supplier_id'] ?? '';
    
    if (empty($supplier_id)) {
        echo 'error|ID supplier tidak valid';
        return;
    }
    
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
    $stmt->bind_param("s", $supplier_id);
    
    if ($stmt->execute()) {
        echo 'success|Supplier berhasil dihapus!';
    } else {
        echo 'error|Gagal menghapus supplier: ' . $conn->error;
    }
    
    $stmt->close();
}

// ====== FUNGSI GET SUPPLIER BY ID ======
function getSupplier($conn) {
    $supplier_id = $_POST['supplier_id'] ?? '';
    
    if (empty($supplier_id)) {
        echo 'error|ID supplier tidak valid';
        return;
    }
    
    $stmt = $conn->prepare("SELECT supplier_id, company_name, phone_number, address 
                           FROM suppliers WHERE supplier_id = ?");
    $stmt->bind_param("s", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $supplier = $result->fetch_assoc();
        echo 'success|' . 
             $supplier['supplier_id'] . '|' . 
             $supplier['company_name'] . '|' . 
             $supplier['phone_number'] . '|' . 
             $supplier['address'];
    } else {
        echo 'error|Supplier tidak ditemukan';
    }
    
    $stmt->close();
}

// ====== FUNGSI LIST SEMUA SUPPLIER ======
function listSuppliers($conn) {
    $keyword = isset($_POST['search']) ? trim($_POST['search']) : '';
    
    if (!empty($keyword)) {
        $keyword = "%{$keyword}%";
        $stmt = $conn->prepare("SELECT supplier_id, company_name, phone_number, address 
                               FROM suppliers 
                               WHERE supplier_id LIKE ? OR company_name LIKE ? 
                               OR phone_number LIKE ? OR address LIKE ?
                               ORDER BY supplier_id ASC");
        $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT supplier_id, company_name, phone_number, address 
                FROM suppliers ORDER BY supplier_id ASC";
        $result = $conn->query($sql);
    }
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td class="supplier-id">' . htmlspecialchars($row['supplier_id']) . '</td>
                    <td class="company-name">' . htmlspecialchars($row['company_name']) . '</td>
                    <td>' . htmlspecialchars($row['phone_number']) . '</td>
                    <td>' . htmlspecialchars($row['address']) . '</td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-edit" onclick="editSupplier(\'' . $row['supplier_id'] . '\')">Edit</button>
                            <button class="btn-delete" onclick="deleteSupplier(\'' . $row['supplier_id'] . '\')">Hapus</button>
                        </div>
                    </td>
                </tr>';
        }
    } else {
        echo '<tr><td colspan="5" class="no-data">' . 
             (empty($keyword) ? 'Tidak ada data supplier' : 'Tidak ada hasil pencarian') . 
             '</td></tr>';
    }
    
    if(isset($stmt)) $stmt->close();
}

// ====== FUNGSI GENERATE ID BARU ======
function generateSupplierId($conn) {
    // Ambil supplier_id terakhir
    $result = $conn->query("SELECT supplier_id FROM suppliers ORDER BY supplier_id DESC LIMIT 1");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['supplier_id'];
        $number = intval(substr($lastId, 3)) + 1;
    } else {
        $number = 1;
    }
    
    $newId = 'SUP' . str_pad($number, 3, '0', STR_PAD_LEFT);
    
    // Echo untuk dikirim ke JavaScript
    echo 'success|' . $newId;
}
?>
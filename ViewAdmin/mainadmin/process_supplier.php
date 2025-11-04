<?php
session_start();
require_once '../../Connection/connect.php';

header('Content-Type: text/plain');

$action = $_POST['action'] ?? '';

switch($action) {
    case 'add':
        addSupplier();
        break;
    case 'update':
        updateSupplier();
        break;
    case 'delete':
        deleteSupplier();
        break;
    case 'get':
        getSupplier();
        break;
    case 'list':
        listSuppliers();
        break;
    case 'generate_id':
        generateSupplierId();
        break;
    default:
        echo 'error|Invalid action';
}

function addSupplier() {
    global $conn;
    
    // Gunakan fungsi untuk generate ID baru
    $supplier_id = generateSupplierId();
    
    // Ambil data supplier lainnya
    $company_name = trim($_POST['company_name']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    
    // Insert new supplier
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

function updateSupplier() {
    global $conn;
    
    $supplier_id = trim($_POST['supplier_id']);
    $company_name = trim($_POST['company_name']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    
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

function deleteSupplier() {
    global $conn;
    
    $supplier_id = $_POST['supplier_id'];
    
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
    $stmt->bind_param("s", $supplier_id);
    
    if ($stmt->execute()) {
        echo 'success|Supplier berhasil dihapus!';
    } else {
        echo 'error|Gagal menghapus supplier: ' . $conn->error;
    }
    
    $stmt->close();
}

function getSupplier() {
    global $conn;
    
    $supplier_id = $_POST['supplier_id'];
    
    $stmt = $conn->prepare("SELECT supplier_id, company_name, phone_number, address 
                           FROM suppliers WHERE supplier_id = ?");
    $stmt->bind_param("s", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
            if ($row) {
                $last_id = $row['user_id'];
                // Ambil angka terakhir setelah 'CUS'
                $last_number = (int) substr($last_id, 3);
                $new_id = 'SUP' . str_pad($last_number + 1, 3, '0', STR_PAD_LEFT); // Format CUS001, CUS002, ...
            } else {
                $new_id = 'SUP001'; // Jika belum ada data, mulai dari CUS001
            }

    if($result->num_rows > 0) {
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

function listSuppliers() {
    global $conn;
    
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

function generateSupplierId() {
    global $conn;
    
    // Ambil supplier_id terakhir
    $result = $conn->query("SELECT supplier_id FROM suppliers ORDER BY supplier_id DESC LIMIT 1");
    
    // Cek apakah ada data supplier
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['supplier_id'];
        // Ambil angka dari ID terakhir dan tambahkan 1
        $number = intval(substr($lastId, 3)) + 1;
    } else {
        $number = 1; // Jika belum ada data, mulai dari 1
    }
    
    // Format ID baru
    $newId = 'SUP' . str_pad($number, 3, '0', STR_PAD_LEFT);
    return $newId;
}
?>
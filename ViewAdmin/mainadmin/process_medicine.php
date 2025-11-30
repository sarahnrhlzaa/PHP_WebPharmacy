<?php
// ✅ AKTIFKAN OUTPUT BUFFERING DI AWAL FILE
ob_start();

// ✅ Matikan error display supaya ga ganggu JSON response
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../../Connection/connect.php';

// ✅ CEK LOGIN - WAJIB!
if (!isset($_SESSION['admin_id'])) {
    if (isset($_GET['modal'])) {
        ob_clean(); // Bersihkan buffer
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    $_SESSION['error'] = 'Unauthorized! Silakan login terlebih dahulu.';
    header('Location: login.php');
    exit;
}

// ✅ AMBIL ADMIN ID DARI SESSION
$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['username'];

$conn = getConnection();

// Tentukan action (add, update, delete)
$action = $_GET['action'] ?? 'add';

// ========== HANDLE DELETE ==========
if ($action == 'delete') {
    $medicine_id = $_GET['id'] ?? '';
    
    if (empty($medicine_id)) {
        $_SESSION['error'] = 'ID obat tidak valid!';
        header('Location: stock_medicine.php');
        exit;
    }
    
    // Get image path before delete
    $get_image = $conn->prepare("SELECT image_path FROM medicines WHERE medicine_id = ?");
    $get_image->bind_param("s", $medicine_id);
    $get_image->execute();
    $result = $get_image->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'];
        
        // Delete the medicine
        $delete_stmt = $conn->prepare("DELETE FROM medicines WHERE medicine_id = ?");
        $delete_stmt->bind_param("s", $medicine_id);
        
        if ($delete_stmt->execute()) {
            // Delete image file if exists (with error suppression)
            if (!empty($image_path)) {
                $project_root = dirname(dirname(__DIR__));
                $full_path = $project_root . '/' . $image_path;
                if (file_exists($full_path)) {
                    @unlink($full_path);
                }
            }
            $_SESSION['success'] = "Obat berhasil dihapus!";
        } else {
            $_SESSION['error'] = 'Gagal menghapus obat: ' . $delete_stmt->error;
        }
        $delete_stmt->close();
    } else {
        $_SESSION['error'] = 'Obat tidak ditemukan!';
    }
    $get_image->close();
    
    closeConnection($conn);
    header('Location: stock_medicine.php');
    exit;
}

// ========== HANDLE POST REQUEST (ADD/UPDATE) ==========
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validasi input
    $nama_obat = trim($_POST['nama_obat'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = floatval($_POST['harga'] ?? 0);
    $expired = trim($_POST['expired'] ?? '');
    $id_supplier = trim($_POST['id_supplier'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 0);
    $category = trim($_POST['category'] ?? 'medicine');
    $benefits = trim($_POST['benefits'] ?? '');
    $dosage = trim($_POST['dosage'] ?? '');
    $warnings = trim($_POST['warnings'] ?? '');
    
    // ✅ VALIDASI SUPPLIER ID EXIST
    $check_supplier = $conn->prepare("SELECT supplier_id FROM suppliers WHERE supplier_id = ?");
    $check_supplier->bind_param("s", $id_supplier);
    $check_supplier->execute();
    $supplier_result = $check_supplier->get_result();
    
    if ($supplier_result->num_rows == 0) {
        $error_msg = "Supplier ID '{$id_supplier}' tidak ditemukan di database!";
        $_SESSION['error'] = $error_msg;
        $check_supplier->close();
        closeConnection($conn);
        
        if (isset($_GET['modal'])) {
            ob_clean(); // ✅ BERSIHKAN BUFFER SEBELUM JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $error_msg]);
            exit;
        }
        
        header('Location: input_medicine.php');
        exit;
    }
    $check_supplier->close();
    
    // Cek field required
    if (empty($nama_obat) || empty($harga) || empty($expired) || empty($id_supplier) || empty($quantity)) {
        $error_msg = 'Semua field wajib diisi!';
        $_SESSION['error'] = $error_msg;
        closeConnection($conn);
        
        if (isset($_GET['modal'])) {
            ob_clean(); // ✅ BERSIHKAN BUFFER
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $error_msg]);
            exit;
        }
        
        header('Location: input_medicine.php' . ($action == 'update' ? '?id=' . ($_POST['medicine_id'] ?? '') : ''));
        exit;
    }
    
    // Handle upload gambar
    $gambar_path = '';
    $update_image = false;
    
    if (isset($_FILES['gambar_obat']) && $_FILES['gambar_obat']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        $file = $_FILES['gambar_obat'];
        $file_type = $file['type'];
        $file_size = $file['size'];
        
        // Validasi tipe file
        if (!in_array($file_type, $allowed_types)) {
            $error_msg = 'Format file tidak valid! Hanya JPG, JPEG, PNG yang diizinkan.';
            $_SESSION['error'] = $error_msg;
            closeConnection($conn);
            
            if (isset($_GET['modal'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_msg]);
                exit;
            }
            
            header('Location: input_medicine.php' . ($action == 'update' ? '?id=' . ($_POST['medicine_id'] ?? '') : ''));
            exit;
        }
        
        // Validasi ukuran file
        if ($file_size > $max_size) {
            $error_msg = 'Ukuran file terlalu besar! Maksimal 2MB.';
            $_SESSION['error'] = $error_msg;
            closeConnection($conn);
            
            if (isset($_GET['modal'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_msg]);
                exit;
            }
            
            header('Location: input_medicine.php' . ($action == 'update' ? '?id=' . ($_POST['medicine_id'] ?? '') : ''));
            exit;
        }
        
        // Generate nama file unik
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'MED_' . time() . '_' . uniqid() . '.' . $file_extension;
        
        // ✅ FIX: Gunakan __DIR__ untuk dapat path absolut dari file ini
        // File ini ada di ViewAdmin/mainadmin/, naik 2 level ke root project
        $project_root = dirname(dirname(__DIR__));
        $upload_dir = $project_root . '/uploads/medicines/';
        
        // Buat folder jika belum ada
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $upload_path = $upload_dir . $new_filename;
        $gambar_path = 'uploads/medicines/' . $new_filename; // Path untuk database (relative)
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $update_image = true;
        } else {
            // ✅ DEBUG: Log detailed error
            $error_msg = 'Gagal mengupload gambar! ';
            $error_msg .= 'Upload dir: ' . $upload_dir . ' | ';
            $error_msg .= 'Exists: ' . (file_exists($upload_dir) ? 'YES' : 'NO') . ' | ';
            $error_msg .= 'Writable: ' . (is_writable($upload_dir) ? 'YES' : 'NO');
            
            $_SESSION['error'] = $error_msg;
            closeConnection($conn);
            
            if (isset($_GET['modal'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_msg]);
                exit;
            }
            
            header('Location: input_medicine.php' . ($action == 'update' ? '?id=' . ($_POST['medicine_id'] ?? '') : ''));
            exit;
        }
    }
    
    // ========== TAMBAH OBAT BARU ==========
    if ($action == 'add') {
        // ✅ FIX: Generate medicine_id yang auto-increment
        $get_last_id = $conn->query("SELECT medicine_id FROM medicines ORDER BY medicine_id DESC LIMIT 1");
        
        if ($get_last_id && $get_last_id->num_rows > 0) {
            $last_row = $get_last_id->fetch_assoc();
            $last_id = $last_row['medicine_id'];
            
            // Extract number from MED-XXX
            $last_number = intval(substr($last_id, 4));
            $new_number = $last_number + 1;
            $medicine_id = 'MED-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);
        } else {
            // Jika belum ada data, mulai dari MED-001
            $medicine_id = 'MED-001';
        }
        
        // Check if gambar required for new entry
        if (empty($gambar_path)) {
            $error_msg = 'Gambar obat wajib diupload untuk obat baru!';
            $_SESSION['error'] = $error_msg;
            closeConnection($conn);
            
            if (isset($_GET['modal'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_msg]);
                exit;
            }
            
            header('Location: input_medicine.php');
            exit;
        }
        
        // ✅ INSERT dengan admin_id
        $stmt = $conn->prepare("
            INSERT INTO medicines 
            (medicine_id, medicine_name, description, price, expired_date, supplier_id, stock, 
             image_path, category, benefits, dosage, warnings, admin_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt === false) {
            $error_msg = 'Database error: ' . htmlspecialchars($conn->error);
            $_SESSION['error'] = $error_msg;
            closeConnection($conn);
            
            if (isset($_GET['modal'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_msg]);
                exit;
            }
            
            header('Location: input_medicine.php');
            exit;
        }
        
        $stmt->bind_param(
            "sssdssissssss", 
            $medicine_id,
            $nama_obat,
            $deskripsi,
            $harga,
            $expired,
            $id_supplier,
            $quantity,
            $gambar_path,
            $category,
            $benefits,
            $dosage,
            $warnings,
            $admin_id
        );
        
        if ($stmt->execute()) {
            $success_msg = "Obat {$nama_obat} berhasil ditambahkan dengan ID {$medicine_id}!";
            $_SESSION['success'] = $success_msg;
            $stmt->close();
            closeConnection($conn);
            
            // ✅ FIX: Return JSON for modal to handle closure
            if (isset($_GET['modal'])) {
                ob_clean(); // ✅ BERSIHKAN BUFFER
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $success_msg]);
                exit;
            }
            
            header('Location: stock_medicine.php');
            exit;
        } else {
            $error_msg = 'Gagal menambahkan obat: ' . $stmt->error;
            $_SESSION['error'] = $error_msg;
            $stmt->close();
            closeConnection($conn);
            
            if (isset($_GET['modal'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_msg]);
                exit;
            }
            
            header('Location: input_medicine.php');
            exit;
        }
    } 
    // ========== UPDATE OBAT ==========
    else if ($action == 'update') {
        $medicine_id = trim($_POST['medicine_id'] ?? '');
        
        if (empty($medicine_id)) {
            $error_msg = 'ID obat tidak valid!';
            $_SESSION['error'] = $error_msg;
            closeConnection($conn);
            
            if (isset($_GET['modal'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_msg]);
                exit;
            }
            
            header('Location: stock_medicine.php');
            exit;
        }
        
        // Get current image if no new upload
        if (!$update_image || empty($gambar_path)) {
            $current_stmt = $conn->prepare("SELECT image_path FROM medicines WHERE medicine_id = ?");
            $current_stmt->bind_param("s", $medicine_id);
            $current_stmt->execute();
            $current_result = $current_stmt->get_result();
            if ($row = $current_result->fetch_assoc()) {
                $gambar_path = $row['image_path'];
            }
            $current_stmt->close();
        } else {
            // Delete old image if new one uploaded (with error suppression)
            $old_stmt = $conn->prepare("SELECT image_path FROM medicines WHERE medicine_id = ?");
            $old_stmt->bind_param("s", $medicine_id);
            $old_stmt->execute();
            $old_result = $old_stmt->get_result();
            if ($old_row = $old_result->fetch_assoc()) {
                $old_image = $old_row['image_path'];
                if (!empty($old_image)) {
                    $project_root = dirname(dirname(__DIR__));
                    $old_full_path = $project_root . '/' . $old_image;
                    if (file_exists($old_full_path)) {
                        @unlink($old_full_path);
                    }
                }
            }
            $old_stmt->close();
        }
        
        $stmt = $conn->prepare("
            UPDATE medicines 
            SET medicine_name = ?, 
                description = ?, 
                price = ?, 
                expired_date = ?,
                supplier_id = ?, 
                stock = ?,
                image_path = ?,
                category = ?,
                benefits = ?,
                dosage = ?,
                warnings = ?
            WHERE medicine_id = ?
        ");
        
        if ($stmt === false) {
            $error_msg = 'Database error: ' . htmlspecialchars($conn->error);
            $_SESSION['error'] = $error_msg;
            closeConnection($conn);
            
            if (isset($_GET['modal'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_msg]);
                exit;
            }
            
            header('Location: input_medicine.php?id=' . $medicine_id);
            exit;
        }
        
        $stmt->bind_param(
            "ssdssissssss",
            $nama_obat, $deskripsi, $harga, $expired, $id_supplier, 
            $quantity, $gambar_path, $category, $benefits, $dosage, 
            $warnings, $medicine_id
        );
        
        if ($stmt->execute()) {
            $success_msg = "Obat {$nama_obat} berhasil diupdate!";
            $_SESSION['success'] = $success_msg;
            $stmt->close();
            closeConnection($conn);
            
            // ✅ FIX: Return JSON for modal
            if (isset($_GET['modal'])) {
                ob_clean(); // ✅ BERSIHKAN BUFFER
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $success_msg]);
                exit;
            }
            
            header('Location: stock_medicine.php');
            exit;
        } else {
            $error_msg = 'Gagal mengupdate obat: ' . $stmt->error;
            $_SESSION['error'] = $error_msg;
            $stmt->close();
            closeConnection($conn);
            
            if (isset($_GET['modal'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_msg]);
                exit;
            }
            
            header('Location: input_medicine.php?id=' . $medicine_id);
            exit;
        }
    }
    
} else {
    $_SESSION['error'] = 'Invalid request method';
    closeConnection($conn);
    header('Location: stock_medicine.php');
    exit;
}

closeConnection($conn);
?>
<?php
session_start();
require_once '../../Connection/connect.php';

// ✅ CEK LOGIN - WAJIB!
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = 'Unauthorized! Silakan login terlebih dahulu.';
    header('Location: login.php');
    exit;
}

// ✅ AMBIL ADMIN ID DARI SESSION
$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['username'];

$conn = getConnection();

// Tentukan action (add atau update)
$action = $_GET['action'] ?? 'add';

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
    
    // ✅ DEBUG - CEK SUPPLIER ID
    error_log("Supplier ID dari form: [" . $id_supplier . "]");
    
    // ✅ VALIDASI SUPPLIER ID EXIST
    $check_supplier = $conn->prepare("SELECT supplier_id FROM suppliers WHERE supplier_id = ?");
    $check_supplier->bind_param("s", $id_supplier);
    $check_supplier->execute();
    $supplier_result = $check_supplier->get_result();
    
    if ($supplier_result->num_rows == 0) {
        $_SESSION['error'] = "Supplier ID '{$id_supplier}' tidak ditemukan di database!";
        header('Location: input_medicine.php');
        exit;
    }
    $check_supplier->close();
    
    // ✅ VALIDASI ADMIN ID EXIST
    $check_admin = $conn->prepare("SELECT admin_id FROM admins WHERE admin_id = ?");
    $check_admin->bind_param("s", $admin_id);
    $check_admin->execute();
    $admin_result = $check_admin->get_result();
    
    if ($admin_result->num_rows == 0) {
        $_SESSION['error'] = "Admin ID '{$admin_id}' tidak ditemukan di database!";
        header('Location: input_medicine.php');
        exit;
    }
    $check_admin->close();
    
    // Cek field required
    if (empty($nama_obat) || empty($harga) || empty($expired) || empty($id_supplier) || empty($quantity)) {
        $_SESSION['error'] = 'Semua field wajib diisi!';
        header('Location: input_medicine.php' . ($action == 'update' ? '?id=' . intval($_POST['medicine_id'] ?? 0) : ''));
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
            $_SESSION['error'] = 'Format file tidak valid! Hanya JPG, JPEG, PNG yang diizinkan.';
            header('Location: input_medicine.php' . ($action == 'update' ? '?id=' . intval($_POST['medicine_id'] ?? 0) : ''));
            exit;
        }
        
        // Validasi ukuran file
        if ($file_size > $max_size) {
            $_SESSION['error'] = 'Ukuran file terlalu besar! Maksimal 2MB.';
            header('Location: input_medicine.php' . ($action == 'update' ? '?id=' . intval($_POST['medicine_id'] ?? 0) : ''));
            exit;
        }
        
        // Generate nama file unik
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'MED_' . time() . '_' . uniqid() . '.' . $file_extension;
        
        // Tentukan folder upload
        $upload_dir = '../../uploads/medicines/';
        
        // Buat folder jika belum ada
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $upload_path = $upload_dir . $new_filename;
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $gambar_path = 'uploads/medicines/' . $new_filename;
            $update_image = true;
        } else {
            $_SESSION['error'] = 'Gagal mengupload gambar!';
            header('Location: input_medicine.php' . ($action == 'update' ? '?id=' . intval($_POST['medicine_id'] ?? 0) : ''));
            exit;
        }
    }
    
    // ========== TAMBAH OBAT BARU ==========
    if ($action == 'add') {
        // Generate medicine_id
        $medicine_id = 'MED' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Check if ID already exists (loop until unique)
        $check_stmt = $conn->prepare("SELECT medicine_id FROM medicines WHERE medicine_id = ?");
        $check_stmt->bind_param("s", $medicine_id);
        $check_stmt->execute();
        while ($check_stmt->get_result()->num_rows > 0) {
            $medicine_id = 'MED' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $check_stmt->execute();
        }
        $check_stmt->close();
        
        // ✅ INSERT dengan admin_id (VARCHAR)
        $stmt = $conn->prepare("
            INSERT INTO medicines 
            (medicine_id, medicine_name, description, price, expired_date, supplier_id, stock, 
             image_path, category, benefits, dosage, warnings, admin_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        // ✅ CEK PREPARE STATEMENT
        if ($stmt === false) {
            $_SESSION['error'] = 'Database error: ' . htmlspecialchars($conn->error);
            header('Location: input_medicine.php');
            exit;
        }
        
        // ✅ FIX: Urutan type harus: s s s d s s i s s s s s s
        //         medicine_id, nama, desc, price, expired, supplier, stock, image, cat, ben, dos, warn, admin
        $stmt->bind_param(
            "sssdssissssss", 
            $medicine_id,    // s - medicine_id (VARCHAR)
            $nama_obat,      // s - medicine_name (VARCHAR)
            $deskripsi,      // s - description (TEXT)
            $harga,          // d - price (DECIMAL)
            $expired,        // s - expired_date (DATE)
            $id_supplier,    // s - supplier_id (VARCHAR)
            $quantity,       // i - stock (INT)
            $gambar_path,    // s - image_path (VARCHAR)
            $category,       // s - category (ENUM)
            $benefits,       // s - benefits (TEXT)
            $dosage,         // s - dosage (TEXT)
            $warnings,       // s - warnings (TEXT)
            $admin_id        // s - admin_id (VARCHAR)
        );
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Obat {$nama_obat} berhasil ditambahkan oleh {$admin_username}!";
            
            // Close modal jika dipanggil dari modal
            if (isset($_GET['modal'])) {
                echo "<script>window.parent.postMessage('closeModal', '*');</script>";
                exit;
            }
            
            header('Location: stock_medicine.php');
            exit;
        } else {
            $_SESSION['error'] = 'Gagal menambahkan obat: ' . $stmt->error;
            header('Location: input_medicine.php');
            exit;
        }
        
        $stmt->close();
    } 
    // ========== UPDATE OBAT ==========
    else if ($action == 'update') {
        $medicine_id = trim($_POST['medicine_id'] ?? '');
        
        if (empty($medicine_id)) {
            $_SESSION['error'] = 'ID obat tidak valid!';
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
            // Delete old image if new one uploaded
            $old_stmt = $conn->prepare("SELECT image_path FROM medicines WHERE medicine_id = ?");
            $old_stmt->bind_param("s", $medicine_id);
            $old_stmt->execute();
            $old_result = $old_stmt->get_result();
            if ($old_row = $old_result->fetch_assoc()) {
                $old_image = $old_row['image_path'];
                if (!empty($old_image) && file_exists("../../" . $old_image)) {
                    unlink("../../" . $old_image);
                }
            }
            $old_stmt->close();
        }
        
        // ✅ UPDATE dengan expired_date
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
        
        // ✅ CEK PREPARE STATEMENT
        if ($stmt === false) {
            $_SESSION['error'] = 'Database error: ' . htmlspecialchars($conn->error);
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
            $_SESSION['success'] = "Obat {$nama_obat} berhasil diupdate oleh {$admin_username}!";
            
            // Close modal jika dipanggil dari modal
            if (isset($_GET['modal'])) {
                echo "<script>window.parent.postMessage('closeModal', '*');</script>";
                exit;
            }
            
            header('Location: stock_medicine.php');
            exit;
        } else {
            $_SESSION['error'] = 'Gagal mengupdate obat: ' . $stmt->error;
            header('Location: input_medicine.php?id=' . $medicine_id);
            exit;
        }
        
        $stmt->close();
    }
    
} else {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: stock_medicine.php');
    exit;
}

closeConnection($conn);
?>
<?php
// 1. AKTIFKAN DEBUGGING (Supaya error kelihatan jelas jika ada)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. BUFFER OUTPUT (Agar JSON tidak tercampur text lain)
ob_start();

session_start();
require_once '../../Connection/connect.php';

$conn = getConnection();

// 3. Cek Login
if (!isset($_SESSION['admin_id'])) {
    if (isset($_GET['modal'])) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Sesi habis. Login ulang.']);
        exit;
    }
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];
$action = $_GET['action'] ?? '';

// ==========================================
// HANDLE DELETE (HAPUS)
// ==========================================
if ($action == 'delete') {
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        $_SESSION['error'] = "ID tidak valid.";
        header("Location: stock_medicine.php");
        exit;
    }

    // Ambil path gambar lama
    $q = $conn->prepare("SELECT image_path FROM medicines WHERE medicine_id = ?");
    $q->bind_param("s", $id);
    $q->execute();
    $res = $q->get_result();
    
    if ($row = $res->fetch_assoc()) {
        $img = $row['image_path'];
        
        // Hapus dari DB
        $del = $conn->prepare("DELETE FROM medicines WHERE medicine_id = ?");
        $del->bind_param("s", $id);
        
        if ($del->execute()) {
            // Hapus file fisik
            if (!empty($img)) {
                $file_path = dirname(dirname(__DIR__)) . '/' . $img;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            $_SESSION['success'] = "Data berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus: " . $conn->error;
        }
    }
    
    header("Location: stock_medicine.php");
    exit;
}

// ==========================================
// HANDLE ADD & UPDATE
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $response = ['success' => false, 'message' => 'Unknown error.'];

    try {
        // Ambil Data Form
        $nama       = $_POST['nama_obat'] ?? '';
        $deskripsi  = $_POST['deskripsi'] ?? '';
        $harga      = floatval($_POST['harga'] ?? 0);
        $stok       = intval($_POST['quantity'] ?? 0);
        $expired    = $_POST['expired'] ?? '';
        $supplier   = $_POST['id_supplier'] ?? '';
        $kategori   = $_POST['category'] ?? 'medicine'; // Default medicine
        $manfaat    = $_POST['benefits'] ?? '';
        $dosis      = $_POST['dosage'] ?? '';
        $peringatan = $_POST['warnings'] ?? '';

        // Validasi
        if (empty($nama) || $harga <= 0 || empty($supplier)) {
            throw new Exception("Nama, Harga, dan Supplier wajib diisi dengan benar.");
        }

        // Cek Supplier
        $cekSup = $conn->prepare("SELECT supplier_id FROM suppliers WHERE supplier_id = ?");
        $cekSup->bind_param("s", $supplier);
        $cekSup->execute();
        if ($cekSup->get_result()->num_rows === 0) {
            throw new Exception("Supplier ID tidak valid.");
        }

        // --- UPLOAD GAMBAR ---
        $image_path_db = '';
        if (isset($_FILES['gambar_obat']) && $_FILES['gambar_obat']['error'] === 0) {
            $file = $_FILES['gambar_obat'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed)) {
                throw new Exception("Format gambar harus JPG, PNG, atau WEBP.");
            }
            if ($file['size'] > 2 * 1024 * 1024) {
                throw new Exception("Ukuran gambar maksimal 2MB.");
            }

            $upload_dir_abs = dirname(dirname(__DIR__)) . '/uploads/medicines/';
            $upload_dir_rel = 'uploads/medicines/';

            if (!is_dir($upload_dir_abs)) mkdir($upload_dir_abs, 0777, true);

            $new_name = 'MED_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $upload_dir_abs . $new_name)) {
                $image_path_db = $upload_dir_rel . $new_name;
            } else {
                throw new Exception("Gagal upload gambar. Cek permission folder.");
            }
        }

        // --- TAMBAH DATA (ADD) ---
        if ($action == 'add') {
            if (empty($image_path_db)) throw new Exception("Gambar wajib diupload.");
            
            // 1. Tentukan Prefix berdasarkan Kategori
            $prefix = ($kategori === 'wellness') ? 'WEL-' : 'MED-';

            // 2. Cari angka tertinggi HANYA untuk prefix tersebut
            //    Kita pakai REGEXP agar ID "rusak" seperti MED--3 tidak ikut terhitung
            $sqlCheck = "SELECT MAX(CAST(SUBSTRING(medicine_id, 5) AS UNSIGNED)) as max_num 
                         FROM medicines 
                         WHERE medicine_id LIKE '$prefix%' 
                         AND medicine_id REGEXP '^{$prefix}[0-9]+$'";
            
            $qMax = $conn->query($sqlCheck);
            $rowMax = $qMax->fetch_assoc();
            
            // 3. Tambah 1 dari angka terakhir
            $max_num = $rowMax['max_num'] ?? 0; // Jika belum ada, mulai dari 0
            $next_num = $max_num + 1;
            
            // 4. Format jadi 3 digit (001, 002, dst)
            $new_id = $prefix . str_pad($next_num, 3, '0', STR_PAD_LEFT);

            // Insert
            $stmt = $conn->prepare("INSERT INTO medicines (medicine_id, medicine_name, description, price, stock, expired_date, supplier_id, category, benefits, dosage, warnings, image_path, admin_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssdissssssss", $new_id, $nama, $deskripsi, $harga, $stok, $expired, $supplier, $kategori, $manfaat, $dosis, $peringatan, $image_path_db, $admin_id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Data berhasil ditambah! ID Baru: " . $new_id;
                $_SESSION['success'] = $response['message'];
            } else {
                throw new Exception("Duplicate/DB Error: " . $stmt->error);
            }
        } 
        
        // --- UPDATE DATA (UPDATE) ---
        elseif ($action == 'update') {
            $med_id = $_POST['medicine_id'] ?? '';
            if (empty($med_id)) throw new Exception("ID Obat hilang.");

            if (!empty($image_path_db)) {
                // Hapus gambar lama
                $qOld = $conn->prepare("SELECT image_path FROM medicines WHERE medicine_id = ?");
                $qOld->bind_param("s", $med_id);
                $qOld->execute();
                $resOld = $qOld->get_result()->fetch_assoc();
                if ($resOld && !empty($resOld['image_path'])) {
                    $pathOld = dirname(dirname(__DIR__)) . '/' . $resOld['image_path'];
                    if (file_exists($pathOld)) unlink($pathOld);
                }

                $stmt = $conn->prepare("UPDATE medicines SET medicine_name=?, description=?, price=?, stock=?, expired_date=?, supplier_id=?, category=?, benefits=?, dosage=?, warnings=?, image_path=?, updated_at=NOW() WHERE medicine_id=?");
                $stmt->bind_param("ssdissssssss", $nama, $deskripsi, $harga, $stok, $expired, $supplier, $kategori, $manfaat, $dosis, $peringatan, $image_path_db, $med_id);
            } else {
                $stmt = $conn->prepare("UPDATE medicines SET medicine_name=?, description=?, price=?, stock=?, expired_date=?, supplier_id=?, category=?, benefits=?, dosage=?, warnings=?, updated_at=NOW() WHERE medicine_id=?");
                $stmt->bind_param("ssdisssssss", $nama, $deskripsi, $harga, $stok, $expired, $supplier, $kategori, $manfaat, $dosis, $peringatan, $med_id);
            }

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Data berhasil diperbarui!";
                $_SESSION['success'] = $response['message'];
            } else {
                throw new Exception("Update Error: " . $stmt->error);
            }
        }

    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
    }

    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

closeConnection($conn);
ob_end_flush();
?>

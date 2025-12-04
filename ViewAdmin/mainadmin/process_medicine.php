<?php
// 1. Matikan pesan error bawaan PHP agar tidak merusak JSON
error_reporting(0);
ini_set('display_errors', 0);

// 2. Mulai Session & Koneksi
session_start();
require_once '../../Connection/connect.php';

// 3. Set Header agar browser tahu ini respon data, bukan halaman web
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Terjadi kesalahan sistem'];

try {
    // Cek Login Admin
    if (!isset($_SESSION['admin_id'])) {
        throw new Exception('Sesi habis. Silakan login ulang.');
    }

    $conn = getConnection();
    $admin_id = $_SESSION['admin_id'];
    $action = $_GET['action'] ?? 'add';

    // ============================================
    // FITUR DELETE (HAPUS OBAT)
    // ============================================
    if ($action === 'delete') {
        $id = $_GET['id'] ?? '';
        
        // Hapus file gambar lama
        $q = $conn->query("SELECT image_path FROM medicines WHERE medicine_id = '$id'");
        if ($row = $q->fetch_assoc()) {
            $file_lama = '../../' . $row['image_path'];
            if (file_exists($file_lama)) {
                unlink($file_lama); // Hapus file fisik
            }
        }

        $conn->query("DELETE FROM medicines WHERE medicine_id = '$id'");
        
        // Redirect kembali ke halaman stock (bukan JSON)
        header("Location: stock_medicine.php");
        exit;
    }

    // ============================================
    // FITUR ADD / UPDATE (SIMPAN OBAT)
    // ============================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Ambil Data Input
        $nama_obat = trim($_POST['nama_obat'] ?? '');
        $supplier_id = $_POST['id_supplier'] ?? '';
        $harga = floatval($_POST['harga'] ?? 0);
        $stock = intval($_POST['quantity'] ?? 0);

        if (empty($nama_obat)) throw new Exception('Nama Obat wajib diisi!');
        if (empty($supplier_id)) throw new Exception('Supplier wajib dipilih!');

        // --- PROSES UPLOAD GAMBAR ---
        $gambar_path_db = ''; // Ini string path untuk disimpan di Database

        // Jika Edit, ambil path lama dulu sebagai default
        if ($action === 'update') {
            $qOld = $conn->query("SELECT image_path FROM medicines WHERE medicine_id = '{$_POST['medicine_id']}'");
            $resOld = $qOld->fetch_assoc();
            $gambar_path_db = $resOld['image_path'] ?? '';
        }

        // Cek apakah ada file baru yang diupload
        if (isset($_FILES['gambar_obat']) && $_FILES['gambar_obat']['error'] === 0) {
            
            $file_tmp = $_FILES['gambar_obat']['tmp_name'];
            $file_name = $_FILES['gambar_obat']['name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validasi Ekstensi
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                throw new Exception('Format file harus JPG, PNG, atau WEBP');
            }

            // TENTUKAN FOLDER TUJUAN (SESUAI STRUKTUR VSCODE)
            // Mundur 2 folder dari file ini (mainadmin -> ViewAdmin -> ROOT -> uploads)
            $upload_folder = '../../uploads/';

            // Generate Nama File Baru (Unik)
            $new_filename = 'MED_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $destination = $upload_folder . $new_filename;

            // PINDAHKAN FILE
            // Kita gunakan '@' untuk menekan warning jika permission error, lalu tangkap hasilnya
            if (@move_uploaded_file($file_tmp, $destination)) {
                // Sukses Upload! Path untuk database: uploads/namafile.jpg
                $gambar_path_db = 'uploads/' . $new_filename;
            } else {
                // Gagal Upload
                throw new Exception("Gagal menyimpan gambar. Pastikan folder 'uploads' ada di root project dan tidak dikunci (Read-Only).");
            }
        } 
        elseif ($action === 'add') {
            throw new Exception('Wajib upload gambar untuk obat baru!');
        }

        // --- SIMPAN KE DATABASE ---
        if ($action === 'add') {
            // Generate ID Otomatis
            $res = $conn->query("SELECT medicine_id FROM medicines ORDER BY medicine_id DESC LIMIT 1");
            $row = $res->fetch_assoc();
            $lastNum = $row ? intval(substr($row['medicine_id'], 4)) : 0;
            $newId = 'MED-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO medicines (medicine_id, medicine_name, description, price, expired_date, supplier_id, stock, image_path, category, benefits, dosage, warnings, admin_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdssissssss", $newId, $nama_obat, $_POST['deskripsi'], $harga, $_POST['expired'], $supplier_id, $stock, $gambar_path_db, $_POST['category'], $_POST['benefits'], $_POST['dosage'], $_POST['warnings'], $admin_id);

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Berhasil menambah obat!'];
            } else {
                throw new Exception("Database Error: " . $stmt->error);
            }

        } elseif ($action === 'update') {
            $stmt = $conn->prepare("UPDATE medicines SET medicine_name=?, description=?, price=?, expired_date=?, supplier_id=?, stock=?, image_path=?, category=?, benefits=?, dosage=?, warnings=? WHERE medicine_id=?");
            $stmt->bind_param("ssdssissssss", $nama_obat, $_POST['deskripsi'], $harga, $_POST['expired'], $supplier_id, $stock, $gambar_path_db, $_POST['category'], $_POST['benefits'], $_POST['dosage'], $_POST['warnings'], $_POST['medicine_id']);

            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Berhasil update obat!'];
            } else {
                throw new Exception("Database Error: " . $stmt->error);
            }
        }
    }

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

// Kirim respon ke Javascript
echo json_encode($response);
exit;
?>
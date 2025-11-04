<?php
session_start();

// ✅ WAJIB CEK LOGIN!
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ AMBIL INFO ADMIN DARI SESSION
$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['username'];

// Koneksi database
require_once '../../Connection/connect.php';
$conn = getConnection();

// Check if edit mode
$edit_mode = false;
$medicine_data = null;

if (isset($_GET['id'])) {
    $edit_mode = true;
    $medicine_id = intval($_GET['id']);
    
    // ✅ JOIN dengan admins untuk info created_by dan updated_by
    $query = "SELECT m.*, 
              ca.username as created_by_name,
              ua.username as updated_by_name
              FROM medicines m
              LEFT JOIN admins ca ON m.created_by = ca.admin_id
              LEFT JOIN admins ua ON m.updated_by = ua.admin_id
              WHERE m.medicine_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $medicine_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $medicine_data = $result->fetch_assoc();
    } else {
        $_SESSION['error'] = "Obat tidak ditemukan!";
        header("Location: stock_medicine.php");
        exit();
    }
    $stmt->close();
}

// Ambil data supplier untuk dropdown
$query_supplier = "SELECT supplier_id, company_name FROM suppliers ORDER BY company_name ASC";
$result_supplier = $conn->query($query_supplier);

closeConnection($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Input'; ?> Medicine - PharmaCare</title>
    <link rel="stylesheet" href="../cssadmin/input_medicine.css">
    <style>
        /* Style untuk info admin */
        .admin-info {
            margin-top: 15px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #92a0e0ff 0%, #cea6f6ff 100%);
            border-radius: 8px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
        }
        
        .admin-info svg {
            flex-shrink: 0;
        }
        
        .admin-info-text {
            flex: 1;
        }
        
        .admin-info strong {
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .admin-info small {
            display: block;
            margin-top: 4px;
            opacity: 0.9;
            font-size: 12px;
        }
        
        .history-info {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-left: 3px solid #667eea;
            border-radius: 4px;
            font-size: 13px;
            color: #666;
        }
        
        .history-info strong {
            color: #333;
        }
    </style>
</head>
<body>
    
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    </svg>
                    <?php echo $edit_mode ? 'Edit Medicine' : 'Input Medicine'; ?>
                </h1>
                <p><?php echo $edit_mode ? 'Update informasi obat' : 'Tambahkan obat baru ke dalam database'; ?></p>
                
                <!-- ✅ INFO ADMIN YANG SEDANG INPUT/EDIT -->
                <div class="admin-info">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <div class="admin-info-text">
                        <?php if ($edit_mode): ?>
                            <div>
                                <strong>Sedang diedit oleh:</strong> <?= htmlspecialchars($admin_username) ?> 
                                <small>(Admin ID: <?= $admin_id ?>)</small>
                            </div>
                        <?php else: ?>
                            <div>
                                <strong>Diinput oleh:</strong> <?= htmlspecialchars($admin_username) ?> 
                                <small>(Admin ID: <?= $admin_id ?>)</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- ✅ HISTORY INFO (hanya tampil di edit mode) -->
                <?php if ($edit_mode && $medicine_data): ?>
                    <div class="history-info">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                            <div>
                                <strong>📝 Dibuat oleh:</strong> 
                                <?= htmlspecialchars($medicine_data['created_by_name'] ?? 'Unknown') ?>
                                <br>
                                <small>
                                    <?= isset($medicine_data['created_at']) ? date('d M Y H:i', strtotime($medicine_data['created_at'])) : '-' ?>
                                </small>
                            </div>
                            <?php if (!empty($medicine_data['updated_by_name'])): ?>
                            <div>
                                <strong>✏️ Terakhir diupdate:</strong> 
                                <?= htmlspecialchars($medicine_data['updated_by_name']) ?>
                                <br>
                                <small>
                                    <?= isset($medicine_data['updated_at']) ? date('d M Y H:i', strtotime($medicine_data['updated_at'])) : '-' ?>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
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

            <div class="form-card">
                <form method="POST" action="process_medicine.php?action=<?php echo $edit_mode ? 'update' : 'add'; ?>" enctype="multipart/form-data" id="medicineForm">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="medicine_id" value="<?php echo $medicine_data['medicine_id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <!-- Nama Obat -->
                        <div class="form-group">
                            <label for="nama_obat">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                </svg>
                                Nama Obat <span class="required">*</span>
                            </label>
                            <input type="text" id="nama_obat" name="nama_obat" placeholder="Contoh: Paracetamol 500mg" 
                                   value="<?php echo $edit_mode ? htmlspecialchars($medicine_data['medicine_name']) : ''; ?>" required>
                        </div>

                        <!-- Harga -->
                        <div class="form-group">
                            <label for="harga">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"/>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                                Harga (Rp) <span class="required">*</span>
                            </label>
                            <input type="number" id="harga" name="harga" placeholder="Contoh: 5000" min="0" step="100" 
                                   value="<?php echo $edit_mode ? $medicine_data['price'] : ''; ?>" required>
                        </div>

                        <!-- Quantity -->
                        <div class="form-group">
                            <label for="quantity">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7"/>
                                    <rect x="14" y="3" width="7" height="7"/>
                                    <rect x="14" y="14" width="7" height="7"/>
                                    <rect x="3" y="14" width="7" height="7"/>
                                </svg>
                                Quantity (Stock) <span class="required">*</span>
                            </label>
                            <input type="number" id="quantity" name="quantity" placeholder="Contoh: 100" min="0" 
                                   value="<?php echo $edit_mode ? $medicine_data['stock'] : ''; ?>" required>
                        </div>

                        <!-- Tanggal Kadaluarsa -->
                        <div class="form-group">
                            <label for="expired">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                Tanggal Kadaluarsa <span class="required">*</span>
                            </label>
                            <input type="date" id="expired" name="expired" 
                                   value="<?php echo $edit_mode ? $medicine_data['expired_date'] : ''; ?>" required>
                        </div>

                        <!-- Supplier -->
                        <div class="form-group">
                            <label for="id_supplier">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="8.5" cy="7" r="4"/>
                                    <polyline points="17 11 19 13 23 9"/>
                                </svg>
                                Supplier <span class="required">*</span>
                            </label>
                            <select id="id_supplier" name="id_supplier" required>
                                <option value="">Pilih Supplier</option>
                                <?php 
                                if ($result_supplier && $result_supplier->num_rows > 0) {
                                    while($row = $result_supplier->fetch_assoc()) {
                                        $selected = ($edit_mode && $medicine_data['supplier_id'] == $row['supplier_id']) ? 'selected' : '';
                                        echo '<option value="' . $row['supplier_id'] . '" ' . $selected . '>' . htmlspecialchars($row['company_name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="form-group">
                            <label for="category">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7"/>
                                    <rect x="14" y="3" width="7" height="7"/>
                                    <rect x="14" y="14" width="7" height="7"/>
                                    <rect x="3" y="14" width="7" height="7"/>
                                </svg>
                                Kategori <span class="required">*</span>
                            </label>
                            <select id="category" name="category" required>
                                <option value="medicine" <?php echo ($edit_mode && $medicine_data['category'] == 'medicine') ? 'selected' : ''; ?>>Medicine</option>
                                <option value="wellness" <?php echo ($edit_mode && $medicine_data['category'] == 'wellness') ? 'selected' : ''; ?>>Wellness</option>
                            </select>
                        </div>

                        <!-- Gambar Obat -->
                        <div class="form-group full-width">
                            <label for="gambar_obat">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                Gambar Obat <?php echo $edit_mode ? '' : '<span class="required">*</span>'; ?>
                            </label>
                            <div class="upload-area" id="uploadArea">
                                <input type="file" id="gambar_obat" name="gambar_obat" accept="image/*" <?php echo $edit_mode ? '' : 'required'; ?> hidden>
                                <div class="upload-content" id="uploadContent" style="<?php echo ($edit_mode && !empty($medicine_data['image_path'])) ? 'display:none;' : ''; ?>">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                    <p class="upload-text">Klik atau drag & drop gambar</p>
                                    <span class="upload-hint">PNG, JPG, JPEG (Max. 2MB)</span>
                                </div>
                                <div class="preview-container" id="previewContainer" style="<?php echo ($edit_mode && !empty($medicine_data['image_path'])) ? '' : 'display:none;'; ?>">
                                    <img id="imagePreview" src="<?php echo $edit_mode && !empty($medicine_data['image_path']) ? '../../' . $medicine_data['image_path'] : ''; ?>" alt="Preview">
                                    <button type="button" class="remove-image" id="removeImage">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi - Full Width -->
                        <div class="form-group full-width">
                            <label for="deskripsi">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                                Deskripsi
                            </label>
                            <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Masukkan deskripsi obat (opsional)"><?php echo $edit_mode ? htmlspecialchars($medicine_data['description']) : ''; ?></textarea>
                        </div>

                        <!-- Benefits -->
                        <div class="form-group full-width">
                            <label for="benefits">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Manfaat
                            </label>
                            <textarea id="benefits" name="benefits" rows="3" placeholder="Manfaat obat (opsional)"><?php echo $edit_mode ? htmlspecialchars($medicine_data['benefits']) : ''; ?></textarea>
                        </div>

                        <!-- Dosage -->
                        <div class="form-group">
                            <label for="dosage">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                Dosis
                            </label>
                            <textarea id="dosage" name="dosage" rows="2" placeholder="Dosis obat (opsional)"><?php echo $edit_mode ? htmlspecialchars($medicine_data['dosage']) : ''; ?></textarea>
                        </div>

                        <!-- Warnings -->
                        <div class="form-group">
                            <label for="warnings">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                                Peringatan
                            </label>
                            <textarea id="warnings" name="warnings" rows="2" placeholder="Peringatan obat (opsional)"><?php echo $edit_mode ? htmlspecialchars($medicine_data['warnings']) : ''; ?></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="reset" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="1 4 1 10 7 10"/>
                                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                            </svg>
                            Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            <?php echo $edit_mode ? 'Update' : 'Simpan'; ?> Obat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="../jsadmin/input_medicine.js"></script>
</body>
</html>
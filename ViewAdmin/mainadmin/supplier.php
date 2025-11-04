<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Supplier</title>
    <link rel="stylesheet" href="../cssadmin/supplier.css">
</head>
<body>
<?php include 'navbar.php';?>
    <div class="container">
        <h1 class="page-title">Data Supplier</h1>
        
        <div class="table-controls">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Cari supplier..." onkeyup="searchSupplier()">
            </div>
            <button class="add-btn" onclick="openModal()">+ Tambah Supplier</button>
        </div>

        <div class="table-wrapper">
            <table id="supplierTable">
                <thead>
                    <tr>
                        <th>Supplier ID</th>
                        <th>Nama Perusahaan</th>
                        <th>Nomor Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="supplierTableBody">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="supplierModal" class="modal">
        <div class="modal-content">
            <h2 class="modal-header" id="modalTitle">Tambah Supplier Baru</h2>
            <form id="supplierForm" onsubmit="saveSupplier(event)">
                <input type="hidden" id="supplierId">
                <input type="hidden" id="formAction" value="add">
                
                <div class="form-group">
                    <label for="companyName">Nama Perusahaan *</label>
                    <input type="text" id="companyName" required>
                </div>

                <div class="form-group">
                    <label for="phoneNumber">Nomor Telepon *</label>
                    <input type="tel" id="phoneNumber" required>
                </div>

                <div class="form-group">
                    <label for="address">Alamat *</label>
                    <textarea id="address" required></textarea>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="modal-btn btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="modal-btn btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script src="../jsadmin/supplier.js"></script>
</body>
</html>
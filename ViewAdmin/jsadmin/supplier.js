// Load suppliers saat halaman dibuka
document.addEventListener('DOMContentLoaded', function() {
    loadSuppliers();
});

// Load suppliers dari database
function loadSuppliers() {
    const formData = new FormData();
    formData.append('action', 'list');
    
    fetch('process_supplier.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('supplierTableBody').innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Gagal memuat data supplier', 'error');
    });
}

// Buka modal
function openModal(id = null) {
    const modal = document.getElementById('supplierModal');
    const modalTitle = document.getElementById('modalTitle');
    
    if (id) {
        // Edit mode
        modalTitle.textContent = 'Edit Supplier';
        document.getElementById('formAction').value = 'update';
        
        // Load data supplier
        const formData = new FormData();
        formData.append('action', 'get');
        formData.append('supplier_id', id);
        
        fetch('process_supplier.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            const parts = data.split('|');
            if (parts[0] === 'success') {
                document.getElementById('supplierId').value = parts[1];
                document.getElementById('companyName').value = parts[2];
                document.getElementById('phoneNumber').value = parts[3];
                document.getElementById('address').value = parts[4];
            }
        });
    } else {
        // Add mode
        modalTitle.textContent = 'Tambah Supplier Baru';
        document.getElementById('supplierForm').reset();
        document.getElementById('formAction').value = 'add';
        
        // Generate ID otomatis
        const formData = new FormData();
        formData.append('action', 'generate_id');
        
        fetch('process_supplier.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            const parts = data.split('|');
            if (parts[0] === 'success') {
                document.getElementById('supplierId').value = parts[1];
            }
        });
    }
    
    modal.classList.add('show');
}

// Tutup modal
function closeModal() {
    const modal = document.getElementById('supplierModal');
    modal.classList.remove('show');
    document.getElementById('supplierForm').reset();
}

// Simpan supplier
function saveSupplier(event) {
    event.preventDefault();
    
    const formData = new FormData();
    const action = document.getElementById('formAction').value;
    
    formData.append('action', action);
    formData.append('supplier_id', document.getElementById('supplierId').value);
    formData.append('company_name', document.getElementById('companyName').value);
    formData.append('phone_number', document.getElementById('phoneNumber').value);
    formData.append('address', document.getElementById('address').value);
    
    fetch('process_supplier.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        const parts = data.split('|');
        if (parts[0] === 'success') {
            showToast(parts[1], 'success');
            closeModal();
            loadSuppliers();
        } else {
            showToast(parts[1], 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan', 'error');
    });
}

// Edit supplier
function editSupplier(id) {
    openModal(id);
}

// Hapus supplier
function deleteSupplier(id) {
    if (confirm('Apakah Anda yakin ingin menghapus supplier ini?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('supplier_id', id);
        
        fetch('process_supplier.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            const parts = data.split('|');
            if (parts[0] === 'success') {
                showToast(parts[1], 'success');
                loadSuppliers();
            } else {
                showToast(parts[1], 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Gagal menghapus supplier', 'error');
        });
    }
}

// Search supplier
function searchSupplier() {
    const keyword = document.getElementById('searchInput').value;
    const formData = new FormData();
    formData.append('action', 'list');
    formData.append('search', keyword);
    
    fetch('process_supplier.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('supplierTableBody').innerHTML = html;
    });
}

// Show toast notification
function showToast(message, type) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    
    setTimeout(() => {
        toast.className = 'toast';
    }, 3000);
}

// Tutup modal jika klik di luar
window.onclick = function(event) {
    const modal = document.getElementById('supplierModal');
    if (event.target === modal) {
        closeModal();
    }
}
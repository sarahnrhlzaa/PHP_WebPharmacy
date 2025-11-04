// ============================================
// INPUT MEDICINE JAVASCRIPT
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // IMAGE UPLOAD HANDLING
    // ============================================
    
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('gambar_obat');
    const uploadContent = document.getElementById('uploadContent');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const removeImageBtn = document.getElementById('removeImage');
    
    // Click to upload
    if (uploadArea && fileInput) {
        uploadContent.addEventListener('click', function() {
            fileInput.click();
        });
        
        // File input change
        fileInput.addEventListener('change', function(e) {
            handleFileSelect(e.target.files[0]);
        });
        
        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });
        
        // Remove image
        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                removeImage();
            });
        }
    }
    
    // ============================================
    // HANDLE FILE SELECT
    // ============================================
    
    function handleFileSelect(file) {
        if (!file) return;
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            showAlert('Format file tidak valid! Hanya JPG, JPEG, dan PNG yang diperbolehkan.', 'error');
            return;
        }
        
        // Validate file size (2MB)
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
            showAlert('Ukuran file terlalu besar! Maksimal 2MB.', 'error');
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            uploadContent.style.display = 'none';
            previewContainer.style.display = 'flex';
        };
        reader.readAsDataURL(file);
        
        // Update file input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;
    }
    
    // ============================================
    // REMOVE IMAGE
    // ============================================
    
    function removeImage() {
        imagePreview.src = '';
        fileInput.value = '';
        uploadContent.style.display = 'flex';
        previewContainer.style.display = 'none';
    }
    
    // ============================================
    // FORM VALIDATION
    // ============================================
    
    const medicineForm = document.getElementById('medicineForm');
    
    if (medicineForm) {
        medicineForm.addEventListener('submit', function(e) {
            // Get all required fields
            const namaObat = document.getElementById('nama_obat').value.trim();
            const harga = document.getElementById('harga').value;
            const quantity = document.getElementById('quantity').value;
            const expired = document.getElementById('expired').value;
            const supplier = document.getElementById('id_supplier').value;
            
            // Validate empty fields
            if (!namaObat || !harga || !quantity || !expired || !supplier) {
                e.preventDefault();
                showAlert('Mohon lengkapi semua field yang wajib diisi!', 'error');
                return false;
            }
            
            // Validate price
            if (parseFloat(harga) <= 0) {
                e.preventDefault();
                showAlert('Harga harus lebih dari 0!', 'error');
                return false;
            }
            
            // Validate quantity
            if (parseInt(quantity) < 0) {
                e.preventDefault();
                showAlert('Quantity tidak boleh negatif!', 'error');
                return false;
            }
            
            // Validate expired date
            const expiredDate = new Date(expired);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (expiredDate <= today) {
                const confirm = window.confirm('Tanggal kadaluarsa sudah lewat atau hari ini. Lanjutkan?');
                if (!confirm) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Show loading state
            const submitBtn = medicineForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            }
            
            return true;
        });
    }
    
    // ============================================
    // SHOW ALERT
    // ============================================
    
    function showAlert(message, type = 'error') {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        
        const icon = type === 'success' 
            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
        
        alert.innerHTML = `${icon}<span>${message}</span>`;
        
        // Insert after page header
        const pageHeader = document.querySelector('.page-header');
        if (pageHeader) {
            pageHeader.after(alert);
        } else {
            document.body.insertBefore(alert, document.body.firstChild);
        }
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    }
    
    // ============================================
    // FORMAT CURRENCY INPUT
    // ============================================
    
    const hargaInput = document.getElementById('harga');
    if (hargaInput) {
        hargaInput.addEventListener('input', function(e) {
            // Remove non-numeric characters except dots
            let value = e.target.value.replace(/[^\d]/g, '');
            e.target.value = value;
        });
        
        // Format on blur
        hargaInput.addEventListener('blur', function(e) {
            if (e.target.value) {
                e.target.value = parseInt(e.target.value) || 0;
            }
        });
    }
    
    // ============================================
    // PREVENT NEGATIVE QUANTITY
    // ============================================
    
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('input', function(e) {
            if (parseInt(e.target.value) < 0) {
                e.target.value = 0;
            }
        });
    }
    
    // ============================================
    // AUTO-RESIZE TEXTAREA
    // ============================================
    
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
    
    // ============================================
    // FORM RESET HANDLING
    // ============================================
    
    const resetBtn = medicineForm?.querySelector('button[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Apakah Anda yakin ingin mereset form?')) {
                medicineForm.reset();
                removeImage();
                
                // Reset textareas height
                textareas.forEach(textarea => {
                    textarea.style.height = 'auto';
                });
                
                showAlert('Form berhasil direset!', 'success');
            }
        });
    }
    
    // ============================================
    // PREVENT FORM RESUBMISSION
    // ============================================
    
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    
    // ============================================
    // CLOSE MODAL ON SUCCESS (for iframe usage)
    // ============================================
    
    // Check if page is in iframe and has success message
    if (window.self !== window.top) {
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                window.parent.postMessage('closeModal', '*');
            }, 1500);
        }
    }
    
    // ============================================
    // SMOOTH SCROLL TO ERROR
    // ============================================
    
    const errorAlert = document.querySelector('.alert-error');
    if (errorAlert) {
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // ============================================
    // KEYBOARD SHORTCUTS
    // ============================================
    
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S to submit form
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (medicineForm) {
                medicineForm.requestSubmit();
            }
        }
        
        // Escape to reset form
        if (e.key === 'Escape') {
            if (medicineForm && confirm('Reset form?')) {
                medicineForm.reset();
                removeImage();
            }
        }
    });
    
    console.log('✅ Input Medicine JS Loaded');
});
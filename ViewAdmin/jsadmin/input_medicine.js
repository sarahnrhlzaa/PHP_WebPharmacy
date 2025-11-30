// ========== IMAGE UPLOAD PREVIEW ==========
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('gambar_obat');
const uploadContent = document.getElementById('uploadContent');
const previewContainer = document.getElementById('previewContainer');
const imagePreview = document.getElementById('imagePreview');
const removeImageBtn = document.getElementById('removeImage');

// Click to upload
uploadArea.addEventListener('click', () => {
    fileInput.click();
});

// Drag & Drop
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = '#4F46E5';
    uploadArea.style.backgroundColor = '#F3F4F6';
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.style.borderColor = '#D1D5DB';
    uploadArea.style.backgroundColor = 'white';
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = '#D1D5DB';
    uploadArea.style.backgroundColor = 'white';
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        previewImage(files[0]);
    }
});

// File input change
fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        previewImage(e.target.files[0]);
    }
});

// Preview image function
function previewImage(file) {
    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!validTypes.includes(file.type)) {
        alert('Format file tidak valid! Hanya JPG, JPEG, PNG yang diizinkan.');
        return;
    }
    
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file terlalu besar! Maksimal 2MB.');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.src = e.target.result;
        uploadContent.style.display = 'none';
        previewContainer.style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

// Remove image
removeImageBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    fileInput.value = '';
    imagePreview.src = '';
    uploadContent.style.display = 'flex';
    previewContainer.style.display = 'none';
});

// ========== FORM SUBMISSION WITH AJAX ==========
document.getElementById('medicineForm').addEventListener('submit', function(e) {
    // Check if this is in modal mode
    const urlParams = new URLSearchParams(window.location.search);
    const isModal = urlParams.has('modal');
    
    if (!isModal) {
        return; // Let normal form submission happen
    }
    
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="spin">
            <circle cx="12" cy="12" r="10"/>
        </svg>
        Memproses...
    `;
    
    // Get action from form action attribute
    const formAction = this.getAttribute('action');
    
    fetch(formAction, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // ✅ DEBUG: Log response status
        console.log('Response status:', response.status);
        
        // Try to get response as text first
        return response.text().then(text => {
            console.log('Response text:', text);
            
            // Try to parse as JSON
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON Parse Error:', e);
                console.error('Raw response:', text);
                
                // Show first 500 chars of response in alert
                const preview = text.substring(0, 500);
                throw new Error('Server tidak mengembalikan JSON valid.\n\nResponse:\n' + preview);
            }
        });
    })
    .then(data => {
        console.log('Parsed data:', data);
        
        if (data.success) {
            // Show success message
            alert(data.message);
            
            // Send message to parent to close modal
            window.parent.postMessage('closeModal', '*');
        } else {
            // Show error message
            alert(data.message || 'Terjadi kesalahan!');
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Show detailed error in alert
        const errorDetails = `
Terjadi kesalahan saat memproses data!

Error: ${error.message}

Cek Console (F12) untuk detail lengkap.
        `;
        
        alert(errorDetails);
        
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});

// Add spinning animation for loading state
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
// Load users on page load
window.onload = function() {
    loadUsers();
};

// Load all users
function loadUsers() {
    const formData = new FormData();
    formData.append('action', 'list');
    
    fetch('process_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('userTableBody').innerHTML = data;
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Gagal memuat data user');
    });
}

// Search users
function searchUsers() {
    const keyword = document.getElementById('searchInput').value;
    const formData = new FormData();
    formData.append('action', 'list');
    formData.append('search', keyword);
    
    fetch('process_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('userTableBody').innerHTML = data;
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Refresh data
function refreshData() {
    document.getElementById('searchInput').value = '';
    loadUsers();
    showNotification('success', 'Data berhasil direfresh!');
}

// Confirm delete with custom popup
function confirmDelete(userId, username) {
    showConfirmDialog(
        'Konfirmasi Hapus',
        `Apakah Anda yakin ingin menghapus user "${username}"?\n\nTindakan ini tidak dapat dibatalkan!`,
        function() {
            deleteUser(userId);
        }
    );
}

// Delete user
function deleteUser(userId) {
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('user_id', userId);
    
    fetch('process_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        const [status, message] = data.split('|');
        
        if (status === 'success') {
            showNotification('success', message);
            loadUsers();
        } else {
            showNotification('error', message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat menghapus user');
    });
}

// Show custom notification popup
function showNotification(type, message) {
    // Remove existing notifications
    const existing = document.querySelector('.custom-notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = `custom-notification notification-${type}`;
    
    const icon = type === 'success' ? '✓' : '✗';
    const iconClass = type === 'success' ? 'icon-success' : 'icon-error';
    
    notification.innerHTML = `
        <div class="notification-icon ${iconClass}">${icon}</div>
        <div class="notification-content">
            <div class="notification-title">${type === 'success' ? 'Berhasil!' : 'Error!'}</div>
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    document.body.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Show custom confirm dialog
function showConfirmDialog(title, message, onConfirm) {
    // Remove existing dialog
    const existing = document.querySelector('.confirm-dialog-overlay');
    if (existing) existing.remove();
    
    const overlay = document.createElement('div');
    overlay.className = 'confirm-dialog-overlay';
    
    overlay.innerHTML = `
        <div class="confirm-dialog">
            <div class="confirm-dialog-header">
                <h3>${title}</h3>
            </div>
            <div class="confirm-dialog-body">
                <div class="confirm-icon">⚠️</div>
                <p>${message.replace(/\n/g, '<br>')}</p>
            </div>
            <div class="confirm-dialog-footer">
                <button class="confirm-btn btn-cancel" onclick="closeConfirmDialog()">Batal</button>
                <button class="confirm-btn btn-confirm" onclick="handleConfirm()">Ya, Hapus</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    // Store callback
    window.confirmCallback = onConfirm;
    
    // Trigger animation
    setTimeout(() => overlay.classList.add('show'), 10);
}

// Handle confirm button click
function handleConfirm() {
    if (window.confirmCallback) {
        window.confirmCallback();
        window.confirmCallback = null;
    }
    closeConfirmDialog();
}

// Close confirm dialog
function closeConfirmDialog() {
    const overlay = document.querySelector('.confirm-dialog-overlay');
    if (overlay) {
        overlay.classList.remove('show');
        setTimeout(() => overlay.remove(), 300);
    }
}

// Close dialog when clicking outside
window.onclick = function(event) {
    const confirmOverlay = document.querySelector('.confirm-dialog-overlay');
    if (event.target == confirmOverlay) {
        closeConfirmDialog();
    }
}
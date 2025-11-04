// Update quantity
function updateQuantity(itemId, change) {
    const cartItem = document.querySelector(`.cart-item[data-id="${itemId}"]`);
    const qtyInput = cartItem.querySelector('.qty-input');
    let currentQty = parseInt(qtyInput.value);
    let newQty = currentQty + change;
    
    if (newQty < 1) {
        showConfirmPopup(
            'Remove Item',
            'Remove this item from cart?',
            () => removeItem(itemId)
        );
        return;
    }
    
    // Update display
    qtyInput.value = newQty;
    
    // Update server
    const formData = new FormData();
    formData.append('action', 'update_qty');
    formData.append('id', itemId);
    formData.append('qty', newQty);
    
    fetch('cart.php', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // Reload to update prices
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update quantity', 'error');
    });
}

// Remove item
function removeItem(itemId) {
    showConfirmPopup(
        'Remove Item',
        'Are you sure you want to remove this item from cart?',
        () => {
            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('id', itemId);
            
            fetch('cart.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to remove item', 'error');
            });
        }
    );
}

// Clear cart
function clearCart() {
    showConfirmPopup(
        'Clear Cart',
        'Clear all items from cart?',
        () => {
            const formData = new FormData();
            formData.append('action', 'clear');
            
            fetch('cart.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to clear cart', 'error');
            });
        }
    );
}

// Toggle select all
function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSummary();
}

// Update summary (for selected items calculation)
function updateSummary() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    let count = 0;
    let subtotal = 0;
    
    checkboxes.forEach(cb => {
        const cartItem = cb.closest('.cart-item');
        const subtotalDiv = cartItem.querySelector('.item-subtotal');
        const price = parseFloat(subtotalDiv.dataset.price);
        const qty = parseInt(subtotalDiv.dataset.qty);
        
        count += qty;
        subtotal += price * qty;
    });
    
    const shipping = count > 0 ? 10000 : 0;
    const total = subtotal + shipping;
    
    document.getElementById('selected-count').textContent = count;
    document.getElementById('subtotal-price').textContent = 'Rp ' + formatPrice(subtotal);
    document.getElementById('shipping-cost').textContent = 'Rp ' + formatPrice(shipping);
    document.getElementById('total-price').textContent = 'Rp ' + formatPrice(total);
    
    // Update checkout button state
    const checkoutBtn = document.getElementById('checkout-btn');
    const checkoutWarning = document.getElementById('checkout-warning');
    
    if (count === 0) {
        checkoutBtn.style.opacity = '0.5';
        checkoutBtn.style.cursor = 'not-allowed';
        checkoutWarning.style.display = 'block';
    } else {
        checkoutBtn.style.opacity = '1';
        checkoutBtn.style.cursor = 'pointer';
        checkoutWarning.style.display = 'none';
    }
}

// Format price
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Proceed to checkout
function proceedToCheckout() {
    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    
    if (checkedItems.length === 0) {
        showToast('Please select at least one item to checkout', 'error');
        return;
    }
    
    // Collect selected item IDs
    const selectedIds = [];
    checkedItems.forEach(cb => {
        const cartItem = cb.closest('.cart-item');
        selectedIds.push(cartItem.dataset.id);
    });
    
    // Send to checkout with selected items
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'checkout.php';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'selected_items';
    input.value = JSON.stringify(selectedIds);
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

// Show toast
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast-notification');
    const toastMessage = document.getElementById('toast-message');
    
    if (toast && toastMessage) {
        const icon = toast.querySelector('i');
        if (type === 'success') {
            icon.className = 'fa fa-check-circle';
        } else if (type === 'error') {
            icon.className = 'fa fa-exclamation-circle';
        }
        
        toastMessage.textContent = message;
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
}

// Show custom confirm popup
function showConfirmPopup(title, message, onConfirm) {
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'confirm-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.2s ease;
    `;
    
    // Create popup
    const popup = document.createElement('div');
    popup.className = 'confirm-popup';
    popup.style.cssText = `
        background: white;
        border-radius: 10px;
        padding: 25px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        animation: slideIn 0.3s ease;
    `;
    
    popup.innerHTML = `
        <div style="margin-bottom: 20px;">
            <h3 style="margin: 0 0 10px 0; color: #333; font-size: 20px;">${title}</h3>
            <p style="margin: 0; color: #666; font-size: 14px;">${message}</p>
        </div>
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button class="btn-cancel" style="
                padding: 10px 20px;
                border: 1px solid #ddd;
                background: white;
                color: #666;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                transition: all 0.3s ease;
            ">Cancel</button>
            <button class="btn-confirm" style="
                padding: 10px 20px;
                border: none;
                background: #e74c3c;
                color: white;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                transition: all 0.3s ease;
            ">Confirm</button>
        </div>
    `;
    
    // Add animations (only once)
    if (!document.getElementById('confirm-popup-styles')) {
        const style = document.createElement('style');
        style.id = 'confirm-popup-styles';
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes slideIn {
                from { transform: translateY(-20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            .btn-cancel:hover {
                background: #f5f5f5 !important;
            }
            .btn-confirm:hover {
                background: #c0392b !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    overlay.appendChild(popup);
    document.body.appendChild(overlay);
    
    // Handle buttons
    const btnCancel = popup.querySelector('.btn-cancel');
    const btnConfirm = popup.querySelector('.btn-confirm');
    
    const closePopup = () => {
        overlay.style.animation = 'fadeIn 0.2s ease reverse';
        setTimeout(() => overlay.remove(), 200);
    };
    
    btnCancel.onclick = closePopup;
    overlay.onclick = (e) => {
        if (e.target === overlay) closePopup();
    };
    
    btnConfirm.onclick = () => {
        closePopup();
        if (onConfirm) onConfirm();
    };
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', () => {
    updateSummary();
});
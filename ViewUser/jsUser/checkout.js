// // Toggle address input visibility
// function toggleAddressInput() {
//     const useSaved = document.getElementById('use-saved');
//     const addressSection = document.getElementById('address-input-section');
//     const addressField = document.getElementById('address');
//     const cityField = document.getElementById('city');
//     const postalCodeField = document.getElementById('postal_code');
    
//     if (useSaved && useSaved.checked) {
//         // Hide new address form
//         addressSection.style.display = 'none';
//         // Disable validation untuk field yang hidden
//         if (addressField) addressField.removeAttribute('required');
//         if (cityField) cityField.removeAttribute('required');
//         if (postalCodeField) postalCodeField.removeAttribute('required');
//     } else {
//         // Show new address form
//         addressSection.style.display = 'block';
//         // Enable validation
//         if (addressField) addressField.setAttribute('required', 'required');
//         if (cityField) cityField.setAttribute('required', 'required');
//         if (postalCodeField) postalCodeField.setAttribute('required', 'required');
//     }
// }

// // Main initialization
// document.addEventListener('DOMContentLoaded', function() {
    
//     // === FORM VALIDATION ===
//     const checkoutForm = document.getElementById('checkout-form');
    
//     if (checkoutForm) {
//         checkoutForm.addEventListener('submit', function(e) {
//             console.log('🚀 Form submitted!');
            
//             const addressOption = document.querySelector('input[name="address-option"]:checked');
//             console.log('📍 Address option:', addressOption ? addressOption.value : 'none');
            
//             // Jika pakai alamat baru, validasi input
//             if (!addressOption || addressOption.value === 'new') {
//                 const address = document.getElementById('address');
//                 const city = document.getElementById('city');
//                 const postalCode = document.getElementById('postal_code');
                
//                 // Cek apakah field ada
//                 if (!address || !city || !postalCode) {
//                     console.error('❌ Address fields not found!');
//                     e.preventDefault();
//                     showNotification('Address form fields not found. Please refresh the page.', 'error');
//                     return false;
//                 }
                
//                 const addressVal = address.value.trim();
//                 const cityVal = city.value.trim();
//                 const postalVal = postalCode.value.trim();
                
//                 console.log('📝 Address:', addressVal);
//                 console.log('🏙️ City:', cityVal);
//                 console.log('📮 Postal:', postalVal);
                
//                 // Validasi alamat tidak kosong
//                 if (!addressVal || addressVal.length < 10) {
//                     e.preventDefault();
//                     showNotification('Please enter a complete address (minimum 10 characters)', 'error');
//                     return false;
//                 }
                
//                 // Validasi kota
//                 if (!cityVal || cityVal.length < 3) {
//                     e.preventDefault();
//                     showNotification('Please enter a valid city name', 'error');
//                     return false;
//                 }
                
//                 // Validasi postal code (5 digit)
//                 if (!/^\d{5}$/.test(postalVal)) {
//                     e.preventDefault();
//                     showNotification('Please enter a valid 5-digit postal code', 'error');
//                     return false;
//                 }
//             }
            
//             // Validasi payment method dipilih
//             const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
//             console.log('💳 Payment method:', paymentMethod ? paymentMethod.value : 'none');
            
//             if (!paymentMethod) {
//                 e.preventDefault();
//                 showNotification('Please select a payment method', 'error');
//                 return false;
//             }
            
//             // Show loading state
//             const submitBtn = checkoutForm.querySelector('button[type="submit"]');
//             if (submitBtn) {
//                 submitBtn.disabled = true;
//                 submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
//             }
            
//             console.log('✅ Form validation passed! Submitting...');
//             return true;
//         });
//     } else {
//         console.error('❌ Checkout form not found! Make sure form has id="checkout-form"');
//     }
    
//     // === FORMAT POSTAL CODE INPUT ===
//     const postalCodeInput = document.getElementById('postal_code');
//     if (postalCodeInput) {
//         postalCodeInput.addEventListener('input', function(e) {
//             // Hanya izinkan angka
//             this.value = this.value.replace(/[^0-9]/g, '');
//             // Maximum 5 digit
//             if (this.value.length > 5) {
//                 this.value = this.value.slice(0, 5);
//             }
//         });
//     }
    
//     // === AUTO-RESIZE TEXTAREA ===
//     const textareas = document.querySelectorAll('textarea');
//     textareas.forEach(textarea => {
//         textarea.addEventListener('input', function() {
//             this.style.height = 'auto';
//             this.style.height = (this.scrollHeight) + 'px';
//         });
//     });
    
//     // === FIX IMAGE ERROR HANDLING ===
//     const images = document.querySelectorAll('img');
//     images.forEach(img => {
//         img.addEventListener('error', function() {
//             // Cek apakah sudah pernah diganti (prevent infinite loop)
//             if (!this.dataset.errorHandled) {
//                 this.dataset.errorHandled = 'true';
//                 this.src = '../assets/default.jpg';
//             }
//         });
//     });
    
//     console.log('✅ Checkout page initialized successfully');
// });

// // === SHOW NOTIFICATION FUNCTION ===
// function showNotification(message, type = 'info') {
//     // Create notification element if doesn't exist
//     let notification = document.getElementById('notification');
    
//     if (!notification) {
//         notification = document.createElement('div');
//         notification.id = 'notification';
//         notification.className = 'notification';
        
//         // Add styles
//         notification.style.cssText = `
//             position: fixed;
//             top: 20px;
//             right: 20px;
//             padding: 15px 20px;
//             border-radius: 5px;
//             color: white;
//             font-size: 14px;
//             z-index: 9999;
//             opacity: 0;
//             transform: translateX(100%);
//             transition: all 0.3s ease;
//             max-width: 300px;
//             box-shadow: 0 2px 10px rgba(0,0,0,0.2);
//         `;
        
//         document.body.appendChild(notification);
//     }
    
//     // Set notification content and type
//     notification.textContent = message;
    
//     // Set color based on type
//     if (type === 'error') {
//         notification.style.backgroundColor = '#f44336';
//     } else if (type === 'success') {
//         notification.style.backgroundColor = '#4caf50';
//     } else if (type === 'warning') {
//         notification.style.backgroundColor = '#ff9800';
//     } else {
//         notification.style.backgroundColor = '#2196f3';
//     }
    
//     // Show notification
//     setTimeout(() => {
//         notification.style.opacity = '1';
//         notification.style.transform = 'translateX(0)';
//     }, 10);
    
//     // Auto hide after 3 seconds
//     setTimeout(() => {
//         notification.style.opacity = '0';
//         notification.style.transform = 'translateX(100%)';
//     }, 3000);
// }


// // // Toggle address input visibility
// // function toggleAddressInput() {
// //     const useSaved = document.getElementById('use-saved');
// //     const addressSection = document.getElementById('address-input-section');
// //     const addressField = document.getElementById('address');
// //     const cityField = document.getElementById('city');
// //     const postalCodeField = document.getElementById('postal_code');
    
// //     if (useSaved && useSaved.checked) {
// //         // Hide new address form
// //         addressSection.style.display = 'none';
// //         // Disable validation untuk field yang hidden
// //         addressField.removeAttribute('required');
// //         cityField.removeAttribute('required');
// //         postalCodeField.removeAttribute('required');
// //     } else {
// //         // Show new address form
// //         addressSection.style.display = 'block';
// //         // Enable validation
// //         addressField.setAttribute('required', 'required');
// //         cityField.setAttribute('required', 'required');
// //         postalCodeField.setAttribute('required', 'required');
// //     }
// // }

// // // Form validation
// // document.addEventListener('DOMContentLoaded', function() {
// //     const checkoutForm = document.getElementById('checkout-form');
    
// //     if (checkoutForm) {
// //         checkoutForm.addEventListener('submit', function(e) {
// //             const useNew = document.getElementById('use-new');
            
// //             // Jika pakai alamat baru, validasi input
// //             if (useNew && useNew.checked) {
// //                 const address = document.getElementById('address').value.trim();
// //                 const city = document.getElementById('city').value.trim();
// //                 const postalCode = document.getElementById('postal_code').value.trim();
                
// //                 // Validasi alamat tidak kosong
// //                 if (!address || address.length < 10) {
// //                     e.preventDefault();
// //                     showNotification('Please enter a complete address (minimum 10 characters)', 'error');
// //                     return false;
// //                 }
                
// //                 // Validasi kota
// //                 if (!city || city.length < 3) {
// //                     e.preventDefault();
// //                     showNotification('Please enter a valid city name', 'error');
// //                     return false;
// //                 }
                
// //                 // Validasi postal code (5 digit)
// //                 if (!/^\d{5}$/.test(postalCode)) {
// //                     e.preventDefault();
// //                     showNotification('Please enter a valid 5-digit postal code', 'error');
// //                     return false;
// //                 }
// //             }
            
// //             // Validasi payment method dipilih
// //             const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
// //             if (!paymentMethod) {
// //                 e.preventDefault();
// //                 showNotification('Please select a payment method', 'error');
// //                 return false;
// //             }
            
// //             // Show loading state
// //             const submitBtn = checkoutForm.querySelector('button[type="submit"]');
// //             if (submitBtn) {
// //                 submitBtn.disabled = true;
// //                 submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
// //             }
            
// //             return true;
// //         });
// //     }
    
// //     // Format postal code input - hanya angka
// //     const postalCodeInput = document.getElementById('postal_code');
// //     if (postalCodeInput) {
// //         postalCodeInput.addEventListener('input', function(e) {
// //             // Hanya izinkan angka
// //             this.value = this.value.replace(/[^0-9]/g, '');
// //             // Maximum 5 digit
// //             if (this.value.length > 5) {
// //                 this.value = this.value.slice(0, 5);
// //             }
// //         });
// //     }
    
// //     // Auto-resize textarea
// //     const textareas = document.querySelectorAll('textarea');
// //     textareas.forEach(textarea => {
// //         textarea.addEventListener('input', function() {
// //             this.style.height = 'auto';
// //             this.style.height = (this.scrollHeight) + 'px';
// //         });
// //     });
    
// //     // Fix image error handling - prevent infinite reload
// //     const images = document.querySelectorAll('img');
// //     images.forEach(img => {
// //         img.addEventListener('error', function() {
// //             // Cek apakah sudah pernah diganti (prevent infinite loop)
// //             if (!this.dataset.errorHandled) {
// //                 this.dataset.errorHandled = 'true';
// //                 this.src = '../assets/default.jpg';
// //             }
// //         });
// //     });
// // });

// // // Show notification
// // function showNotification(message, type = 'info') {
// //     // Create notification element if doesn't exist
// //     let notification = document.getElementById('notification');
    
// //     if (!notification) {
// //         notification = document.createElement('div');
// //         notification.id = 'notification';
// //         notification.className = 'notification';
        
// //         // Add styles
// //         notification.style.cssText = `
// //             position: fixed;
// //             top: 20px;
// //             right: 20px;
// //             padding: 15px 20px;
// //             border-radius: 5px;
// //             color: white;
// //             font-size: 14px;
// //             z-index: 9999;
// //             opacity: 0;
// //             transform: translateX(100%);
// //             transition: all 0.3s ease;
// //             max-width: 300px;
// //             box-shadow: 0 2px 10px rgba(0,0,0,0.2);
// //         `;
        
// //         document.body.appendChild(notification);
// //     }
    
// //     // Set notification content and type
// //     notification.textContent = message;
    
// //     // Set color based on type
// //     if (type === 'error') {
// //         notification.style.backgroundColor = '#f44336';
// //     } else if (type === 'success') {
// //         notification.style.backgroundColor = '#4caf50';
// //     } else if (type === 'warning') {
// //         notification.style.backgroundColor = '#ff9800';
// //     } else {
// //         notification.style.backgroundColor = '#2196f3';
// //     }
    
// //     // Show notification
// //     setTimeout(() => {
// //         notification.style.opacity = '1';
// //         notification.style.transform = 'translateX(0)';
// //     }, 10);
    
// //     // Auto hide after 3 seconds
// //     setTimeout(() => {
// //         notification.style.opacity = '0';
// //         notification.style.transform = 'translateX(100%)';
// //     }, 3000);
// // }

// // // // Format input postal code (hanya angka)
// // // document.addEventListener('DOMContentLoaded', function() {
// // //     const postalCodeInput = document.getElementById('postal_code');
    
// // //     if (postalCodeInput) {
// // //         postalCodeInput.addEventListener('input', function(e) {
// // //             // Hanya izinkan angka
// // //             this.value = this.value.replace(/[^0-9]/g, '');
// // //             // Maximum 5 digit
// // //             if (this.value.length > 5) {
// // //                 this.value = this.value.slice(0, 5);
// // //             }
// // //         });
// // //     }
// // // });

// // // // Auto-resize textarea
// // // document.addEventListener('DOMContentLoaded', function() {
// // //     const textareas = document.querySelectorAll('textarea');
    
// // //     textareas.forEach(textarea => {
// // //         textarea.addEventListener('input', function() {
// // //             this.style.height = 'auto';
// // //             this.style.height = (this.scrollHeight) + 'px';
// // //         });
// // //     });
// // // });


// // // Toggle address input visibility
// // // function toggleAddressInput() {
// // //     const useSaved = document.getElementById('use-saved');
// // //     const addressSection = document.getElementById('address-input-section');
// // //     const addressField = document.getElementById('address');
// // //     const cityField = document.getElementById('city');
// // //     const postalCodeField = document.getElementById('postal_code');
    
// // //     if (useSaved && useSaved.checked) {
// // //         // Hide new address form
// // //         addressSection.style.display = 'none';
// // //         // Disable validation untuk field yang hidden
// // //         addressField.removeAttribute('required');
// // //         cityField.removeAttribute('required');
// // //         postalCodeField.removeAttribute('required');
// // //     } else {
// // //         // Show new address form
// // //         addressSection.style.display = 'block';
// // //         // Enable validation
// // //         addressField.setAttribute('required', 'required');
// // //         cityField.setAttribute('required', 'required');
// // //         postalCodeField.setAttribute('required', 'required');
// // //     }
// // // }

// // // // Form validation
// // // document.addEventListener('DOMContentLoaded', function() {
// // //     const checkoutForm = document.getElementById('checkout-form');
    
// // //     if (checkoutForm) {
// // //         checkoutForm.addEventListener('submit', function(e) {
// // //             const useNew = document.getElementById('use-new');
            
// // //             // Jika pakai alamat baru, validasi input
// // //             if (useNew && useNew.checked) {
// // //                 const address = document.getElementById('address').value.trim();
// // //                 const city = document.getElementById('city').value.trim();
// // //                 const postalCode = document.getElementById('postal_code').value.trim();
                
// // //                 // Validasi alamat tidak kosong
// // //                 if (!address || address.length < 10) {
// // //                     e.preventDefault();
// // //                     showNotification('Please enter a complete address (minimum 10 characters)', 'error');
// // //                     return false;
// // //                 }
                
// // //                 // Validasi kota
// // //                 if (!city || city.length < 3) {
// // //                     e.preventDefault();
// // //                     showNotification('Please enter a valid city name', 'error');
// // //                     return false;
// // //                 }
                
// // //                 // Validasi postal code (5 digit)
// // //                 if (!/^\d{5}$/.test(postalCode)) {
// // //                     e.preventDefault();
// // //                     showNotification('Please enter a valid 5-digit postal code', 'error');
// // //                 return false;
// // //                 }
// // //             }
// // //             // Validasi payment method dipilih
// // //             const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
// // //             if (!paymentMethod) {
// // //                 e.preventDefault();
// // //                 showNotification('Please select a payment method', 'error');
// // //                 return false;
// // //             }
            
// // //             // Show loading state
// // //             const submitBtn = checkoutForm.querySelector('button[type="submit"]');
// // //             if (submitBtn) {
// // //                 submitBtn.disabled = true;
// // //                 submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
// // //             }
            
// // //             return true;
// // //         });
// // //     }
// // // });

// // // // Show notification
// // // function showNotification(message, type = 'info') {
// // //     // Create notification element if doesn't exist
// // //     let notification = document.getElementById('notification');
    
// // //     if (!notification) {
// // //         notification = document.createElement('div');
// // //         notification.id = 'notification';
// // //         notification.className = 'notification';
// // //         document.body.appendChild(notification);
// // //     }
    
// // //     // Set notification content and type
// // //     notification.textContent = message;
// // //     notification.className = 'notification notification-' + type + ' show';
    
// // //     // Auto hide after 3 seconds
// // //     setTimeout(() => {
// // //         notification.classList.remove('show');
// // //     }, 3000);
// // // }

// // // // Auto-resize textarea
// // // document.addEventListener('DOMContentLoaded', function() {
// // //     const textareas = document.querySelectorAll('textarea');
    
// // //     textareas.forEach(textarea => {
// // //         textarea.addEventListener('input', function() {
// // //             this.style.height = 'auto';
// // //             this.style.height = (this.scrollHeight) + 'px';
// // //         });
// // //     });
// // // });

// // // // Fix image error handling - prevent infinite reload
// // // document.addEventListener('DOMContentLoaded', function() {
// // //     const images = document.querySelectorAll('img');
    
// // //     images.forEach(img => {
// // //         img.addEventListener('error', function() {
// // //             // Cek apakah sudah pernah diganti (prevent infinite loop)
// // //             if (!this.dataset.errorHandled) {
// // //                 this.dataset.errorHandled = 'true';
// // //                 this.src = '../assets/default.jpg';
// // //             }
// // //         });
// // //     });
// // // });

// Toggle address input based on saved/new address selection
function toggleAddressInput() {
    const useNew = document.getElementById('use-new');
    const addressSection = document.getElementById('address-input-section');
    
    if (useNew && useNew.checked) {
        addressSection.style.display = 'block';
        // Make fields required
        document.getElementById('address').setAttribute('required', 'required');
        document.getElementById('city').setAttribute('required', 'required');
        document.getElementById('postal_code').setAttribute('required', 'required');
    } else {
        addressSection.style.display = 'none';
        // Remove required attribute
        document.getElementById('address').removeAttribute('required');
        document.getElementById('city').removeAttribute('required');
        document.getElementById('postal_code').removeAttribute('required');
    }
}

// Form validation before submit
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const addressOption = document.querySelector('input[name="address-option"]:checked');
    
    // If using new address, validate fields
    if (!addressOption || addressOption.value === 'new') {
        const address = document.getElementById('address').value.trim();
        const city = document.getElementById('city').value.trim();
        const postalCode = document.getElementById('postal_code').value.trim();
        
        if (!address || !city || !postalCode) {
            e.preventDefault();
            alert('Please fill in all address fields (Address, City, Postal Code)');
            return false;
        }
        
        // Validate postal code (harus angka)
        if (!/^\d+$/.test(postalCode)) {
            e.preventDefault();
            alert('Postal code must contain only numbers');
            return false;
        }
    }
    
    // Validate payment method selected
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if (!paymentMethod) {
        e.preventDefault();
        alert('Please select a payment method');
        return false;
    }
    
    return true;
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial state
    toggleAddressInput();
});
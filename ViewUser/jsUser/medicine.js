// // // Medicine page JavaScript

// // // Modal functionality for "Learn More"
// // document.addEventListener('DOMContentLoaded', function() {
// //     const modal = document.getElementById('modal-overlay');
// //     const closeModal = document.getElementById('close-modal');
// //     const learnMoreButtons = document.querySelectorAll('.learn-btn');

// //     // Open modal when "Learn More" is clicked
// //     learnMoreButtons.forEach(button => {
// //         button.addEventListener('click', function(e) {
// //             e.preventDefault();
            
// //             // Get product data from data attributes (NO supplier/stock info)
// //             const medicineId = this.dataset.id;
// //             const medicineName = this.dataset.name;
// //             const medicineDescription = this.dataset.description;
// //             const medicineBenefits = this.dataset.benefits;
// //             const medicineDosage = this.dataset.dosage;
// //             const medicineWarnings = this.dataset.warnings;
// //             const medicineImg = this.dataset.img;

// //             // Populate modal with product data (NO supplier/stock display)
// //             document.getElementById('modal-title').textContent = medicineName;
// //             document.getElementById('modal-image').src = medicineImg;
// //             document.getElementById('modal-description').textContent = medicineDescription;
// //             document.getElementById('modal-benefits').innerHTML = medicineBenefits;
// //             document.getElementById('modal-dosage').textContent = medicineDosage;
// //             document.getElementById('modal-warnings').textContent = medicineWarnings;

// //             // Show modal with animation
// //             modal.classList.add('active');
// //             document.body.style.overflow = 'hidden'; // Prevent scrolling
// //         });
// //     });

// //     // Close modal when X is clicked
// //     closeModal.addEventListener('click', function() {
// //         modal.classList.remove('active');
// //         document.body.style.overflow = 'auto'; // Enable scrolling
// //     });

// //     // Close modal when clicking outside the modal content
// //     modal.addEventListener('click', function(e) {
// //         if (e.target === modal) {
// //             modal.classList.remove('active');
// //             document.body.style.overflow = 'auto';
// //         }
// //     });

// //     // Close modal with ESC key
// //     document.addEventListener('keydown', function(e) {
// //         if (e.key === 'Escape' && modal.classList.contains('active')) {
// //             modal.classList.remove('active');
// //             document.body.style.overflow = 'auto';
// //         }
// //     });

// //     // Disable buttons for out of stock items (check internally, don't display)
// //     disableOutOfStockItems();

// //     // Add to Cart functionality with stock validation
// //     const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
// //     addToCartButtons.forEach(button => {
// //         button.addEventListener('click', function(e) {
// //             e.preventDefault();
            
// //             const medicineId = this.dataset.id;
// //             const medicineName = this.dataset.name;
// //             const medicinePrice = this.dataset.price;
// //             const medicineStock = parseInt(this.dataset.stock);
// //             const medicineImg = this.dataset.img;

// //             // Check stock availability (internal check)
// //             if (medicineStock === 0) {
// //                 showToast('Sorry, this item is currently unavailable', 'error');
// //                 return;
// //             }

// //             // Add to cart
// //             addToCart(medicineId, medicineName, medicinePrice, medicineImg, medicineStock);
            
// //             showToast(`${medicineName} added to cart!`);
// //         });
// //     });
// // });

// // // Disable buttons for out of stock items (no visual stock display)
// // function disableOutOfStockItems() {
// //     const boxes = document.querySelectorAll('.box');
    
// //     boxes.forEach(box => {
// //         const addToCartBtn = box.querySelector('.add-to-cart-btn');
// //         const orderNowBtn = box.querySelector('.order-now-btn');
        
// //         if (addToCartBtn) {
// //             const stock = parseInt(addToCartBtn.dataset.stock);
            
// //             if (stock === 0) {
// //                 if (addToCartBtn) {
// //                     addToCartBtn.disabled = true;
// //                     addToCartBtn.textContent = 'Out of Stock';
// //                     addToCartBtn.style.cursor = 'not-allowed';
// //                 }
                
// //                 if (orderNowBtn) {
// //                     orderNowBtn.disabled = true;
// //                     orderNowBtn.textContent = 'Out of Stock';
// //                     orderNowBtn.style.cursor = 'not-allowed';
// //                 }
// //             }
// //         }
// //     });
// // }

// // // Add to cart function
// // function addToCart(medicineId, medicineName, medicinePrice, medicineImg, medicineStock) {
// //     // Get existing cart from localStorage or create new one
// //     let cart = JSON.parse(localStorage.getItem('medicineCart')) || [];
    
// //     // Check if item already exists in cart
// //     const existingItemIndex = cart.findIndex(item => item.id === medicineId);
    
// //     if (existingItemIndex > -1) {
// //         // Check if adding one more exceeds stock
// //         if (cart[existingItemIndex].quantity >= medicineStock) {
// //             showToast('Cannot add more items', 'error');
// //             return;
// //         }
// //         // Increase quantity
// //         cart[existingItemIndex].quantity += 1;
// //     } else {
// //         // Add new item to cart
// //         cart.push({
// //             id: medicineId,
// //             name: medicineName,
// //             price: parseFloat(medicinePrice),
// //             image: medicineImg,
// //             quantity: 1,
// //             maxStock: medicineStock
// //         });
// //     }
    
// //     // Save cart to localStorage
// //     localStorage.setItem('medicineCart', JSON.stringify(cart));
    
// //     // Update cart display if cart component exists
// //     updateCartDisplay();
// // }

// // // Update cart display
// // function updateCartDisplay() {
// //     const cart = JSON.parse(localStorage.getItem('medicineCart')) || [];
// //     const cartContainer = document.querySelector('.shopping-cart');
    
// //     if (!cartContainer) return;
    
// //     // Clear current cart display
// //     cartContainer.innerHTML = '';
    
// //     let total = 0;
    
// //     // Add each item to cart display
// //     cart.forEach(item => {
// //         const subtotal = item.price * item.quantity;
// //         total += subtotal;
        
// //         const cartItem = document.createElement('div');
// //         cartItem.className = 'box';
// //         cartItem.innerHTML = `
// //             <img src="${item.image}" alt="${item.name}">
// //             <div class="content">
// //                 <h3>${item.name}</h3>
// //                 <p class="price">Rp ${formatPrice(item.price)} x ${item.quantity}</p>
// //                 <p class="price">Subtotal: Rp ${formatPrice(subtotal)}</p>
// //             </div>
// //             <i class="fa fa-trash" data-id="${item.id}"></i>
// //         `;
        
// //         cartContainer.appendChild(cartItem);
// //     });
    
// //     // Add total and checkout button
// //     const cartTotal = document.createElement('div');
// //     cartTotal.className = 'cart-total';
// //     cartTotal.innerHTML = `
// //         <h3>Total: Rp ${formatPrice(total)}</h3>
// //         <button class="checkout-btn" onclick="goToCheckout()">Proceed to Checkout</button>
// //     `;
// //     cartContainer.appendChild(cartTotal);
    
// //     // Add remove from cart functionality
// //     const removeButtons = cartContainer.querySelectorAll('.fa-trash');
// //     removeButtons.forEach(button => {
// //         button.addEventListener('click', function() {
// //             const medicineId = this.dataset.id;
// //             removeFromCart(medicineId);
// //         });
// //     });
// // }

// // // Remove item from cart
// // function removeFromCart(medicineId) {
// //     let cart = JSON.parse(localStorage.getItem('medicineCart')) || [];
// //     cart = cart.filter(item => item.id !== medicineId);
// //     localStorage.setItem('medicineCart', JSON.stringify(cart));
// //     updateCartDisplay();
// //     showToast('Item removed from cart');
// // }

// // // Format price to Indonesian Rupiah
// // function formatPrice(price) {
// //     return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
// // }

// // // Go to checkout page
// // function goToCheckout() {
// //     const cart = JSON.parse(localStorage.getItem('medicineCart')) || [];
    
// //     if (cart.length === 0) {
// //         showToast('Your cart is empty', 'error');
// //         return;
// //     }
    
// //     window.location.href = 'checkout.php';
// // }

// // // File upload functionality
// // function active() {
// //     const fileInput = document.querySelector('.upload-form input[type="file"]');
// //     fileInput.click();
// // }

// // const fileInput = document.querySelector('.upload-form input[type="file"]');
// // const fileName = document.querySelector('.file-name');

// // if (fileInput) {
// //     fileInput.addEventListener('change', function() {
// //         if (this.files && this.files.length > 0) {
// //             fileName.textContent = this.files[0].name;
            
// //             // Optional: Upload file to server
// //             uploadPrescription(this.files[0]);
// //         }
// //     });
// // }

// // // Upload prescription to server
// // function uploadPrescription(file) {
// //     const formData = new FormData();
// //     formData.append('prescription', file);
// //     formData.append('action', 'upload_prescription');

// //     fetch('mainUser/medicine.php', {
// //         method: 'POST',
// //         body: formData
// //     })
// //     .then(response => response.json())
// //     .then(data => {
// //         if (data.success) {
// //             showToast('Prescription uploaded successfully');
// //         } else {
// //             showToast('Failed to upload prescription', 'error');
// //         }
// //     })
// //     .catch(error => {
// //         console.error('Error:', error);
// //         showToast('An error occurred', 'error');
// //     });
// // }

// // // Smooth scroll to sections
// // document.querySelectorAll('a[href^="#"]').forEach(anchor => {
// //     anchor.addEventListener('click', function(e) {
// //         const href = this.getAttribute('href');
// //         if (href !== '#' && href !== '#!') {
// //             e.preventDefault();
// //             const target = document.querySelector(href);
// //             if (target) {
// //                 target.scrollIntoView({
// //                     behavior: 'smooth',
// //                     block: 'start'
// //                 });
// //             }
// //         }
// //     });
// // });

// // // Show toast notification
// // function showToast(message, type = 'success') {
// //     const toast = document.getElementById('toast-notification');
// //     const toastMessage = document.getElementById('toast-message');
    
// //     if (toast && toastMessage) {
// //         toastMessage.textContent = message;
// //         toast.classList.remove('show', 'success', 'error');
// //         toast.classList.add('show', type);
        
// //         setTimeout(() => {
// //             toast.classList.remove('show');
// //         }, 3000);
// //     }
// // }

// // // Search functionality
// // function searchProducts(query) {
// //     const boxes = document.querySelectorAll('.box');
// //     const searchQuery = query.toLowerCase();

// //     boxes.forEach(box => {
// //         const productName = box.querySelector('.type a').textContent.toLowerCase();
// //         const productDesc = box.querySelector('.overlay p').textContent.toLowerCase();

// //         if (productName.includes(searchQuery) || productDesc.includes(searchQuery)) {
// //             box.style.display = 'block';
// //         } else {
// //             box.style.display = 'none';
// //         }
// //     });
// // }

// // // Filter products by category
// // function filterProducts(category) {
// //     if (category === 'all') {
// //         document.getElementById('top').style.display = 'block';
// //         document.getElementById('bottom').style.display = 'block';
// //     } else if (category === 'wellness') {
// //         document.getElementById('top').style.display = 'block';
// //         document.getElementById('bottom').style.display = 'none';
// //     } else if (category === 'medicine') {
// //         document.getElementById('top').style.display = 'none';
// //         document.getElementById('bottom').style.display = 'block';
// //     }
// // }

// // // Animation on scroll
// // const observerOptions = {
// //     threshold: 0.1,
// //     rootMargin: '0px 0px -100px 0px'
// // };

// // const observer = new IntersectionObserver(function(entries) {
// //     entries.forEach(entry => {
// //         if (entry.isIntersecting) {
// //             entry.target.classList.add('fade-in');
// //         }
// //     });
// // }, observerOptions);

// // // Observe all boxes
// // document.querySelectorAll('.box').forEach(box => {
// //     observer.observe(box);
// // });

// // // Initialize cart display on page load
// // if (document.querySelector('.shopping-cart')) {
// //     updateCartDisplay();
// // }



// //------------------------------------------------------------------------------------------




// // Medicine page JavaScript - Updated with Cart Integration

// // Modal functionality for "Learn More"
// // document.addEventListener('DOMContentLoaded', function() {
// //     const modal = document.getElementById('modal-overlay');
// //     const closeModal = document.getElementById('close-modal');
// //     const learnMoreButtons = document.querySelectorAll('.learn-btn');

// //     // Open modal when "Learn More" is clicked
// //     learnMoreButtons.forEach(button => {
// //         button.addEventListener('click', function(e) {
// //             e.preventDefault();
            
// //             const medicineId = this.dataset.id;
// //             const medicineName = this.dataset.name;
// //             const medicineDescription = this.dataset.description;
// //             const medicineBenefits = this.dataset.benefits;
// //             const medicineDosage = this.dataset.dosage;
// //             const medicineWarnings = this.dataset.warnings;
// //             const medicineImg = this.dataset.img;

// //             document.getElementById('modal-title').textContent = medicineName;
// //             document.getElementById('modal-image').src = medicineImg;
// //             document.getElementById('modal-description').textContent = medicineDescription;
// //             document.getElementById('modal-benefits').innerHTML = medicineBenefits;
// //             document.getElementById('modal-dosage').textContent = medicineDosage;
// //             document.getElementById('modal-warnings').textContent = medicineWarnings;

// //             modal.classList.add('active');
// //             document.body.style.overflow = 'hidden';
// //         });
// //     });

// //     // Close modal when X is clicked
// //     closeModal.addEventListener('click', function() {
// //         modal.classList.remove('active');
// //         document.body.style.overflow = 'auto';
// //     });

// //     // Close modal when clicking outside
// //     modal.addEventListener('click', function(e) {
// //         if (e.target === modal) {
// //             modal.classList.remove('active');
// //             document.body.style.overflow = 'auto';
// //         }
// //     });

// //     // Close modal with ESC key
// //     document.addEventListener('keydown', function(e) {
// //         if (e.key === 'Escape' && modal.classList.contains('active')) {
// //             modal.classList.remove('active');
// //             document.body.style.overflow = 'auto';
// //         }
// //     });

// //     // Add to Cart functionality
// //     const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
// //     addToCartButtons.forEach(button => {
// //         button.addEventListener('click', function(e) {
// //             e.preventDefault();
            
// //             const medicineId = this.dataset.id;
// //             const medicineName = this.dataset.name;
// //             const medicinePrice = parseFloat(this.dataset.price);
// //             const medicineImg = this.dataset.img;

// //             // Add to cart
// //             addToCart(medicineId, medicineName, medicinePrice, medicineImg);
// //         });
// //     });
    
// //     // Update cart badge
// //     updateCartBadge();
// // });

// // // Add to cart function with notification
// // function addToCart(medicineId, medicineName, medicinePrice, medicineImg) {
// //     // Get existing cart
// //     let cart = JSON.parse(localStorage.getItem('medicineCart')) || [];
    
// //     // Check if item already exists
// //     const existingItemIndex = cart.findIndex(item => item.id === medicineId);
    
// //     if (existingItemIndex > -1) {
// //         // Increase quantity
// //         cart[existingItemIndex].quantity += 1;
// //         showToast(`${medicineName} quantity updated in cart!`, 'success');
// //     } else {
// //         // Add new item
// //         cart.push({
// //             id: medicineId,
// //             name: medicineName,
// //             price: medicinePrice,
// //             image: medicineImg,
// //             quantity: 1,
// //             maxStock: 999 // Default max stock
// //         });
// //         showToast(`${medicineName} added to cart!`, 'success');
// //     }
    
// //     // Save to localStorage
// //     localStorage.setItem('medicineCart', JSON.stringify(cart));
    
// //     // Update cart badge
// //     updateCartBadge();
// // }

// // // Update cart badge counter
// // function updateCartBadge() {
// //     const cart = JSON.parse(localStorage.getItem('medicineCart')) || [];
// //     const cartBadge = document.querySelector('.cart-badge');
    
// //     if (cartBadge) {
// //         const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
// //         cartBadge.textContent = totalItems;
        
// //         if (totalItems > 0) {
// //             cartBadge.style.display = 'inline-block';
// //         } else {
// //             cartBadge.style.display = 'none';
// //         }
// //     }
// // }

// // // Format price to Indonesian Rupiah
// // function formatPrice(price) {
// //     return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
// // }

// // // File upload functionality
// // function active() {
// //     const fileInput = document.querySelector('.upload-form input[type="file"]');
// //     fileInput.click();
// // }

// // const fileInput = document.querySelector('.upload-form input[type="file"]');
// // const fileName = document.querySelector('.file-name');

// // if (fileInput) {
// //     fileInput.addEventListener('change', function() {
// //         if (this.files && this.files.length > 0) {
// //             fileName.textContent = this.files[0].name;
// //             uploadPrescription(this.files[0]);
// //         }
// //     });
// // }

// // // Upload prescription to server
// // function uploadPrescription(file) {
// //     const formData = new FormData();
// //     formData.append('prescription', file);
// //     formData.append('action', 'upload_prescription');

// //     fetch('upload_prescription.php', {
// //         method: 'POST',
// //         body: formData
// //     })
// //     .then(response => response.json())
// //     .then(data => {
// //         if (data.success) {
// //             showToast('Prescription uploaded successfully');
// //             // Store prescription path in sessionStorage
// //             sessionStorage.setItem('prescriptionPath', data.file_path);
// //         } else {
// //             showToast('Failed to upload prescription', 'error');
// //         }
// //     })
// //     .catch(error => {
// //         console.error('Error:', error);
// //         showToast('An error occurred', 'error');
// //     });
// // }

// // // Show toast notification
// // function showToast(message, type = 'success') {
// //     const toast = document.getElementById('toast-notification');
// //     const toastMessage = document.getElementById('toast-message');
    
// //     if (toast && toastMessage) {
// //         // Set icon based on type
// //         const icon = toast.querySelector('i');
// //         if (type === 'success') {
// //             icon.className = 'fa fa-check-circle';
// //         } else if (type === 'error') {
// //             icon.className = 'fa fa-exclamation-circle';
// //         }
        
// //         toastMessage.textContent = message;
// //         toast.classList.remove('show', 'success', 'error');
// //         toast.classList.add('show', type);
        
// //         setTimeout(() => {
// //             toast.classList.remove('show');
// //         }, 3000);
// //     }
// // }

// // // Search functionality
// // function searchProducts(query) {
// //     const boxes = document.querySelectorAll('.box');
// //     const searchQuery = query.toLowerCase();

// //     boxes.forEach(box => {
// //         const productName = box.querySelector('.type a').textContent.toLowerCase();
// //         const productDesc = box.querySelector('.overlay p').textContent.toLowerCase();

// //         if (productName.includes(searchQuery) || productDesc.includes(searchQuery)) {
// //             box.style.display = 'block';
// //         } else {
// //             box.style.display = 'none';
// //         }
// //     });
// // }

// // // Filter products by category
// // function filterProducts(category) {
// //     if (category === 'all') {
// //         document.getElementById('top').style.display = 'block';
// //         document.getElementById('bottom').style.display = 'block';
// //     } else if (category === 'wellness') {
// //         document.getElementById('top').style.display = 'block';
// //         document.getElementById('bottom').style.display = 'none';
// //     } else if (category === 'medicine') {
// //         document.getElementById('top').style.display = 'none';
// //         document.getElementById('bottom').style.display = 'block';
// //     }
// // }

// // // Animation on scroll
// // const observerOptions = {
// //     threshold: 0.1,
// //     rootMargin: '0px 0px -100px 0px'
// // };

// // const observer = new IntersectionObserver(function(entries) {
// //     entries.forEach(entry => {
// //         if (entry.isIntersecting) {
// //             entry.target.classList.add('fade-in');
// //         }
// //     });
// // }, observerOptions);

// // // Observe all boxes
// // document.querySelectorAll('.box').forEach(box => {
// //     observer.observe(box);
// // });


// //----------------------------------------------------------------------------------------//

// // ================= Medicine page JavaScript - Server Cart (SESSION) =================

// // Modal "Learn More"
// document.addEventListener('DOMContentLoaded', function () {
//   const modal = document.getElementById('modal-overlay');
//   const closeModal = document.getElementById('close-modal');
//   const learnMoreButtons = document.querySelectorAll('.learn-btn');

//   if (learnMoreButtons && modal && closeModal) {
//     learnMoreButtons.forEach(button => {
//       button.addEventListener('click', function (e) {
//         e.preventDefault();

//         const medicineName        = this.dataset.name || '';
//         const medicineDescription = this.dataset.description || '';
//         const medicineBenefits    = this.dataset.benefits || '';
//         const medicineDosage      = this.dataset.dosage || '';
//         const medicineWarnings    = this.dataset.warnings || '';
//         const medicineImg         = this.dataset.img || '';

//         document.getElementById('modal-title').textContent      = medicineName;
//         document.getElementById('modal-image').src              = medicineImg;
//         document.getElementById('modal-description').textContent= medicineDescription;
//         document.getElementById('modal-benefits').innerHTML     = medicineBenefits;
//         document.getElementById('modal-dosage').textContent     = medicineDosage;
//         document.getElementById('modal-warnings').textContent   = medicineWarnings;

//         modal.classList.add('active');
//         document.body.style.overflow = 'hidden';
//       });
//     });

//     closeModal.addEventListener('click', function () {
//       modal.classList.remove('active');
//       document.body.style.overflow = 'auto';
//     });

//     modal.addEventListener('click', function (e) {
//       if (e.target === modal) {
//         modal.classList.remove('active');
//         document.body.style.overflow = 'auto';
//       }
//     });

//     document.addEventListener('keydown', function (e) {
//       if (e.key === 'Escape' && modal.classList.contains('active')) {
//         modal.classList.remove('active');
//         document.body.style.overflow = 'auto';
//       }
//     });
//   }

//   // Binding tombol Add to Cart -> kirim ke server
//   bindAddToCart();

//   // Sinkron badge cart dari server saat page load
//   updateCartBadgeFromServer();

//   // Animasi on scroll
//   document.querySelectorAll('.box').forEach(box => {
//     observer.observe(box);
//   });
// });

// // function bindAddToCart() {
// //   const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
// //   addToCartButtons.forEach(button => {
// //     button.addEventListener('click', function (e) {
// //       e.preventDefault();

// //       const id    = this.dataset.id;
// //       const name  = this.dataset.name;
// //       const price = parseFloat(this.dataset.price || '0');
// //       const img   = this.dataset.img || '';

// //       if (!id || !name || !price || price <= 0) {
// //         showToast('Data produk tidak valid', 'error');
// //         return;
// //       }

// //       const fd = new FormData();
// //       fd.append('action', 'add');
// //       fd.append('id', id);
// //       fd.append('name', name);
// //       fd.append('price', price);
// //       fd.append('image', img);
// //       fd.append('quantity', 1);

// //       fetch('cart.php', {
// //         method: 'POST',
// //         headers: { 'X-Requested-With': 'XMLHttpRequest' },
// //         body: fd
// //       })
// //         .then(r => r.json())
// //         .then(j => {
// //           if (j && j.redirect) {
// //             // belum login / session habis -> diarahkan ke login, lalu balik lagi
// //             window.location.href = j.redirect;
// //             return;
// //           }
// //           if (j.success) {
// //             showToast(`${name} added to cart!`, 'success');
// //             updateCartBadgeFromServer();
// //           } else {
// //             showToast(j.message || 'Gagal menambah ke cart', 'error');
// //           }
// //         })
// //         .catch(err => {
// //           console.error(err);
// //           showToast('Terjadi kesalahan', 'error');
// //         });
// //     });
// //   });
// // }

// document.addEventListener('click', async (e) => {
//   const btn = e.target.closest('.add-to-cart-btn');
//   if (!btn) return;            // bukan klik tombol Add to Cart
//   e.preventDefault();

//   // Ambil data dari tombol
//   const id    = btn.dataset.id;
//   const name  = btn.dataset.name;
//   const price = parseFloat(btn.dataset.price || '0');
//   const img   = btn.dataset.img || '';

//   if (!id || !name || !price || price <= 0 || Number.isNaN(price)) {
//     showToast && showToast('Data produk tidak valid', 'error');
//     console.warn('Invalid dataset:', { id, name, price, img });
//     return;
//   }

//   // Siapkan form
//   const fd = new FormData();
//   fd.append('action', 'add');
//   fd.append('id', id);
//   fd.append('name', name);
//   fd.append('price', price);
//   fd.append('image', img);
//   fd.append('quantity', 1);

//   // Kirim ke server
//   try {
//     const resp = await fetch('cart.php', {
//       method: 'POST',
//       headers: { 'X-Requested-With': 'XMLHttpRequest' },
//       body: fd
//     });

//     const text = await resp.text();   // ambil sebagai teks dulu
//     let data;
//     try {
//       data = JSON.parse(text);        // coba parse JSON
//     } catch (err) {
//       console.warn('Response bukan JSON murni. Isi:', text);
//       // fallback: anggap sukses (barang sudah masuk session)
//       data = { success: true };
//     }

//     if (data.redirect) {
//       window.location.href = data.redirect;
//       return;
//     }

//     if (data.success) {
//       showToast && showToast(`${name} added to cart!`, 'success');
//       if (typeof updateCartBadgeFromServer === 'function') {
//         updateCartBadgeFromServer();
//       }
//     } else {
//       showToast && showToast(data.message || 'Gagal menambah ke cart', 'error');
//     }
//   } catch (err) {
//     console.error(err);
//     showToast && showToast('Terjadi kesalahan', 'error');
//   }
// });

// // Badge cart ambil dari SESSION di server
// function updateCartBadgeFromServer() {
//   const fd = new FormData();
//   fd.append('action', 'get_cart');

//   fetch('cart.php', {
//     method: 'POST',
//     headers: { 'X-Requested-With': 'XMLHttpRequest' },
//     body: fd
//   })
//     .then(r => r.json())
//     .then(j => {
//       const badge = document.querySelector('.cart-badge');
//       if (!badge) return;

//       if (j && j.redirect) {
//         // belum login, sembunyikan badge
//         badge.style.display = 'none';
//         return;
//       }

//       const total = j && j.cartCount ? j.cartCount : 0;
//       badge.textContent = total;
//       badge.style.display = total > 0 ? 'inline-block' : 'none';
//     })
//     .catch(() => { /* diamkan */ });
// }

// // ===== Utility umum =====
// function formatPrice(price) {
//   return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
// }

// // Upload resep (biarkan seperti semula)
// function active() {
//   const fileInput = document.querySelector('.upload-form input[type="file"]');
//   if (fileInput) fileInput.click();
// }
// const fileInput = document.querySelector('.upload-form input[type="file"]');
// const fileName  = document.querySelector('.file-name');
// if (fileInput) {
//   fileInput.addEventListener('change', function () {
//     if (this.files && this.files.length > 0) {
//       if (fileName) fileName.textContent = this.files[0].name;
//       uploadPrescription(this.files[0]);
//     }
//   });
// }
// function uploadPrescription(file) {
//   const formData = new FormData();
//   formData.append('prescription', file);
//   formData.append('action', 'upload_prescription');

//   fetch('upload_prescription.php', { method: 'POST', body: formData })
//     .then(r => r.json())
//     .then(data => {
//       if (data.success) {
//         showToast('Prescription uploaded successfully');
//         sessionStorage.setItem('prescriptionPath', data.file_path);
//       } else {
//         showToast('Failed to upload prescription', 'error');
//       }
//     })
//     .catch(() => showToast('An error occurred', 'error'));
// }

// // Toast (pakai elemen yang sudah ada di HTML)
// function showToast(message, type = 'success') {
//   const toast = document.getElementById('toast-notification');
//   const toastMessage = document.getElementById('toast-message');
//   if (!toast || !toastMessage) return;

//   const icon = toast.querySelector('i');
//   icon.className = (type === 'success') ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';

//   toastMessage.textContent = message;
//   toast.classList.remove('show', 'success', 'error');
//   toast.classList.add('show', type);

//   setTimeout(() => { toast.classList.remove('show'); }, 3000);
// }

// // Search & Filter (tetap)
// function searchProducts(query) {
//   const boxes = document.querySelectorAll('.box');
//   const q = (query || '').toLowerCase();
//   boxes.forEach(box => {
//     const name = box.querySelector('.type a')?.textContent.toLowerCase() || '';
//     const desc = box.querySelector('.overlay p')?.textContent.toLowerCase() || '';
//     box.style.display = (name.includes(q) || desc.includes(q)) ? 'block' : 'none';
//   });
// }
// function filterProducts(category) {
//   const top = document.getElementById('top');
//   const bottom = document.getElementById('bottom');
//   if (!top || !bottom) return;
//   if (category === 'all') { top.style.display = 'block'; bottom.style.display = 'block'; }
//   else if (category === 'wellness') { top.style.display = 'block'; bottom.style.display = 'none'; }
//   else if (category === 'medicine') { top.style.display = 'none'; bottom.style.display = 'block'; }
// }

// // Animasi masuk
// const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -100px 0px' };
// const observer = new IntersectionObserver(function (entries) {
//   entries.forEach(entry => {
//     if (entry.isIntersecting) entry.target.classList.add('fade-in');
//   });
// }, observerOptions);


// ================= Medicine page JavaScript - Server Cart (SESSION) =================

// Modal "Learn More"
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('modal-overlay');
  const closeModal = document.getElementById('close-modal');
  const learnMoreButtons = document.querySelectorAll('.learn-btn');

  if (learnMoreButtons && modal && closeModal) {
    learnMoreButtons.forEach(button => {
      button.addEventListener('click', function (e) {
        e.preventDefault();

        const medicineName        = this.dataset.name || '';
        const medicineDescription = this.dataset.description || '';
        const medicineBenefits    = this.dataset.benefits || '';
        const medicineDosage      = this.dataset.dosage || '';
        const medicineWarnings    = this.dataset.warnings || '';
        const medicineImg         = this.dataset.img || '';

        document.getElementById('modal-title').textContent      = medicineName;
        document.getElementById('modal-image').src              = medicineImg;
        document.getElementById('modal-description').textContent= medicineDescription;
        document.getElementById('modal-benefits').innerHTML     = medicineBenefits;
        document.getElementById('modal-dosage').textContent     = medicineDosage;
        document.getElementById('modal-warnings').textContent   = medicineWarnings;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
      });
    });

    closeModal.addEventListener('click', function () {
      modal.classList.remove('active');
      document.body.style.overflow = 'auto';
    });

    modal.addEventListener('click', function (e) {
      if (e.target === modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.classList.contains('active')) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
      }
    });
  }

  // Sinkron badge cart dari server saat page load
  updateCartBadgeFromServer();

  // Animasi on scroll
  document.querySelectorAll('.box').forEach(box => {
    observer.observe(box);
  });
  
  // Fix image error handling - prevent infinite reload
  fixImageErrors();
});

// Handle Add to Cart dengan event delegation
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.add-to-cart-btn');
  if (!btn) return;
  e.preventDefault();

  // Ambil data dari tombol
  const id    = btn.dataset.id;
  const name  = btn.dataset.name;
  const price = parseFloat(btn.dataset.price || '0');
  const img   = btn.dataset.img || '';

  if (!id || !name || !price || price <= 0 || Number.isNaN(price)) {
    showToast && showToast('Data produk tidak valid', 'error');
    console.warn('Invalid dataset:', { id, name, price, img });
    return;
  }

  // Siapkan form
  const fd = new FormData();
  fd.append('action', 'add');
  fd.append('id', id);
  fd.append('name', name);
  fd.append('price', price);
  fd.append('image', img);
  fd.append('quantity', 1);

  // Kirim ke server
  try {
    const resp = await fetch('cart.php', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd
    });

    const text = await resp.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch (err) {
      console.warn('Response bukan JSON murni. Isi:', text);
      data = { success: true };
    }

    if (data.redirect) {
      window.location.href = data.redirect;
      return;
    }

    if (data.success) {
      showToast && showToast(`${name} added to cart!`, 'success');
      if (typeof updateCartBadgeFromServer === 'function') {
        updateCartBadgeFromServer();
      }
    } else {
      showToast && showToast(data.message || 'Gagal menambah ke cart', 'error');
    }
  } catch (err) {
    console.error(err);
    showToast && showToast('Terjadi kesalahan', 'error');
  }
});

// Badge cart ambil dari SESSION di server
function updateCartBadgeFromServer() {
  const fd = new FormData();
  fd.append('action', 'get_cart');

  fetch('cart.php', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: fd
  })
    .then(r => r.json())
    .then(j => {
      const badge = document.querySelector('.cart-badge');
      if (!badge) return;

      if (j && j.redirect) {
        badge.style.display = 'none';
        return;
      }

      const total = j && j.cartCount ? j.cartCount : 0;
      badge.textContent = total;
      badge.style.display = total > 0 ? 'inline-block' : 'none';
    })
    .catch(() => { /* diamkan */ });
}

// Fix image error handling - CRITICAL FIX untuk prevent infinite reload
function fixImageErrors() {
  const images = document.querySelectorAll('img');
  
  images.forEach(img => {
    // Remove existing onerror if any
    img.removeAttribute('onerror');
    
    // Add error handler with flag to prevent infinite loop
    img.addEventListener('error', function() {
      if (!this.dataset.errorHandled) {
        this.dataset.errorHandled = 'true';
        console.warn('Image failed to load:', this.src);
        this.src = '../assets/default.jpg';
      } else {
        // Jika default.jpg juga gagal, hide image
        this.style.display = 'none';
        console.error('Default image also failed to load');
      }
    }, { once: false });
  });
}

// ===== Utility umum =====
function formatPrice(price) {
  return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Upload resep
function active() {
  const fileInput = document.querySelector('.upload-form input[type="file"]');
  if (fileInput) fileInput.click();
}

const fileInput = document.querySelector('.upload-form input[type="file"]');
const fileName  = document.querySelector('.file-name');
if (fileInput) {
  fileInput.addEventListener('change', function () {
    if (this.files && this.files.length > 0) {
      if (fileName) fileName.textContent = this.files[0].name;
      uploadPrescription(this.files[0]);
    }
  });
}

function uploadPrescription(file) {
  const formData = new FormData();
  formData.append('prescription', file);
  formData.append('action', 'upload_prescription');

  fetch('upload_prescription.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showToast('Prescription uploaded successfully');
        sessionStorage.setItem('prescriptionPath', data.file_path);
      } else {
        showToast('Failed to upload prescription', 'error');
      }
    })
    .catch(() => showToast('An error occurred', 'error'));
}

// Toast notification
function showToast(message, type = 'success') {
  const toast = document.getElementById('toast-notification');
  const toastMessage = document.getElementById('toast-message');
  if (!toast || !toastMessage) return;

  const icon = toast.querySelector('i');
  if (icon) {
    icon.className = (type === 'success') ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';
  }

  toastMessage.textContent = message;
  toast.classList.remove('show', 'success', 'error');
  toast.classList.add('show', type);

  setTimeout(() => { toast.classList.remove('show'); }, 3000);
}

// Search products
function searchProducts(query) {
  const boxes = document.querySelectorAll('.box');
  const q = (query || '').toLowerCase();
  boxes.forEach(box => {
    const name = box.querySelector('.type a')?.textContent.toLowerCase() || '';
    const desc = box.querySelector('.overlay p')?.textContent.toLowerCase() || '';
    box.style.display = (name.includes(q) || desc.includes(q)) ? 'block' : 'none';
  });
}

// Filter products by category
function filterProducts(category) {
  const top = document.getElementById('top');
  const bottom = document.getElementById('bottom');
  if (!top || !bottom) return;
  if (category === 'all') { 
    top.style.display = 'block'; 
    bottom.style.display = 'block'; 
  } else if (category === 'wellness') { 
    top.style.display = 'block'; 
    bottom.style.display = 'none'; 
  } else if (category === 'medicine') { 
    top.style.display = 'none'; 
    bottom.style.display = 'block'; 
  }
}

// Animasi masuk on scroll
const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -100px 0px' };
const observer = new IntersectionObserver(function (entries) {
  entries.forEach(entry => {
    if (entry.isIntersecting) entry.target.classList.add('fade-in');
  });
}, observerOptions);
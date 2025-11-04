// Toggle search bar visibility
const searchBar = document.getElementById('search-bar');
const searchBtn = document.getElementById('searchBtn');
const searchInput = document.getElementById('searchInput');

function toggleSearch() {
  if (!searchBar) return;  // Ensure searchBar exists
  searchBar.classList.toggle('active');  // Toggle visibility of search bar
  if (searchBar.classList.contains('active')) {
    searchInput.focus();  // Focus the input field when the search bar is shown
  }
}

// Aksi saat klik tombol Search
searchBtn.addEventListener('click', () => {
  const keyword = searchInput.value.trim();
  if (keyword) {
    // arahkan ke halaman hasil pencarian
    window.location.href = `medicine.php?search=${encodeURIComponent(keyword)}`;
  }
});

// Close the search bar when clicking outside
document.addEventListener('click', (e) => {
  if (!e.target.closest('.search-container') && searchBar) {
    searchBar.classList.remove('active');
  }
});

// Handle login link on login.php page
document.addEventListener("DOMContentLoaded", function() {
  const loginLink = document.getElementById("loginLink");

  if (loginLink) {
    // cek apakah user sedang di login.php
    const currentPage = window.location.pathname.split("/").pop();
    if (currentPage === "login.php") {
      loginLink.addEventListener("click", function(e) {
        e.preventDefault(); // biar nggak reload
        showNotification("⚠️ Kamu sudah ada di halaman login");
      });
    }
  }
});

// Show Notification
function showNotification(text) {
  const notif = document.createElement("div");
  notif.textContent = text;
  notif.style.position = "fixed";
  notif.style.top = "20px";
  notif.style.right = "20px";
  notif.style.background = "#ff7675";
  notif.style.color = "#fff";
  notif.style.padding = "10px 20px";
  notif.style.borderRadius = "8px";
  notif.style.zIndex = "9999";
  notif.style.fontFamily = "sans-serif";
  document.body.appendChild(notif);

  setTimeout(() => notif.remove(), 2500);
}

// Profile Dropdown
const userToggle = document.getElementById('userDropdownToggle');
const dropdown = document.getElementById('userDropdown');

// Toggle dropdown visibility when user clicks on the user info
userToggle.addEventListener('click', (e) => {
  e.stopPropagation(); // biar kliknya gak ikut trigger event document
  dropdown.classList.toggle('show');
});

// Close the dropdown when clicking outside
document.addEventListener('click', (e) => {
  if (!userToggle.contains(e.target) && !dropdown.contains(e.target)) {
    dropdown.classList.remove('show');
  }
});

// (Optional) Close dropdown on ESC key press
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    dropdown.classList.remove('show');
  }
});

// Update cart badge on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartBadgeInNavbar();
});

function updateCartBadgeInNavbar() {
    const cart = JSON.parse(localStorage.getItem('medicineCart')) || [];
    const cartBadge = document.querySelector('.cart-badge');
    
    if (cartBadge) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartBadge.textContent = totalItems;
        
        if (totalItems > 0) {
            cartBadge.style.display = 'inline-flex';
        } else {
            cartBadge.style.display = 'none';
        }
    }
}

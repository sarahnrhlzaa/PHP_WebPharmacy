// ==================== JSADMIN/NAVBAR.JS ====================

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== SIDEBAR TOGGLE ==========
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const header = document.querySelector('.header');
    const mainContent = document.querySelector('.main-content');
    
    let sidebarOpen = true;
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebarOpen = !sidebarOpen;
            
            if (sidebarOpen) {
                sidebar.classList.remove('closed');
                header.classList.remove('full-width');
                if (mainContent) {
                    mainContent.classList.remove('full-width');
                }
            } else {
                sidebar.classList.add('closed');
                header.classList.add('full-width');
                if (mainContent) {
                    mainContent.classList.add('full-width');
                }
            }
        });
    }
    
    // ========== USER DROPDOWN ==========
    const userToggle = document.getElementById('userToggle');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userToggle && userDropdown) {
        userToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('open');
            userToggle.classList.toggle('open');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('open');
                userToggle.classList.remove('open');
            }
        });
    }
    
    // // ========== TRANSACTION DROPDOWN ==========
    // const transactionToggle = document.getElementById('transactionToggle');
    // const transactionDropdown = document.getElementById('transactionDropdown');
    
    // if (transactionToggle && transactionDropdown) {
    //     transactionToggle.addEventListener('click', function(e) {
    //         e.preventDefault();
    //         transactionDropdown.classList.toggle('open');
    //         transactionToggle.classList.toggle('open');
    //     });
    // }
    
    // ========== RESPONSIVE SIDEBAR ==========
    function checkWindowSize() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('closed');
            header.classList.add('full-width');
            if (mainContent) {
                mainContent.classList.add('full-width');
            }
            sidebarOpen = false;
        } else {
            if (sidebarOpen) {
                sidebar.classList.remove('closed');
                header.classList.remove('full-width');
                if (mainContent) {
                    mainContent.classList.remove('full-width');
                }
            }
        }
    }
    
    // Check on load
    checkWindowSize();
    
    // Check on resize
    window.addEventListener('resize', checkWindowSize);
    
});
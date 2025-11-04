// ========== SMOOTH SCROLL ==========
// Smooth scroll untuk semua link dengan #
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            // Hitung offset navbar (sesuaikan dengan tinggi navbar kamu)
            const navbarHeight = 100; // Sesuaikan dengan tinggi navbar
            const targetPosition = target.offsetTop - navbarHeight;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// ========== SCROLL REVEAL ANIMATION ==========
// Intersection Observer untuk animasi cards saat scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            
            // Unobserve setelah animasi selesai biar ga repeat
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Apply animation untuk semua cards
document.querySelectorAll('.about-card, .service-card, .why-item').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(card);
});

// ========== COUNTER ANIMATION ==========
// Animasi angka di stats section
const statNumbers = document.querySelectorAll('.stat-number');

const animateCounters = (entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const target = entry.target;
            const targetNumber = target.textContent.replace(/\D/g, ''); // Ambil angka aja
            const suffix = target.textContent.replace(/[0-9]/g, ''); // Ambil suffix (K+, +, dll)
            
            let current = 0;
            const increment = targetNumber / 50; // Kecepatan counter
            const timer = setInterval(() => {
                current += increment;
                if (current >= targetNumber) {
                    target.textContent = targetNumber + suffix;
                    clearInterval(timer);
                } else {
                    target.textContent = Math.floor(current) + suffix;
                }
            }, 30);
            
            observer.unobserve(target);
        }
    });
};

const counterObserver = new IntersectionObserver(animateCounters, {
    threshold: 0.5
});

statNumbers.forEach(number => {
    counterObserver.observe(number);
});

// ========== NAVBAR SCROLL EFFECT (OPTIONAL) ==========
// Tambah shadow ke navbar saat scroll
let lastScroll = 0;
const navbar = document.querySelector('.header');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
        if (navbar) {
            navbar.style.boxShadow = '0 2px 15px rgba(0,0,0,0.1)';
        }
    } else {
        if (navbar) {
            navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        }
    }
    
    lastScroll = currentScroll;
});

// ========== PARALLAX EFFECT (OPTIONAL) ==========
// Efek parallax sederhana untuk hero section
window.addEventListener('scroll', () => {
    const hero = document.querySelector('.hero');
    if (hero) {
        const scrolled = window.pageYOffset;
        hero.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
});

// ========== LAZY LOADING OPTIMIZATION ==========
// Load images dengan lazy loading
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// ========== PREVENT DEFAULT FORM SUBMISSION (OPTIONAL) ==========
// Kalau ada form di halaman ini
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        // Tambahkan validasi atau AJAX submit kalau perlu
        // e.preventDefault();
    });
});
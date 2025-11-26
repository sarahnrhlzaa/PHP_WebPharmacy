// Animasi masuk untuk setiap card saat halaman dimuat
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.role-card');
    
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200 + (index * 150));
    });
});
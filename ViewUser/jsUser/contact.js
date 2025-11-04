// Smooth scroll animation
document.addEventListener('DOMContentLoaded', function() {
    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all contact cards and info boxes
    const animatedElements = document.querySelectorAll('.contact-card, .info-box, .form-container');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease-out';
        observer.observe(el);
    });
});

// Contact form submission
const contactForm = document.getElementById('contactForm');

contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        subject: document.getElementById('subject').value,
        message: document.getElementById('message').value
    };
    
    // Show loading state
    const submitBtn = contactForm.querySelector('.submit-btn');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitBtn.disabled = true;
    
    // Simulate form submission (replace with actual AJAX call to your PHP backend)
    setTimeout(function() {
        // Success message
        alert('Thank you for contacting us! We will get back to you soon.');
        
        // Reset form
        contactForm.reset();
        
        // Reset button
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
        
        // Optional: Send to WhatsApp instead
        // const whatsappMessage = `Name: ${formData.name}%0AEmail: ${formData.email}%0APhone: ${formData.phone}%0ASubject: ${formData.subject}%0AMessage: ${formData.message}`;
        // window.open(`https://wa.me/6282384390165?text=${whatsappMessage}`, '_blank');
        
    }, 2000);
    
    // If you want to use AJAX to send to PHP:
    /*
    fetch('submit_contact.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Message sent successfully!');
            contactForm.reset();
        } else {
            alert('Error sending message. Please try again.');
        }
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message. Please try again.');
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
    */
});

// Add hover effect for contact cards
const contactCards = document.querySelectorAll('.contact-card');

contactCards.forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Click to copy functionality (optional)
function copyToClipboard(text, element) {
    navigator.clipboard.writeText(text).then(function() {
        // Show copied notification
        const notification = document.createElement('div');
        notification.textContent = 'Copied!';
        notification.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #25D366;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: bold;
            z-index: 9999;
            animation: fadeInOut 2s ease-in-out;
        `;
        document.body.appendChild(notification);
        
        setTimeout(function() {
            notification.remove();
        }, 2000);
    });
}

// Add click to copy for phone numbers (optional feature)
document.querySelectorAll('.contact-card p').forEach(p => {
    if (p.textContent.includes('+62') || p.textContent.match(/^\d+$/)) {
        p.style.cursor = 'pointer';
        p.title = 'Click to copy';
        
        p.addEventListener('click', function() {
            copyToClipboard(this.textContent, this);
        });
    }
});

// Validate phone number format
const phoneInput = document.getElementById('phone');
phoneInput.addEventListener('input', function(e) {
    // Remove non-numeric characters
    let value = e.target.value.replace(/\D/g, '');
    
    // Add formatting (optional)
    if (value.length > 0) {
        if (value.startsWith('0')) {
            value = '+62' + value.substring(1);
        } else if (!value.startsWith('+')) {
            value = '+62' + value;
        }
    }
    
    e.target.value = value;
});

// Add CSS for copy notification animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
        20% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        80% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        100% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
    }
`;
document.head.appendChild(style);
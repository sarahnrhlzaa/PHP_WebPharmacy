<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../cssuser/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Contact Us - PharmaCare</title>
</head>
<body>
    <!-- INCLUDE NAVBAR -->
    <?php include 'navbar.php'; ?>

    <!-- HERO SECTION -->
    <section class="contact-hero">
        <div class="hero-content">
            <h1>Get In Touch With Us</h1>
            <p>We're here to help you 24/7. Reach out to us anytime!</p>
        </div>
    </section>

    <!-- CONTACT CONTENT -->
    <section class="contact-section">
        <div class="container">
            <!-- Contact Info Cards -->
            <div class="contact-cards">
                <div class="contact-card">
                    <div class="icon-wrapper phone">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3>Phone</h3>
                    <p>2156893</p>
                    <a href="tel:2156893" class="contact-link">Call Now</a>
                </div>

                <div class="contact-card whatsapp-card">
                    <div class="icon-wrapper whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h3>WhatsApp</h3>
                    <p>+62 823 8439 0165</p>
                    <a href="https://wa.me/6282384390165?text=Halo%20PharmaCare,%20saya%20ingin%20bertanya" target="_blank" class="contact-link whatsapp-btn">
                        <i class="fab fa-whatsapp"></i> Chat Now
                    </a>
                </div>

                <div class="contact-card">
                    <div class="icon-wrapper email">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email</h3>
                    <p>marketing@pharmacare.com</p>
                    <a href="mailto:marketing@pharmacare.com" class="contact-link">Send Email</a>
                </div>

                <div class="contact-card">
                    <div class="icon-wrapper web">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>Website</h3>
                    <p>www.PharmaCare.com</p>
                    <a href="https://www.PharmaCare.com" target="_blank" class="contact-link">Visit Website</a>
                </div>
            </div>

            <!-- Additional Info Section -->
            <div class="info-section">
                <div class="info-box">
                    <div class="info-header">
                        <i class="fas fa-headset"></i>
                        <h2>Call Center</h2>
                    </div>
                    <p class="info-detail">2480-995</p>
                    <p class="info-description">Available 24/7 for your inquiries</p>
                </div>

                <div class="info-box">
                    <div class="info-header">
                        <i class="fas fa-building"></i>
                        <h2>Company</h2>
                    </div>
                    <p class="info-detail">PT Sehat Indonesia</p>
                    <p class="info-description">Trusted pharmacy partner since 2020</p>
                </div>

                <div class="info-box">
                    <div class="info-header">
                        <i class="fas fa-clock"></i>
                        <h2>Operating Hours</h2>
                    </div>
                    <p class="info-detail">Monday - Sunday</p>
                    <p class="info-description">24 Hours / 7 Days a Week</p>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="form-section">
                <div class="form-container">
                    <h2>Send Us a Message</h2>
                    <p class="form-subtitle">Have questions? Fill out the form below and we'll get back to you soon!</p>
                    
                    <form id="contactForm" class="contact-form">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" placeholder="What is this about?" required>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                        </div>

                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- INCLUDE FOOTER -->
    <?php include 'footer.php'; ?>

    <script src="../jsUser/contact.js"></script>
</body>
</html>
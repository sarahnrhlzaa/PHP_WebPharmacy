<?php
// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include koneksi database
include '../../Connection/connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../cssuser/style.css">
    <title>Web Pharmacy Management</title>
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="main">
    <div class="left">
        <h2> We Are Here For Your Care</h2>
        <h1> We Are The Best Pharmacy</h1>
        <p> We are here for your care 24/7. Any help just call us</p>

        <a href="#about">
            <button>Explore Our Pharmacy</button>
        </a>
    </div>

    <div class="right">
        <img src="../../assets/mainhome.png" alt="Main Home">
    </div>
</section>

<section class="about" id="about">
        <div class="container-section">
            <h2 class="section-title">About PharmaCare</h2>
            <p class="section-subtitle">Committed to Your Health Since Day One</p>
            
            <div class="about-content">
                <div class="about-card">
                    <i class="fas fa-heartbeat"></i>
                    <h3>Our Mission</h3>
                    <p>To provide accessible, affordable, and quality healthcare solutions to communities, ensuring everyone has the medicines and support they need for a healthier life.</p>
                </div>

                <div class="about-card">
                    <i class="fas fa-eye"></i>
                    <h3>Our Vision</h3>
                    <p>To become the most trusted pharmacy network, recognized for innovation, compassion, and excellence in pharmaceutical care across the nation.</p>
                </div>

                <div class="about-card">
                    <i class="fas fa-star"></i>
                    <h3>Our Values</h3>
                    <p>Integrity, Quality, Accessibility, and Customer Care are the pillars that guide every decision we make and every service we provide.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">Pharmacy Outlets</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100K+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">5000+</div>
                <div class="stat-label">Medicine Products</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24H/W</div>
                <div class="stat-label">Customer Support</div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container-section">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Comprehensive Healthcare Solutions for You</p>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h3>Prescription Medicines</h3>
                    <p>Wide range of prescription medications with expert pharmacist consultation available at all times.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3>Online Ordering</h3>
                    <p>Order your medicines online with easy delivery to your doorstep. Fast, secure, and convenient.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>Health Consultation</h3>
                    <p>Free consultation with licensed pharmacists and healthcare professionals for your wellness needs.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-first-aid"></i>
                    </div>
                    <h3>Health Products</h3>
                    <p>Quality vitamins, supplements, and medical equipment for complete healthcare support.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>Same-day delivery available for urgent medicine needs. We're here when you need us most.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile App</h3>
                    <p>Download our app for easy prescription refills, medication reminders, and health tracking.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-us">
        <div class="container-section">
            <h2 class="section-title">Why Choose PharmaCare?</h2>
            <p class="section-subtitle">Your Health is Our Priority</p>
            
            <div class="why-grid">
                <div class="why-item">
                    <div class="why-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="why-text">
                        <h4>Licensed & Certified</h4>
                        <p>All our pharmacists are licensed professionals with years of experience.</p>
                    </div>
                </div>

                <div class="why-item">
                    <div class="why-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="why-text">
                        <h4>Quality Assured</h4>
                        <p>We only stock genuine medicines from trusted manufacturers.</p>
                    </div>
                </div>

                <div class="why-item">
                    <div class="why-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="why-text">
                        <h4>Affordable Prices</h4>
                        <p>Competitive pricing with regular discounts and loyalty rewards.</p>
                    </div>
                </div>

                <div class="why-item">
                    <div class="why-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="why-text">
                        <h4>Privacy & Security</h4>
                        <p>Your health information is protected with highest security standards.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container-section">
            <h2>Ready to Start Your Health Journey?</h2>
            <p>Join thousands of satisfied customers who trust PharmaCare for their healthcare needs</p>
            <a href="medicine.php" class="cta-btn">Browse Medicines</a>
        </div>
    
    </section>
<?php include 'footer.php'; ?>
<script src="../jsUser/script.js"></script>
</body>
</html>
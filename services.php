<?php
// services.php
include 'includes/header.php';
?>
<div class="services-container">
    <div class="page-header">
        <h1>Our Premium Services</h1>
        <p>At The Gentlemen's Bar, we offer a range of premium grooming services including:</p>
    </div>
    <div class="services-grid">
        <div class="service-card">
            <img src="Assets/haircut.jpeg" alt="Classic Haircut" class="service-image">
            <div class="service-content">
                <h3>Classic Haircut</h3>
                <p class="service-desc"> Professional haircut tailored to your style and preferences. </p>
                <div class="service-meta">
                    <span>45 Minutes</span>
                    <span>R50.00</span>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="booking_appointment.php" class="btn-book">Book Now</a>
                <?php else: ?> <a href="login.php" class="btn-book">Login to Book</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="service-card">
            <img src="Assets/Beard Trim (1).jpeg" alt="Beard Grooming" class="service-image">
            <div class="service-content">
                <h3>Beard Grooming</h3>
                <p class="service-desc"> Precision trimming and shaping for a clean professional look. </p>
                <div class="service-meta">
                    <span>30 Minutes</span>
                    <span>R40.00</span>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="booking_appointment.php" class="btn-book">Book Now</a> <?php else: ?> <a href="login.php" class="btn-book">Login to Book</a> <?php endif; ?>
            </div>
        </div>
        <div class="service-card"> <img src="Assets/facial.jpeg" alt="Facial Treatment" class="service-image">
            <div class="service-content">
                <h3>Facial Treatment</h3>
                <p class="service-desc"> Deep cleansing treatment that refreshes and rejuvenates your skin. </p>
                <div class="service-meta"> <span>60 Minutes</span>
                <span>R75.00</span> </div> <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="booking_appointment.php" class="btn-book">Book Now</a>
                    <?php else: ?> <a href="login.php" class="btn-book">Login to Book</a>
                        <?php endif; ?>
            </div>
        </div>
        <div class="service-card">
            <img src="Assets/hairwash.jpg" alt="Hair Wash" class="service-image">
            <div class="service-content">
                <h3>Hair Wash</h3>
                <p class="service-desc"> Professional hair cleansing treatment for a fresh finish. </p>
                <div class="service-meta"> <span>20 Minutes</span> <span>R30.00</span>
            </div> <?php if (isset($_SESSION['user_id'])): ?>
                <a href="booking_appointment.php" class="btn-book">Book Now</a>
                <?php else: ?> <a href="login.php" class="btn-book">Login to Book</a>
                    <?php endif; ?>
            </div>
        </div>
        <div class="service-card">
            <div class="service-content">
                <h3>Full Grooming Package</h3>
                <p class="service-desc"> Complete grooming experience including haircut, beard grooming and facial treatment. </p>
                <div class="service-meta">
                    <span>90 Minutes</span> <span>R120.00</span> </div> <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="booking_appointment.php" class="btn-book">Book Now</a>
                <?php else: ?>
                    <a href="login.php" class="btn-book">Login to Book</a>
                <?php endif; ?>
            </div>
        </div>
        </div>
        <div>  <?php include 'includes/footer.php'; ?></div>
      
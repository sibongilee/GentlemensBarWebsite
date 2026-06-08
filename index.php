<?php
include 'includes/header.php';
?>
<div class="hero">
    <img src="assets/logo.jpeg" alt="The Gentlemen's Bar Logo" class="hero-logo">
    <h1>Premium Grooming Experience</h1>
    <p>Book appointments, manage your bookings and enjoy professional grooming services in South Africa.</p>
    <div class="hero-buttons">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="card-box">Dashboard</a>
            <a href="booking_appointment.php" class="card-box">Book Appointment</a>
        <?php else: ?>
            <a href="login.php" class="card-box">Login</a>
            <a href="register.php" class="card-box">Register</a>
            <a href="booking_appointment.php" class="card-box">Book Appointment</a>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>

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
        <?php foreach ($services as $service): ?>
            <div class="service-card">
                <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                <p class="service-desc"><?php echo htmlspecialchars($service['description']); ?></p>
                <div class="service-meta">
                    <span class="service-duration"><?php echo $service['duration']; ?> min</span>
                    <span class="service-price">R <?php echo number_format($service['price'], 2); ?></span>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="booking_appointment.php?service_id=<?php echo $service['id']; ?>" class="btn-book">Book Now</a>
                <?php else: ?>
                    <a href="login.php" class="btn-book">Login to Book</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
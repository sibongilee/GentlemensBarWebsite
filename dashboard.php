<?php
// dashboard.php
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Calculate upcoming appointments and total spent
$upcoming = 0;
$total_spent = 0;

foreach ($_SESSION['bookings'] as $booking) {
    if ($booking['status'] != 'cancelled' && strtotime($booking['date']) >= strtotime(date('Y-m-d'))) {
        $upcoming++;
    }
}

foreach ($_SESSION['payments'] as $payment) {
    if ($payment['status'] == 'completed') {
        $total_spent += $payment['amount'];
    }
}
?>
<div class="dashboard">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p class="tagline">Manage your appointments, view services, and handle payments all in one place.</p>
    
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-number"><?php echo $upcoming; ?></div>
            <div class="stat-label">Upcoming Appointments</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">R<?php echo number_format($total_spent, 2); ?></div>
            <div class="stat-label">Total Spent</div>
        </div>
    </div>
</div>

<div class="cards">
    <a href="booking_appointment.php" class="card-box">
        <h3>Book Appointment</h3>
        <p>Schedule a new appointment.</p>
    </a>
    <a href="bookings.php" class="card-box">
        <h3>My Bookings</h3>
        <p>View upcoming appointments.</p>
    </a>
    <a href="services.php" class="card-box">
        <h3>Services</h3>
        <p>Browse available services.</p>
    </a>
    <a href="payment.php" class="card-box">
        <h3>Payments</h3>
        <p>Manage payment options.</p>
    </a>
</div>
<?php include 'includes/footer.php'; ?>

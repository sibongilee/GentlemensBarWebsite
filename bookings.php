<?php
// bookings.php
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle cancellation
if (isset($_POST['cancel_booking']) && isset($_POST['booking_id'])) {
    $booking_id = (int)$_POST['booking_id'];
    foreach ($_SESSION['bookings'] as &$booking) {
        if ($booking['id'] == $booking_id && ($booking['status'] == 'pending' || $booking['status'] == 'confirmed')) {
            $booking['status'] = 'cancelled';
            break;
        }
    }
}

// Get user's bookings (all from session)
$user_bookings = array_reverse($_SESSION['bookings']);
?>
<div class="bookings-container">
    <div class="page-header">
        <img src="assets/logo.jpeg" alt="The Gentlemen's Bar Logo" class="hero-logo">
        <h1>My Bookings</h1>
        <p>Here you can view and manage your upcoming appointments.</p>
    </div>
    
    <?php if (empty($user_bookings)): ?>
        <div class="empty-state">
            <p>You have no appointments yet.</p>
            <a href="booking_appointment.php" class="btn-primary">Book Your First Appointment</a>
        </div>
    <?php else: ?>
        <div class="bookings-list">
            <?php foreach ($user_bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-status <?php echo $booking['status']; ?>">
                        <?php echo ucfirst($booking['status']); ?>
                    </div>
                    <div class="booking-details">
                        <h3><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                        <p class="booking-datetime">
                            <strong>Date:</strong> <?php echo date('F j, Y', strtotime($booking['date'])); ?><br>
                            <strong>Time:</strong> <?php echo date('g:i A', strtotime($booking['time'])); ?>
                        </p>
                        <p class="booking-duration"><strong>Duration:</strong> <?php echo $booking['duration']; ?> minutes</p>
                        <p class="booking-price"><strong>Price:</strong> R<?php echo number_format($booking['price'], 2); ?></p>
                        <p class="booking-payment">
                            <strong>Payment:</strong> 
                            <span class="payment-status <?php echo $booking['payment_status']; ?>">
                                <?php echo ucfirst($booking['payment_status']); ?>
                            </span>
                        </p>
                    </div>
                    <div class="booking-actions">
                        <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" name="cancel_booking" class="btn-cancel">Cancel</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($booking['payment_status'] == 'pending' && $booking['status'] != 'cancelled'): ?>
                            <a href="payment.php" class="btn-pay">Pay Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
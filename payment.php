<?php
// payment.php
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success = isset($_SESSION['payment_success']) ? $_SESSION['payment_success'] : '';
$error = isset($_SESSION['payment_error']) ? $_SESSION['payment_error'] : '';
unset($_SESSION['payment_success']);
unset($_SESSION['payment_error']);

// Get pending payments
$pending_payments = [];
foreach ($_SESSION['payments'] as $payment) {
    if ($payment['status'] == 'pending') {
        // Find corresponding booking
        foreach ($_SESSION['bookings'] as $booking) {
            if ($booking['payment_id'] == $payment['id']) {
                $pending_payments[] = [
                    'payment_id' => $payment['id'],
                    'booking_id' => $booking['id'],
                    'service_name' => $booking['service_name'],
                    'appointment_date' => $booking['date'],
                    'appointment_time' => $booking['time'],
                    'amount' => $payment['amount']
                ];
                break;
            }
        }
    }
}
?>
<div class="payment-container">
    <div class="page-header">
        <img src="assets/logo.jpeg" alt="The Gentlemen's Bar Logo" class="hero-logo">
        <h1>Payments</h1>
        <p>Complete your payment to confirm your appointment.</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <?php if (empty($pending_payments)): ?>
        <div class="empty-state">
            <p>You have no pending payments.</p>
            <a href="booking_appointment.php" class="btn-primary">Book an Appointment</a>
        </div>
    <?php else: ?>
        <div class="payments-list">
            <h2>Pending Payments</h2>
            <?php foreach ($pending_payments as $payment): ?>
                <div class="payment-card">
                    <div class="payment-details">
                        <h3><?php echo htmlspecialchars($payment['service_name']); ?></h3>
                        <p><strong>Appointment:</strong> <?php echo date('F j, Y', strtotime($payment['appointment_date'])); ?> at <?php echo date('g:i A', strtotime($payment['appointment_time'])); ?></p>
                        <p><strong>Amount Due:</strong> $<?php echo number_format($payment['amount'], 2); ?></p>
                    </div>
                    <div class="payment-actions">
                        <form method="POST" action="process_payment.php">
                            <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">
                            <select name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash (Pay at counter)</option>
                                <option value="card">Credit/Debit Card</option>
                                <option value="mobile_money">Mobile Money</option>
                            </select>
                            <button type="submit" name="process_payment" class="btn-primary">Pay Now</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
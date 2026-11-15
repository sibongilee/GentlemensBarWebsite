<?php
// payment.php
session_start();
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

if (isset($_SESSION['payments']) && !empty($_SESSION['payments'])) {
    foreach ($_SESSION['payments'] as $payment) {
        if (isset($payment['status']) && $payment['status'] == 'pending') {
            if (isset($_SESSION['bookings']) && !empty($_SESSION['bookings'])) {
                foreach ($_SESSION['bookings'] as $booking) {
                    if (isset($booking['payment_id']) && $booking['payment_id'] == $payment['id']) {
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
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments | The Gentlemen's Bar</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Include Paystack Inline JS SDK -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Payments</h1>
            <p class="tagline">Complete your payment to confirm your appointment</p>
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
                <a href="booking_appointment.php" class="btn-primary" style="display: inline-block; width: auto; margin-top: 15px;">Book an Appointment</a>
            </div>
        <?php else: ?>
            <div class="payments-list">
                <h2>Pending Payments (<?php echo count($pending_payments); ?>)</h2>
                <?php foreach ($pending_payments as $index => $payment): ?>
                    <div class="payment-card" id="payment-card-<?php echo $index; ?>">
                        <div class="payment-details">
                            <h3><?php echo htmlspecialchars($payment['service_name']); ?></h3>
                            <p><strong>Appointment:</strong> <?php echo date('F j, Y', strtotime($payment['appointment_date'])); ?> at <?php echo date('g:i A', strtotime($payment['appointment_time'])); ?></p>
                            <p><strong>Amount Due:</strong> <span style="color: #C4A77D; font-size: 1.2rem;">R <?php echo number_format($payment['amount'], 2); ?></span></p>
                        </div>
                        <div class="payment-actions">
                            <form method="POST" action="process_payment.php" id="payment-form-<?php echo $index; ?>" onsubmit="return handlePaymentSubmission(<?php echo $index; ?>, event)">
                                <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">
                                <input type="hidden" name="amount" value="<?php echo $payment['amount']; ?>">
                                <input type="hidden" name="paystack_reference" id="paystack-ref-<?php echo $index; ?>" value="">
                                
                                <select name="payment_method" id="payment-method-<?php echo $index; ?>" class="payment-method-select" data-index="<?php echo $index; ?>" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash (Pay at counter)</option>
                                    <option value="card">Credit / Debit Card (Paystack)</option>
                                </select>
                                
                                <button type="submit" name="process_payment" class="btn-primary" style="margin-top: 15px;">Pay Now</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function handlePaymentSubmission(index, event) {
        const paymentMethod = document.getElementById(`payment-method-${index}`).value;
        const amountZAR = parseFloat(document.querySelector(`#payment-form-${index} input[name="amount"]`).value);
        const userEmail = "<?php echo $_SESSION['user_email'] ?? 'customer@thegentlemensbar.co.za'; ?>";
        const refInput = document.getElementById(`paystack-ref-${index}`);

        // If paying with Card, trigger Paystack popup checkout
        if (paymentMethod === 'card') {
            event.preventDefault();

            let handler = PaystackPop.setup({
                key: 'pk_test_9fc4431541dfd97e2e1e194e9bb111d32012561b', // Test Public Key[cite: 4]
                email: userEmail,
                amount: Math.round(amountZAR * 100), // Paystack expects amount in cents
                currency: 'ZAR',
                ref: 'GENT_' + Math.floor((Math.random() * 1000000000) + 1),
                callback: function(response) {
                    refInput.value = response.reference;
                    document.getElementById(`payment-form-${index}`).submit();
                },
                onClose: function() {
                    alert('Transaction cancelled.');
                }
            });
            handler.openIframe();
            return false;
        }

        return true;
    }
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>
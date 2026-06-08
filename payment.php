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
                            <form method="POST" action="process_payment.php" id="payment-form-<?php echo $index; ?>" onsubmit="return validatePaymentForm(<?php echo $index; ?>, event)">
                                <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">
                                <input type="hidden" name="amount" value="<?php echo $payment['amount']; ?>">
                                
                                <select name="payment_method" id="payment-method-<?php echo $index; ?>" class="payment-method-select" data-index="<?php echo $index; ?>" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash (Pay at counter)</option>
                                    <option value="card">Credit / Debit Card</option>
                                </select>
                                
                                <!-- Card Details Form - Hidden by default -->
                                <div id="card-details-<?php echo $index; ?>" class="card-details-form">
                                    <div class="form-group">
                                        <label>Cardholder Name</label>
                                        <input type="text" name="card_name" placeholder="John Smith" autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <label>Card Number</label>
                                        <input type="text" name="card_number" placeholder="4242 4242 4242 4242" maxlength="19" autocomplete="off">
                                    </div>
                                    <div class="card-row">
                                        <div class="form-group">
                                            <label>Expiry Month</label>
                                            <input type="text" name="card_expiry_month" placeholder="MM" maxlength="2" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <label>Expiry Year</label>
                                            <input type="text" name="card_expiry_year" placeholder="YY" maxlength="2" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <label>CVV</label>
                                            <input type="password" name="card_cvv" placeholder="123" maxlength="4" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Email for Receipt</label>
                                        <input type="email" name="receipt_email" placeholder="your@email.com" autocomplete="off">
                                    </div>
                                </div>
                                
                                <button type="submit" name="process_payment" class="btn-primary">Pay Now</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Get all payment method selects
        const paymentSelects = document.querySelectorAll('.payment-method-select');
        
        // Add event listener to each select
        paymentSelects.forEach(select => {
            select.addEventListener('change', function() {
                const index = this.getAttribute('data-index');
                const cardDetailsDiv = document.getElementById(`card-details-${index}`);
                
                if (this.value === 'card') {
                    cardDetailsDiv.classList.add('active');
                    // Make card fields required
                    const inputs = cardDetailsDiv.querySelectorAll('input');
                    inputs.forEach(input => {
                        input.setAttribute('required', 'required');
                    });
                } else {
                    cardDetailsDiv.classList.remove('active');
                    // Remove required attribute from card fields
                    const inputs = cardDetailsDiv.querySelectorAll('input');
                    inputs.forEach(input => {
                        input.removeAttribute('required');
                        input.value = '';
                    });
                }
            });
        });
        
        // Validate card details before submission
        function validatePaymentForm(index, event) {
            const form = document.getElementById(`payment-form-${index}`);
            const paymentMethod = document.getElementById(`payment-method-${index}`).value;
            
            if (paymentMethod === 'card') {
                const cardName = form.querySelector('input[name="card_name"]').value.trim();
                const cardNumber = form.querySelector('input[name="card_number"]').value.trim();
                const expiryMonth = form.querySelector('input[name="card_expiry_month"]').value.trim();
                const expiryYear = form.querySelector('input[name="card_expiry_year"]').value.trim();
                const cvv = form.querySelector('input[name="card_cvv"]').value.trim();
                
                // Basic validation
                if (!cardName) {
                    alert('Please enter the cardholder name.');
                    event.preventDefault();
                    return false;
                }
                
                if (!cardNumber || cardNumber.replace(/\s/g, '').length < 15) {
                    alert('Please enter a valid card number.');
                    event.preventDefault();
                    return false;
                }
                
                if (!expiryMonth || expiryMonth < 1 || expiryMonth > 12) {
                    alert('Please enter a valid expiry month (01-12).');
                    event.preventDefault();
                    return false;
                }
                
                if (!expiryYear || expiryYear.length !== 2) {
                    alert('Please enter a valid expiry year (YY).');
                    event.preventDefault();
                    return false;
                }
                
                if (!cvv || cvv.length < 3) {
                    alert('Please enter a valid CVV.');
                    event.preventDefault();
                    return false;
                }
                
                // Optional: Format card number for display
                const rawNumber = cardNumber.replace(/\s/g, '');
                if (rawNumber.length === 16) {
                    const masked = '**** **** **** ' + rawNumber.slice(-4);
                    alert(`Payment of R ${form.querySelector('input[name="amount"]').value} will be processed.\nCard ending in ${rawNumber.slice(-4)}`);
                }
            }
            
            return true;
        }
        
        // Format card number as user types
        document.addEventListener('DOMContentLoaded', function() {
            const cardNumberInputs = document.querySelectorAll('input[name="card_number"]');
            cardNumberInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = this.value.replace(/\s/g, '');
                    if (value.length > 16) value = value.slice(0, 16);
                    if (value.length > 4) {
                        value = value.match(/.{1,4}/g).join(' ');
                    }
                    this.value = value;
                });
            });
            
            // Month validation
            const monthInputs = document.querySelectorAll('input[name="card_expiry_month"]');
            monthInputs.forEach(input => {
                input.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value > 12) value = '12';
                    if (value < 1 && value.length === 2) value = '01';
                    this.value = value;
                });
            });
            
            // Year validation
            const yearInputs = document.querySelectorAll('input[name="card_expiry_year"]');
            yearInputs.forEach(input => {
                input.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.length > 2) value = value.slice(0, 2);
                    this.value = value;
                });
            });
            
            // CVV validation
            const cvvInputs = document.querySelectorAll('input[name="card_cvv"]');
            cvvInputs.forEach(input => {
                input.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.length > 4) value = value.slice(0, 4);
                    this.value = value;
                });
            });
        });
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>

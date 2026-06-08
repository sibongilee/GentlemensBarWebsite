<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_payment'])) {
    $payment_id = $_POST['payment_id'];
    $payment_method = $_POST['payment_method'];
    
    // Find the payment in session
    if (isset($_SESSION['payments'])) {
        foreach ($_SESSION['payments'] as &$payment) {
            if ($payment['id'] == $payment_id) {
                
                // If card payment, validate card details
                if ($payment_method == 'card') {
                    $card_name = isset($_POST['card_name']) ? trim($_POST['card_name']) : '';
                    $card_number = isset($_POST['card_number']) ? preg_replace('/\s/', '', $_POST['card_number']) : '';
                    $expiry_month = isset($_POST['card_expiry_month']) ? $_POST['card_expiry_month'] : '';
                    $expiry_year = isset($_POST['card_expiry_year']) ? $_POST['card_expiry_year'] : '';
                    $cvv = isset($_POST['card_cvv']) ? $_POST['card_cvv'] : '';
                    $receipt_email = isset($_POST['receipt_email']) ? $_POST['receipt_email'] : '';
                    
                    // Validate card details
                    $errors = [];
                    
                    if (empty($card_name)) {
                        $errors[] = "Cardholder name is required.";
                    }
                    
                    if (empty($card_number) || strlen($card_number) < 15) {
                        $errors[] = "Valid card number is required.";
                    }
                    
                    if (empty($expiry_month) || $expiry_month < 1 || $expiry_month > 12) {
                        $errors[] = "Valid expiry month is required.";
                    }
                    
                    if (empty($expiry_year) || strlen($expiry_year) != 2) {
                        $errors[] = "Valid expiry year is required.";
                    }
                    
                    if (empty($cvv) || strlen($cvv) < 3) {
                        $errors[] = "Valid CVV is required.";
                    }
                    
                    if (!empty($errors)) {
                        $_SESSION['payment_error'] = implode(" ", $errors);
                        header("Location: payment.php");
                        exit();
                    }
                    
                    // Process card payment 
                    $transaction_id = 'TXN' . time() . rand(1000, 9999);
                    $masked_card = '**** **** **** ' . substr($card_number, -4);
                    
                    // Store card payment record
                    $card_payment_record = [
                        'transaction_id' => $transaction_id,
                        'cardholder_name' => $card_name,
                        'masked_card' => $masked_card,
                        'receipt_email' => $receipt_email,
                        'payment_date' => date('Y-m-d H:i:s')
                    ];
                    
                    $_SESSION['last_card_payment'] = $card_payment_record;
                    
                    $_SESSION['payment_success'] = "Payment of R " . number_format($payment['amount'], 2) . " processed successfully via " . strtoupper($payment_method) . ".\nTransaction ID: " . $transaction_id . "\nCard: " . $masked_card;
                    
                    // Send receipt email (mock)
                    if (!empty($receipt_email)) {
                        // In production, send actual email here
                        $_SESSION['payment_success'] .= "\nReceipt sent to " . $receipt_email;
                    }
                    
                } else {
                    // Cash payment - no card details needed
                    $_SESSION['payment_success'] = "Payment of R " . number_format($payment['amount'], 2) . " confirmed via " . strtoupper($payment_method) . ". Please pay at the counter.";
                }
                
                // Update payment status
                $payment['status'] = 'completed';
                $payment['payment_method'] = $payment_method;
                $payment['payment_date'] = date('Y-m-d H:i:s');
                
                // Update booking status
                if (isset($_SESSION['bookings'])) {
                    foreach ($_SESSION['bookings'] as &$booking) {
                        if (isset($booking['payment_id']) && $booking['payment_id'] == $payment_id) {
                            $booking['payment_status'] = 'paid';
                            $booking['booking_status'] = 'confirmed';
                            break;
                        }
                    }
                }
                
                break;
            }
        }
    }
    
    header("Location: payment.php");
    exit();
} else {
    header("Location: payment.php");
    exit();
}
?>

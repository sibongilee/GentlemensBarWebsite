<?php
// process_payment.php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_payment'])) {
    $payment_id = $_POST['payment_id'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    
    // Find the payment in session
    if (isset($_SESSION['payments'])) {
        foreach ($_SESSION['payments'] as &$payment) {
            if ($payment['id'] == $payment_id) {
                
                // If card payment, verify via Paystack API
                if ($payment_method == 'card') {
                    $paystack_reference = $_POST['paystack_reference'] ?? '';
                    
                    if (empty($paystack_reference)) {
                        $_SESSION['payment_error'] = "Transaction reference missing from Paystack.";
                        header("Location: payment.php");
                        exit();
                    }
                    
                    // Verify transaction securely on backend using Secret Key[cite: 4]
                    $secretKey = 'sk_test_10ff2480fb29dbbe39e5692dfafe1903b7012368'; 
                    $url = 'https://api.paystack.co/transaction/verify/' . rawurlencode($paystack_reference);
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer ' . $secretKey,
                        'Cache-Control: no-cache',
                    ]);
                    
                    $response = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);
                    
                    if ($err) {
                        $_SESSION['payment_error'] = "Payment verification connection error: " . $err;
                        header("Location: payment.php");
                        exit();
                    }
                    
                    $result = json_decode($response, true);
                    
                    if (!$result || !$result['status'] || $result['data']['status'] !== 'success') {
                        $_SESSION['payment_error'] = "Payment verification failed or was declined.";
                        header("Location: payment.php");
                        exit();
                    }
                    
                    $gateway_reference = $result['data']['reference'];
                    $amount_paid = $result['data']['amount'] / 100; // Convert cents back to ZAR
                    $customer_email = $result['data']['customer']['email'] ?? '';
                    
                    $_SESSION['payment_success'] = "Payment of R " . number_format($amount_paid, 2) . " processed successfully via Paystack Card.\nReference: " . $gateway_reference;
                    
                    if (!empty($customer_email)) {
                        $_SESSION['payment_success'] .= "\nReceipt confirmation linked to " . htmlspecialchars($customer_email);
                    }
                    
                } else {
                    // Cash payment
                    $_SESSION['payment_success'] = "Payment of R " . number_format($payment['amount'], 2) . " confirmed via " . strtoupper($payment_method) . ". Please pay at the counter.";
                }
                
                // Update payment status in session
                $payment['status'] = 'completed';
                $payment['payment_method'] = $payment_method;
                $payment['payment_date'] = date('Y-m-d H:i:s');
                
                // Update booking status in session
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
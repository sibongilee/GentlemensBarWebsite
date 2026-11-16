<?php
// api/process_checkout.php
session_start();

function verifyPaystackPayment($reference, $expectedAmount) {
    $secretKey = 'sk_test_10ff2480fb29dbbe39e5692dfafe1903b7012368';
    $url = 'https://api.paystack.co/transaction/verify/' . rawurlencode($reference);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $secretKey,
        'Cache-Control: no-cache',
    ]);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['ok' => false, 'message' => 'Payment verification connection error: ' . $err];
    }

    $result = json_decode($response, true);
    if (!$result || !$result['status'] || ($result['data']['status'] ?? '') !== 'success') {
        return ['ok' => false, 'message' => 'Payment verification failed or was declined.'];
    }

    $paidAmount = (float)($result['data']['amount'] ?? 0) / 100;
    if (abs($paidAmount - $expectedAmount) > 0.01) {
        return ['ok' => false, 'message' => 'Paid amount does not match the cart total.'];
    }

    return ['ok' => true, 'reference' => $result['data']['reference'] ?? $reference, 'amount' => $paidAmount];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pickup_notes = trim($_POST['address'] ?? '');
    $paystackReference = trim($_POST['paystack_reference'] ?? '');
    $submittedAmount = (float)($_POST['amount'] ?? 0);

    if (empty($_SESSION['cart'])) {
        header('Location: ../cart.php?error=empty');
        exit();
    }

    if (empty($paystackReference)) {
        header('Location: ../cart.php?error=payment_missing');
        exit();
    }

    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += (float)($item['price'] ?? 0);
    }

    if ($submittedAmount <= 0 || abs($submittedAmount - $total_amount) > 0.01) {
        header('Location: ../cart.php?error=amount_mismatch');
        exit();
    }

    $verification = verifyPaystackPayment($paystackReference, $total_amount);
    if (!$verification['ok']) {
        header('Location: ../cart.php?error=' . urlencode($verification['message']));
        exit();
    }

    if ($name === '') {
        $name = 'Customer';
    }

    if ($phone === '') {
        $phone = 'N/A';
    }

    $order_id = 'ORD' . time() . rand(100, 999);
    $cart_items = $_SESSION['cart'];

    $_SESSION['last_order'] = [
        'order_id' => $order_id,
        'name' => $name,
        'phone' => $phone,
        'pickup_notes' => $pickup_notes,
        'items' => $cart_items,
        'total' => $total_amount,
        'date' => date('Y-m-d H:i:s'),
        'paystack_reference' => $verification['reference'],
    ];

    // Clear cart immediately after successful Paystack payment.
    unset($_SESSION['cart']);
    $_SESSION['cart'] = [];

    header('Location: ../order_success.php');
    exit();
} else {
    header('Location: ../cart.php');
    exit();
}
?>
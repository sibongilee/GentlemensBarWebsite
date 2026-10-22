<?php
// api/process_checkout.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pickup_notes = trim($_POST['address'] ?? ''); // Re-used form field for pickup notes

    if (empty($name) || empty($phone) || empty($_SESSION['cart'])) {
        header("Location: ../checkout.php?error=empty");
        exit();
    }

    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'];
    }

    $order_id = 'ORD' . time() . rand(100, 999);

    $_SESSION['last_order'] = [
        'order_id' => $order_id,
        'name' => $name,
        'phone' => $phone,
        'pickup_notes' => $pickup_notes,
        'items' => $_SESSION['cart'],
        'total' => $total_amount,
        'date' => date('Y-m-d H:i:s')
    ];

    unset($_SESSION['cart']);

    header("Location: ../order_success.php");
    exit();
} else {
    header("Location: ../checkout.php");
    exit();
}
?>
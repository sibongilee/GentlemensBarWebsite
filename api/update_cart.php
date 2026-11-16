<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productId = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if ($productId !== null && $action !== null) {
    foreach ($_SESSION['cart'] as $index => &$item) {
        if (($item['id'] ?? null) == $productId) {
            $qty = (int)($item['qty'] ?? 1);

            if ($action === 'increase') {
                $qty += 1;
            } elseif ($action === 'decrease') {
                $qty -= 1;
            }

            if ($qty <= 0) {
                unset($_SESSION['cart'][$index]);
            } else {
                $item['qty'] = $qty;
            }

            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
    unset($item);
}

header('Location: ../cart.php');
exit();

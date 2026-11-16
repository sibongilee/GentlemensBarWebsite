<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $product_name = $_POST['product_name'] ?? '';
    $product_price = $_POST['product_price'] ?? '';
    $product_image = $_POST['product_image'] ?? '';

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if (($item['id'] ?? null) == $product_id) {
            $item['qty'] = (isset($item['qty']) ? (int)$item['qty'] : 1) + 1;
            $item['price'] = (float)$product_price;
            $item['name'] = $product_name;
            $item['image'] = $product_image;
            $found = true;
            break;
        }
    }
    unset($item);

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => (float)$product_price,
            'image' => $product_image,
            'qty' => 1,
        ];
    }

    header("Location: ../shop.php?added=" . urlencode($product_name));
    exit();
}
?>
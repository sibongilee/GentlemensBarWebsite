<?php
// cart.php
session_start();
include 'includes/header.php';
?>

<div class="shop-header-section">
    <div class="shop-header-content">
        <h1>Your Shopping Cart</h1>
        <p>Review your selected grooming products before checkout.</p>
    </div>
</div>

<div class="container" style="padding: 40px; max-width: 800px; margin: auto;">
    <?php if (!empty($_SESSION['cart'])): ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="border-bottom: 2px solid #ddd; text-align: left;">
                <th style="padding: 10px;">Image</th>
                <th style="padding: 10px;">Product</th>
                <th style="padding: 10px;">Price</th>
                <th style="padding: 10px;">Action</th>
            </tr>
            <?php 
            $total = 0;
            foreach ($_SESSION['cart'] as $index => $item): 
                $total += $item['price'];
                $img_src = !empty($item['image']) ? $item['image'] : 'Assets/barber_logo.png';
            ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 10px;">
                    <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                </td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($item['name']); ?></td>
                <td style="padding: 10px;">R <?php echo number_format($item['price'], 2); ?></td>
                <td style="padding: 10px;">
                    <!-- Remove button linked to the api script with the item index -->
                    <a href="api/remove_from_cart.php?index=<?php echo $index; ?>" style="color: #ff4d4d; text-decoration: none; font-weight: bold;">Remove</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <h3>Total: R <?php echo number_format($total, 2); ?></h3>
        
        <div style="margin-top: 20px;">
            <a href="shop.php" class="btn-book" style="margin-right: 10px; text-decoration: none; display: inline-block;">Continue Shopping</a>
            <a href="checkout.php" class="btn-primary" style="text-decoration: none; display: inline-block; padding: 10px 20px; background: #000; color: #fff;">Proceed to Checkout</a>
        </div>
    <?php else: ?>
        <p style="font-size: 18px; color: #666;">Your cart is currently empty.</p>
        <br>
        <a href="shop.php" class="btn-book" style="text-decoration: none; display: inline-block;">Browse the Shop</a>
    <?php endif; ?>
</div>

<br><br>
<?php include 'includes/footer.php'; ?>
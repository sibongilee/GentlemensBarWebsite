<?php
// cart.php
session_start();
include 'includes/header.php';
?>

<div class="shop-header-section">
    <div class="shop-header-content">
        <h1 align="center">Your Shopping Cart</h1>
        <p align="center">Review your selected grooming products before checkout.</p>
    </div>
</div>

    <div class="cart-card">
        <div class="cart-card-inner">
            <div class="cart-container">
                <?php if (!empty($_SESSION['cart'])): ?>
                    <table class="cart-table">
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $index => $item): 
                            $total += $item['price'];
                            $img_src = !empty($item['image']) ? $item['image'] : 'Assets/barber_logo.png';
                        ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Product" class="cart-item-image">
                            </td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>R <?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <!-- Remove button linked to the api script with the item index -->
                                <a href="api/remove_from_cart.php?index=<?php echo $index; ?>" class="cart-remove-link">Remove</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    
                    <h3 class="cart-total">Total: R <?php echo number_format($total, 2); ?></h3>
                    
                <div class="cart-actions">
                    <a href="shop.php" class="btn-book cart-btn">Continue Shopping</a>
                    <a href="login.php" class="btn-primary cart-btn-primary">Proceed to Checkout</a>
                </div>
           
                <?php else: ?>
                    <p class="cart-empty-message">Your cart is currently empty.</p>
                    <br>
                    <a href="shop.php" class="btn-book cart-btn">Browse the Shop</a>
                <?php endif; ?> 
            </div>
        </div>
    </div>

<br><br>
<?php include 'includes/footer.php'; ?>
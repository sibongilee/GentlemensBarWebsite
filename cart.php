<?php
// cart.php
session_start();
include 'includes/header.php';

$cart_total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_total += (float)($item['price'] ?? 0) * (int)($item['qty'] ?? 1);
    }
}
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
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $index => $item): 
                            $item_price = (float)($item['price'] ?? 0);
                            $qty = max(1, (int)($item['qty'] ?? 1));
                            $line_total = $item_price * $qty;
                            $total += $line_total;
                            $img_src = !empty($item['image']) ? $item['image'] : 'Assets/barber_logo.png';
                        ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Product" class="cart-item-image">
                            </td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>R <?php echo number_format($item_price, 2); ?></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px; justify-content:center;">
                                    <a href="api/update_cart.php?action=decrease&id=<?php echo urlencode($item['id']); ?>" class="cart-remove-link" title="Decrease quantity" style="padding:4px 8px;">-</a>
                                    <span><?php echo $qty; ?></span>
                                    <a href="api/update_cart.php?action=increase&id=<?php echo urlencode($item['id']); ?>" class="cart-remove-link" title="Increase quantity" style="padding:4px 8px;">+</a>
                                </div>
                            </td>
                            <td>R <?php echo number_format($line_total, 2); ?></td>
                            <td>
                                <a href="api/remove_from_cart.php?index=<?php echo $index; ?>" class="cart-remove-link">Remove</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    
                    <h3 class="cart-total">Total: R <?php echo number_format($total, 2); ?></h3>
                    
                <div class="cart-actions">
                    <a href="shop.php" class="btn-book cart-btn">Continue Shopping</a>
                    <form id="cart-checkout-form" method="POST" action="api/process_checkout.php" style="display: inline-block;">
                        <input type="hidden" name="amount" value="<?php echo number_format($total, 2, '.', ''); ?>">
                        <input type="hidden" name="paystack_reference" id="paystack_reference" value="">
                        <button type="button" id="checkout-paystack-btn" class="btn-primary cart-btn-primary" data-total="<?php echo number_format($total, 2, '.', ''); ?>">Proceed to Checkout</button>
                    </form>
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
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkoutButton = document.getElementById('checkout-paystack-btn');
    if (!checkoutButton) return;

    checkoutButton.addEventListener('click', function (event) {
        event.preventDefault();

        const total = parseFloat(checkoutButton.dataset.total || '0');
        const userEmail = "<?php echo htmlspecialchars($_SESSION['user_email'] ?? 'customer@thegentlemensbar.co.za'); ?>";

        if (!total || total <= 0) {
            alert('Your cart is empty.');
            return;
        }

        const handler = PaystackPop.setup({
            key: 'pk_test_9fc4431541dfd97e2e1e194e9bb111d32012561b',
            email: userEmail,
            amount: Math.round(total * 100),
            currency: 'ZAR',
            ref: 'GB_CART_' + Date.now() + '_' + Math.floor(Math.random() * 1000),
            callback: function(response) {
                document.getElementById('paystack_reference').value = response.reference;
                document.getElementById('cart-checkout-form').submit();
            },
            onClose: function() {
                alert('Payment cancelled.');
            }
        });

        handler.openIframe();
    });
});
</script>
<?php include 'includes/footer.php'; ?>
<?php
// order_success.php
session_start();
include 'includes/header.php';
$order = $_SESSION['last_order'] ?? null;
?>

<div class="shop-header-section">
    <div class="shop-header-content">
        <h1>Order Confirmed</h1>
        <p>Thank you for shopping with The Gentleman's Bar!</p>
    </div>
</div>

<div class="container" style="padding: 40px; max-width: 600px; margin: auto; text-align: center;">
    <?php if ($order): ?>
        <h2 style="color: #28a745;">Order Placed for In-Store Pickup! 🎉</h2>
        <p>Your Order Reference is: <strong><?php echo htmlspecialchars($order['order_id']); ?></strong></p>
        
        <div style="text-align: left; background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #ddd;">
            <h3>Pickup Details:</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><strong>Notes:</strong> <?php echo htmlspecialchars($order['pickup_notes']); ?></p>
            <p style="margin-top: 10px; color: #555; font-size: 14px;"><em>Please present your order reference number at the counter when collecting.</em></p>
            
            <h3 style="margin-top: 15px; border-top: 1px solid #ccc; padding-top: 10px;">Total Due In-Store: R <?php echo number_format($order['total'], 2); ?></h3>
        </div>

        <a href="shop.php" class="btn-primary" style="text-decoration: none; padding: 12px 25px; background: #000; color: #fff; border-radius: 4px; display: inline-block; font-weight: bold;">Back to Shop</a>

        <!-- Sync script integrated here -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof db !== "undefined") {
                    db.collection("orders").doc("<?php echo $order['order_id']; ?>").set({
                        orderId: "<?php echo $order['order_id']; ?>",
                        customerName: "<?php echo htmlspecialchars($order['name']); ?>",
                        phone: "<?php echo htmlspecialchars($order['phone']); ?>",
                        pickupNotes: "<?php echo htmlspecialchars($order['pickup_notes']); ?>",
                        totalAmount: <?php echo $order['total']; ?>,
                        status: "Confirmed (In-Store Pickup)",
                        createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                        source: "Website Shop"
                    })
                    .then(() => {
                        console.log("Shop order successfully synced!");
                    })
                  .catch((error) => {
    // This will print the exact error code and message to your F12 Console
    console.error("Firebase Error Code:", error.code);
    console.error("Firebase Error Message:", error.message);
});
                }
            });
        </script>

    <?php else: ?>
        <p style="font-size: 18px; color: #666;">No recent order found.</p>
        <br>
        <a href="shop.php" class="btn-book" style="text-decoration: none; display: inline-block;">Go to Shop</a>
    <?php endif; ?>
</div>

<br><br>
<?php include 'includes/footer.php'; ?>
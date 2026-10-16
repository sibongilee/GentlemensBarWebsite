<?php
// shop.php
session_start();
include 'includes/header.php';
?>

<!-- Success Prompt Alert after adding to cart -->
<?php if (isset($_GET['added'])): ?>
    <script>
        alert("Success! '<?php echo htmlspecialchars($_GET['added']); ?>' has been added to your cart.");
    </script>
<?php endif; ?>

<div class="shop-header-section">
    <div class="shop-header-content">
        <h1>The Gentleman's Shop</h1>
        <p>Browse our premium grooming products and professional barber tools.</p>
    </div>
</div>

<div class="services-container">
    <div class="services-grid">

        <!-- Product 1: Shaving Cream -->
        <div class="service-card">
            <form action="api/add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="1">
                <input type="hidden" name="product_name" value="Honeysuckle Shaving Cream">
                <input type="hidden" name="product_price" value="89.99">
                <input type="hidden" name="product_image" value="Assets/shaving_cream.png">
                
                <img src="Assets/shaving_cream.png" alt="Shaving Cream" class="service-image">
                <h3>Honeysuckle Shaving Cream</h3>
                <p class="service-desc">Rich formulation with propolis and honeybush for a smooth, close shave.</p>
                <div class="service-meta">
                    <span class="service-duration">⭐ 4.6 (In Stock)</span>
                    <span class="service-price">R 89.99</span>
                </div>
                <button type="submit" class="btn-book">Add to Cart</button>
            </form>
        </div>

        <!-- Product 2: Hair Wax -->
        <div class="service-card">
            <form action="api/add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="2">
                <input type="hidden" name="product_name" value="Red Dane Hair Wax">
                <input type="hidden" name="product_price" value="149.99">
                <input type="hidden" name="product_image" value="Assets/hair_wax.png">
                
                <img src="Assets/hair_wax.png" alt="Hair Wax" class="service-image">
                <h3>Red Dane Hair Wax</h3>
                <p class="service-desc">Strong hold styling wax for classic or modern cuts and textures.</p>
                <div class="service-meta">
                    <span class="service-duration">⭐ 4.8 (In Stock)</span>
                    <span class="service-price">R 149.99</span>
                </div>
                <button type="submit" class="btn-book">Add to Cart</button>
            </form>
        </div>

        <!-- Product 3: Facial Cleanser -->
        <div class="service-card">
            <form action="api/add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="3">
                <input type="hidden" name="product_name" value="Super Hydrating Cleanser">
                <input type="hidden" name="product_price" value="220.00">
                <input type="hidden" name="product_image" value="Assets/facial_cleanser.png">
                
                <img src="Assets/facial_cleanser.png" alt="Facial Cleanser" class="service-image">
                <h3>Super Hydrating Cleanser</h3>
                <p class="service-desc">Face and body cleansing cream enriched with natural oils and shea butter.</p>
                <div class="service-meta">
                    <span class="service-duration">⭐ 4.9 (In Stock)</span>
                    <span class="service-price">R 220.00</span>
                </div>
                <button type="submit" class="btn-book">Add to Cart</button>
            </form>
        </div>

        <!-- Product 4: Shampoo & Conditioner Set -->
        <div class="service-card">
            <form action="api/add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="4">
                <input type="hidden" name="product_name" value="Shampoo & Conditioner Set">
                <input type="hidden" name="product_price" value="160.00">
                <input type="hidden" name="product_image" value="Assets/conditioner.png">
                
                <img src="Assets/conditioner.png" alt="Shampoo and Conditioner" class="service-image">
                <h3>Shampoo & Conditioner Set</h3>
                <p class="service-desc">Revitalizing hair care essentials designed for deep cleaning and hydration.</p>
                <div class="service-meta">
                    <span class="service-duration">⭐ 4.7 (In Stock)</span>
                    <span class="service-price">R 160.00</span>
                </div>
                <button type="submit" class="btn-book">Add to Cart</button>
            </form>
        </div>

        <!-- Product 5: Professional Clippers -->
        <div class="service-card">
            <form action="api/add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="5">
                <input type="hidden" name="product_name" value="Professional Clippers">
                <input type="hidden" name="product_price" value="1299.99">
                <input type="hidden" name="product_image" value="Assets/clippers.png">
                
                <img src="Assets/clippers.png" alt="Professional Clippers" class="service-image">
                <h3>Professional Clippers</h3>
                <p class="service-desc">High-precision cutting tools for professional-grade styling and fades.</p>
                <div class="service-meta">
                    <span class="service-duration">⭐ 4.9 (In Stock)</span>
                    <span class="service-price">R 1,299.99</span>
                </div>
                <button type="submit" class="btn-book">Add to Cart</button>
            </form>
        </div>

        <!-- Product 6: Beard Oil Kit -->
        <div class="service-card">
            <form action="api/add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="6">
                <input type="hidden" name="product_name" value="Amber Beard Oil Kit">
                <input type="hidden" name="product_price" value="189.99">
                <input type="hidden" name="product_image" value="Assets/beard_oil.png">
                
                <img src="Assets/beard_oil.png" alt="Beard Oil" class="service-image">
                <h3>Amber Beard Oil Kit</h3>
                <p class="service-desc">Manly man amber formulation for a soft, conditioned, and healthy beard.</p>
                <div class="service-meta">
                    <span class="service-duration">⭐ 4.9 (In Stock)</span>
                    <span class="service-price">R 189.99</span>
                </div>
                <button type="submit" class="btn-book">Add to Cart</button>
            </form>
        </div>

        <!-- Product 7: Beard Balm -->
        <div class="service-card">
            <form action="api/add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="7">
                <input type="hidden" name="product_name" value="Daily Grind Beard Balm">
                <input type="hidden" name="product_price" value="155.00">
                <input type="hidden" name="product_image" value="Assets/beard_balm.png">
                
                <img src="Assets/beard_balm.png" alt="Beard Balm" class="service-image">
                <h3>Daily Grind Beard Balm</h3>
                <p class="service-desc">Handcrafted styling balm for holding unruly hairs in check all day.</p>
                <div class="service-meta">
                    <span class="service-duration">⭐ 4.8 (In Stock)</span>
                    <span class="service-price">R 155.00</span>
                </div>
                <button type="submit" class="btn-book">Add to Cart</button>
            </form>
        </div>

        <!-- Product 8: Barber Cape -->
        <div class="service-card">
            <form action="api/add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="8">
                <input type="hidden" name="product_name" value="Professional Barber Cape">
                <input type="hidden" name="product_price" value="199.99">
                <input type="hidden" name="product_image" value="Assets/barber_cape.png">
                
                <img src="Assets/barber_cape.png" alt="Barber Cape" class="service-image">
                <h3>Professional Barber Cape</h3>
                <p class="service-desc">Water-resistant, durable salon cape ensuring clean, professional styling.</p>
                <div class="service-meta">
                    <span class="service-duration">⭐ 4.7 (In Stock)</span>
                    <span class="service-price">R 199.99</span>
                </div>
                <button type="submit" class="btn-book">Add to Cart</button>
            </form>
        </div>

    </div>
</div><br><br>

<?php include 'includes/footer.php'; ?>
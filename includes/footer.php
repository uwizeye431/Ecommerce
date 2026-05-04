<?php
require_once __DIR__ . '/config.php';
?>
<script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
</main>
<footer style="background:#020617;padding:24px 16px;margin-top:40px;border-top:1px solid #1f2937;">
    <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;align-items:flex-start;">
        <div>
            <h3>About ShopEasy</h3>
            <p class="muted">
                We connect small businesses with customers across Rwanda, making online shopping easy,
                secure, and accessible for everyone. Supporting local businesses since 2025.
            </p>
        </div>
        <div>
            <h3>Quick Links</h3>
            <p><a href="<?php echo APP_URL; ?>/index.php">Home</a></p>
            <p><a href="<?php echo APP_URL; ?>/products.php">Products</a></p>
            <p><a href="<?php echo APP_URL; ?>/about.php">About Us</a></p>
            <p><a href="<?php echo APP_URL; ?>/contact.php">Contact</a></p>
            <p><a href="<?php echo APP_URL; ?>/login.php">Login / Register</a></p>
        </div>
        <div>
            <h3>Contact</h3>
            <p>Email: <a href="mailto:support@platform.com">support@platform.com</a></p>
            <p>Phone: <a href="tel:+250788775937">+250788775937</a></p>
            <p>Address: RWANDA / GASABO</p>
        </div>
        <div style="grid-column:1 / -1;text-align:center;margin-top:12px;">
            <p class="muted">"Where businesses grow and customers shop with confidence."</p>
        </div>
    </div>
</footer>
</body>
</html>

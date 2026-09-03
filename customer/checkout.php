<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$items = get_cart_items(current_user_id());
$message = '';
$error = '';
if (is_post()) {
    $shipping = sanitize($_POST['shipping'] ?? '');
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'card');
    if (!$items) {
        $error = 'Cart is empty.';
    } elseif (!$shipping) {
        $error = 'Shipping address required.';
    } else {
        try {
            $orderId = create_order(current_user_id(), $shipping, $items);
            // Simulate payment
            $pdo = getPDO();
            $status = ($paymentMethod === 'cod') ? 'Pending COD' : 'Paid';
            $pdo->prepare('INSERT INTO Payments (OrderID, Amount, PaymentMethod, Status, TransactionID) VALUES (?, ?, ?, ?, ?)')
                ->execute([$orderId, array_sum(array_map(fn($i) => $i['Price'] * $i['Quantity'], $items)), $paymentMethod, $status, uniqid('TXN')]);
            clear_cart(current_user_id());
            $message = "Order #$orderId placed successfully.";
        } catch (Exception $e) {
            $error = 'Checkout failed: ' . $e->getMessage();
        }
    }
}
?>
<div class="card" style="max-width:640px;margin:auto;">
    <h2>Checkout</h2>
    <?php if ($message): ?><div class="alert success"><?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?php echo $error; ?></div><?php endif; ?>
    <form method="post">
        <label>Shipping address</label>
        <textarea name="shipping" required rows="3" placeholder="Street, City, Country"><?php echo $_POST['shipping'] ?? ''; ?></textarea>
        <label>Payment method</label>
        <select name="payment_method">
            <option value="cod" selected>Cash on Delivery (Pay upon delivery)</option>
            <option value="card">Credit Card (simulated)</option>
            <option value="paypal">PayPal (simulated)</option>
        </select>
        <button class="btn primary" type="submit" style="margin-top:12px;">Place order</button>
    </form>
</div>
</main>
</body>
</html>


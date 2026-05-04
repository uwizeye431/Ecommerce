<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$pdo = getPDO();
$message = '';
if (is_post()) {
    $paymentId = (int)$_POST['payment_id'];
    $newStatus = isset($_POST['mark_paid']) ? 'Paid' : 'Failed';
    $pdo->prepare('UPDATE Payments SET Status=? WHERE PaymentID=?')->execute([$newStatus, $paymentId]);
    $message = "Payment marked as $newStatus.";
}

$payments = $pdo->query('SELECT Payments.*, Orders.CustomerID, Users.Username 
    FROM Payments 
    JOIN Orders ON Payments.OrderID = Orders.OrderID 
    JOIN Users ON Orders.CustomerID = Users.UserID
    ORDER BY PaymentID DESC')->fetchAll();
?>
<div class="card">
    <h2>Manage payments</h2>
    <?php render_alerts($message); ?>
    <table>
        <thead><tr><th>ID</th><th>Order</th><th>Customer</th><th>Method</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $p): ?>
            <tr>
                <td><?php echo $p['PaymentID']; ?></td>
                <td>#<?php echo $p['OrderID']; ?></td>
                <td><?php echo sanitize($p['Username']); ?></td>
                <td><?php echo sanitize($p['PaymentMethod']); ?></td>
                <td><?php echo format_price((float)$p['Amount']); ?></td>
                <td><?php echo sanitize($p['Status']); ?></td>
                <td>
                    <form method="post" class="flex" style="gap:6px;">
                        <input type="hidden" name="payment_id" value="<?php echo $p['PaymentID']; ?>">
                        <button class="btn secondary" type="submit" name="mark_paid">Mark Paid</button>
                        <button class="btn danger" type="submit" name="mark_failed">Mark Failed</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>



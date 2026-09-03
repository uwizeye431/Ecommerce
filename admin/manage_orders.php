<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$pdo = getPDO();
$message = '';
if (is_post()) {
    $orderId = (int)$_POST['order_id'];
    if (isset($_POST['delete'])) {
        $pdo->prepare('DELETE FROM OrderItems WHERE OrderID=?')->execute([$orderId]);
        $pdo->prepare('DELETE FROM Payments WHERE OrderID=?')->execute([$orderId]);
        $pdo->prepare('DELETE FROM Orders WHERE OrderID=?')->execute([$orderId]);
        $message = "Order #$orderId deleted.";
    } else {
        $status = sanitize($_POST['status'] ?? 'Processing');
        $pdo->prepare('UPDATE Orders SET Status=? WHERE OrderID=?')->execute([$status, $orderId]);
        $message = "Order #$orderId updated.";
    }
}
$orders = $pdo->query('SELECT Orders.*, Users.Username FROM Orders JOIN Users ON Orders.CustomerID = Users.UserID ORDER BY OrderDate DESC')->fetchAll();
?>
<div class="card">
    <h2>Manage orders</h2>
    <?php if ($message): ?><div class="alert success"><?php echo $message; ?></div><?php endif; ?>
    <table>
        <thead><tr><th>ID</th><th>Customer</th><th>Status</th><th>Total</th><th>Date</th><th>Update</th><th>Details</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo $o['OrderID']; ?></td>
                <td><?php echo sanitize($o['Username']); ?></td>
                <td><?php echo sanitize($o['Status']); ?></td>
                <td><?php echo format_price((float)$o['TotalPrice']); ?></td>
                <td><?php echo $o['OrderDate']; ?></td>
                <td>
                    <form method="post" class="flex">
                        <input type="hidden" name="order_id" value="<?php echo $o['OrderID']; ?>">
                        <select name="status">
                            <option <?php if ($o['Status']==='Pending Payment') echo 'selected'; ?>>Pending Payment</option>
                            <option <?php if ($o['Status']==='Processing') echo 'selected'; ?>>Processing</option>
                            <option <?php if ($o['Status']==='Shipped') echo 'selected'; ?>>Shipped</option>
                            <option <?php if ($o['Status']==='Delivered') echo 'selected'; ?>>Delivered</option>
                            <option <?php if ($o['Status']==='Cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                        <button class="btn primary" type="submit" name="save">Save</button>
                        <button class="btn danger" type="submit" name="delete" onclick="return confirm('Delete this order?')">Delete</button>
                    </form>
                </td>
                <td><a class="btn secondary" href="order_details.php?id=<?php echo $o['OrderID']; ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</main>
</body>
</html>


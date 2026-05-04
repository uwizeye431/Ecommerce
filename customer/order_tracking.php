<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$orders = get_orders_by_customer(current_user_id());
?>
<div class="card">
    <h2>Orders</h2>
    <table>
        <thead><tr><th>ID</th><th>Status</th><th>Total</th><th>Date</th><th>Items</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo $o['OrderID']; ?></td>
                <td><?php echo sanitize($o['Status']); ?></td>
                <td><?php echo format_price((float)$o['TotalPrice']); ?></td>
                <td><?php echo $o['OrderDate']; ?></td>
                <td>
                    <?php foreach (get_order_items($o['OrderID']) as $i): ?>
                        <div><?php echo sanitize($i['Name']); ?> x<?php echo (int)$i['Quantity']; ?></div>
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


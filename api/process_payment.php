<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('customer');

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$orderId = (int)($data['order_id'] ?? 0);
$method = sanitize($data['method'] ?? 'card');
$pdo = getPDO();

$order = $pdo->prepare('SELECT * FROM Orders WHERE OrderID = ? AND CustomerID = ?');
$order->execute([$orderId, current_user_id()]);
$orderData = $order->fetch();
if (!$orderData) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

$success = $method === 'cod' ? true : (rand(0, 100) > 10);
$status = $success ? 'Paid' : 'Failed';
$pdo->prepare('INSERT INTO Payments (OrderID, Amount, PaymentMethod, Status, TransactionID) VALUES (?, ?, ?, ?, ?)')
    ->execute([$orderId, $orderData['TotalPrice'], $method, $status, uniqid('TXN')]);
if ($success) {
    $pdo->prepare('UPDATE Orders SET Status="Processing" WHERE OrderID=?')->execute([$orderId]);
}
echo json_encode(['success' => $success, 'status' => $status]);


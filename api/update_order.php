<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$orderId = (int)($data['order_id'] ?? 0);
$status = sanitize($data['status'] ?? 'Processing');
if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order']);
    exit;
}
$pdo = getPDO();
$pdo->prepare('UPDATE Orders SET Status=? WHERE OrderID=?')->execute([$status, $orderId]);
echo json_encode(['success' => true, 'message' => 'Order updated']);


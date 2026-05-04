<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/config.php';

// If not logged in, tell frontend to redirect to login.
if (!current_user_id()) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to add items to your cart.',
        'login_url' => APP_URL . '/login.php'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$productId = (int)($data['product_id'] ?? 0);
$qty = max(1, (int)($data['quantity'] ?? 1));

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

add_to_cart_db(current_user_id(), $productId, $qty);
echo json_encode(['success' => true, 'message' => 'Added to cart']);


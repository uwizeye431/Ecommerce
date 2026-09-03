<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $data['action'] ?? 'create';
$pdo = getPDO();

if ($action === 'create') {
    $pdo->prepare('INSERT INTO Products (Name, Price, Stock, CategoryID, ImageURL) VALUES (?, ?, ?, ?, ?)')
        ->execute([sanitize($data['name']), (float)$data['price'], (int)$data['stock'], (int)$data['category_id'], sanitize($data['image'])]);
    echo json_encode(['success' => true, 'message' => 'Product created']);
} elseif ($action === 'update') {
    $pdo->prepare('UPDATE Products SET Name=?, Price=?, Stock=?, CategoryID=?, ImageURL=? WHERE ProductID=?')
        ->execute([sanitize($data['name']), (float)$data['price'], (int)$data['stock'], (int)$data['category_id'], sanitize($data['image']), (int)$data['id']]);
    echo json_encode(['success' => true, 'message' => 'Product updated']);
} elseif ($action === 'delete') {
    $pdo->prepare('DELETE FROM Products WHERE ProductID=?')->execute([(int)$data['id']]);
    echo json_encode(['success' => true, 'message' => 'Product deleted']);
} else {
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
}


<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/config.php';

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function redirect(string $path): void
{
    header("Location: $path");
    exit;
}

function get_categories(): array
{
    $pdo = getPDO();
    return $pdo->query('SELECT * FROM Categories ORDER BY CategoryName')->fetchAll();
}

function get_products(?int $categoryId = null, string $search = ''): array
{
    $pdo = getPDO();
    $sql = 'SELECT Products.*, Categories.CategoryName FROM Products 
            LEFT JOIN Categories ON Products.CategoryID = Categories.CategoryID';
    $params = [];
    $clauses = [];
    if ($categoryId) {
        $clauses[] = 'Products.CategoryID = ?';
        $params[] = $categoryId;
    }
    if ($search) {
        $clauses[] = 'Products.Name LIKE ?';
        $params[] = '%' . $search . '%';
    }
    if ($clauses) {
        $sql .= ' WHERE ' . implode(' AND ', $clauses);
    }
    $sql .= ' ORDER BY Products.ProductID DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_product(int $id)
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM Products WHERE ProductID = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function add_to_cart_db(int $customerId, int $productId, int $qty): void
{
    $pdo = getPDO();
    $existing = $pdo->prepare('SELECT * FROM ShoppingCart WHERE CustomerID = ? AND ProductID = ?');
    $existing->execute([$customerId, $productId]);
    $row = $existing->fetch();
    if ($row) {
        $pdo->prepare('UPDATE ShoppingCart SET Quantity = Quantity + ? WHERE CartID = ?')
            ->execute([$qty, $row['CartID']]);
    } else {
        $pdo->prepare('INSERT INTO ShoppingCart (CustomerID, ProductID, Quantity) VALUES (?, ?, ?)')
            ->execute([$customerId, $productId, $qty]);
    }
}

function get_cart_items(int $customerId): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT ShoppingCart.*, Products.Name, Products.Price, Products.ImageURL 
        FROM ShoppingCart 
        JOIN Products ON ShoppingCart.ProductID = Products.ProductID
        WHERE ShoppingCart.CustomerID = ?');
    $stmt->execute([$customerId]);
    return $stmt->fetchAll();
}

function clear_cart(int $customerId): void
{
    $pdo = getPDO();
    $pdo->prepare('DELETE FROM ShoppingCart WHERE CustomerID = ?')->execute([$customerId]);
}

function create_order(int $customerId, string $shippingAddress, array $items): int
{
    $pdo = getPDO();
    $pdo->beginTransaction();
    $total = 0;
    foreach ($items as $item) {
        $total += $item['Price'] * $item['Quantity'];
    }
    try {
        $pdo->prepare('INSERT INTO Orders (CustomerID, OrderDate, Status, TotalPrice, ShippingAddress) 
            VALUES (?, NOW(), "Pending Payment", ?, ?)')
            ->execute([$customerId, $total, $shippingAddress]);
        $orderId = (int)$pdo->lastInsertId();
        $orderItemStmt = $pdo->prepare('INSERT INTO OrderItems (OrderID, ProductID, Quantity, Price) VALUES (?, ?, ?, ?)');
        $stockStmt = $pdo->prepare('UPDATE Products SET Stock = Stock - ? WHERE ProductID = ? AND Stock >= ?');
        foreach ($items as $item) {
            $orderItemStmt->execute([$orderId, $item['ProductID'], $item['Quantity'], $item['Price']]);
            $stockStmt->execute([$item['Quantity'], $item['ProductID'], $item['Quantity']]);
        }
        $pdo->commit();
        return $orderId;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function get_orders_by_customer(int $customerId): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM Orders WHERE CustomerID = ? ORDER BY OrderDate DESC');
    $stmt->execute([$customerId]);
    return $stmt->fetchAll();
}

function get_order_items(int $orderId): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT OrderItems.*, Products.Name, Products.ImageURL FROM OrderItems 
        JOIN Products ON OrderItems.ProductID = Products.ProductID WHERE OrderID = ?');
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}

function format_price(float $amount): string
{
    return number_format($amount, 0, '.', ',') . ' RWF';
}

function image_url(string $path): string
{
    if (preg_match('~^https?://~', $path)) {
        return $path;
    }
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Renders success/error alert divs. Pass empty string to skip.
 */
function render_alerts(string $success = '', string $error = ''): void
{
    if ($success) echo '<div class="alert success">' . $success . '</div>';
    if ($error)   echo '<div class="alert error">'   . $error   . '</div>';
}

/**
 * Saves a contact form message to the database.
 * Returns true on success, false if fields are missing.
 */
function save_contact_message(string $name, string $email, string $body): bool
{
    if (!$name || !$email || !$body) {
        return false;
    }
    $pdo = getPDO();
    $pdo->prepare('INSERT INTO ContactMessages (Name, Email, Message, CreatedAt) VALUES (?, ?, ?, NOW())')
        ->execute([$name, $email, $body]);
    return true;
}
?>


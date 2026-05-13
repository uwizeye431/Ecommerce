<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/config.php';

/**
 * Cleans user input to prevent XSS attacks.
 */
function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Checks if the current request is a POST request.
 */
function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Redirects the user to a new path and terminates the script.
 */
function redirect(string $path): void
{
    header("Location: $path");
    exit;
}

/**
 * Retrieves all product categories from the database.
 */
function get_categories(): array
{
    $pdo = getPDO();
    return $pdo->query('SELECT * FROM Categories ORDER BY CategoryName')->fetchAll();
}

/**
 * Fetches products, optionally filtered by category or search term.
 */
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

/**
 * Fetches a single product by its ID.
 */
function get_product(int $id)
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM Products WHERE ProductID = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Adds an item to the shopping cart or updates quantity if it exists.
 */
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

/**
 * Retrieves all items in a customer's cart with product details.
 */
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

/**
 * Removes all items from a customer's shopping cart.
 */
function clear_cart(int $customerId): void
{
    $pdo = getPDO();
    $pdo->prepare('DELETE FROM ShoppingCart WHERE CustomerID = ?')->execute([$customerId]);
}

/**
 * Handles the checkout process: creates order, records items, and updates stock.
 * Uses a transaction to ensure data integrity.
 */
function create_order(int $customerId, string $shippingAddress, array $items): int
{
    $pdo = getPDO();
    $pdo->beginTransaction();

    $total = 0;
    foreach ($items as $item) {
        $total += $item['Price'] * $item['Quantity'];
    }

    try {
        // Create the main order record
        $pdo->prepare('INSERT INTO Orders (CustomerID, OrderDate, Status, TotalPrice, ShippingAddress) 
            VALUES (?, NOW(), "Pending Payment", ?, ?)')
            ->execute([$customerId, $total, $shippingAddress]);
        
        $orderId = (int)$pdo->lastInsertId();

        $orderItemStmt = $pdo->prepare('INSERT INTO OrderItems (OrderID, ProductID, Quantity, Price) VALUES (?, ?, ?, ?)');
        $stockStmt = $pdo->prepare('UPDATE Products SET Stock = Stock - ? WHERE ProductID = ? AND Stock >= ?');

        // Link items to the order and reduce inventory
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

/**
 * Fetches all orders placed by a specific customer.
 */
function get_orders_by_customer(int $customerId): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM Orders WHERE CustomerID = ? ORDER BY OrderDate DESC');
    $stmt->execute([$customerId]);
    return $stmt->fetchAll();
}

/**
 * Formats a numeric price into a currency string (RWF).
 */
function format_price(float $amount): string
{
    return number_format($amount, 0, '.', ',') . ' RWF';
}

/**
 * Ensures a product image URL is absolute.
 */
function image_url(string $path): string
{
    if (preg_match('~^https?://~', $path)) {
        return $path;
    }
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Renders HTML alert messages for success or errors.
 */
function render_alerts(string $success = '', string $error = ''): void
{
    if ($success) {
        echo '<div class="alert success">' . $success . '</div>';
    }
    if ($error) {
        echo '<div class="alert error">' . $error . '</div>';
    }
}

/**
 * Saves a message from the Contact Us form to the database.
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


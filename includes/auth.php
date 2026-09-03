<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

function find_user_by_email(string $email)
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE Email = ? LIMIT 1');
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function log_activity(int $userId, string $action): void
{
    $pdo = getPDO();
    // Ensure user still exists to avoid foreign key errors (e.g. deleted accounts)
    $check = $pdo->prepare('SELECT 1 FROM Users WHERE UserID = ?');
    $check->execute([$userId]);
    if (!$check->fetchColumn()) {
        return;
    }
    $stmt = $pdo->prepare('INSERT INTO ActivityLogs (UserID, Action, CreatedAt) VALUES (?, ?, NOW())');
    $stmt->execute([$userId, $action]);
}

function login_user(string $email, string $password): bool
{
    if (!$email || !$password) {
        return false;
    }
    $user = find_user_by_email($email);
    if (!$user || !password_verify($password, $user['Password']) || ($user['Status'] ?? 'active') !== 'active') {
        return false;
    }
    $_SESSION['user'] = $user;
    // update last login and log activity
    $pdo = getPDO();
    $pdo->prepare('UPDATE Users SET LastLogin = NOW() WHERE UserID = ?')->execute([$user['UserID']]);
    log_activity((int)$user['UserID'], 'Logged in');
    return true;
}

function register_user(string $username, string $email, string $password, string $role = 'customer'): bool
{
    $pdo = getPDO();
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO Users (Username, Email, Password, Role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, $email, $hashed, $role]);
        $userId = (int)$pdo->lastInsertId();
        if ($role === 'customer') {
            $pdo->prepare('INSERT INTO Customers (CustomerID, ShippingAddress, PhoneNumber) VALUES (?, "", "")')
                ->execute([$userId]);
        } else {
            $pdo->prepare('INSERT INTO Admins (AdminID, AdminLevel) VALUES (?, "standard")')->execute([$userId]);
        }
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function require_login(): void
{
    if (!current_user_id()) {
        header('Location: /login.php');
        exit;
    }
}

function require_role(string $role): void
{
    require_login();
    if (current_user_role() !== $role) {
        http_response_code(403);
        echo 'Access denied';
        exit;
    }
}

function logout_user(): void
{
    if (current_user_id()) {
        log_activity(current_user_id(), 'Logged out');
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
?>


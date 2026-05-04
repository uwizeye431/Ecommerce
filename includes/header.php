<?php
require_once __DIR__ . '/auth.php';

// Automatically handles redirects and role-based protection
check_page_access();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
</head>
<body data-base-url="<?php echo APP_URL; ?>">
<header class="topbar">
    <div class="brand"><a href="<?php echo APP_URL; ?>/index.php">E-Commerce</a></div>
    <nav class="nav">
        <a href="<?php echo APP_URL; ?>/index.php">Home</a>
        <a href="<?php echo APP_URL; ?>/about.php">About</a>
        <a href="<?php echo APP_URL; ?>/products.php">Products</a>
        <a href="<?php echo APP_URL; ?>/contact.php">Contact</a>
        <?php if (current_user_role() === 'customer'): ?>
            <a href="<?php echo APP_URL; ?>/customer/cart.php">Cart</a>
            <a href="<?php echo APP_URL; ?>/customer/order_tracking.php">Orders</a>
            <a class="pill" href="<?php echo APP_URL; ?>/customer/dashboard.php">Customer</a>
        <?php endif; ?>
        <?php if (current_user_role() === 'admin'): ?>
            <a class="pill" href="<?php echo APP_URL; ?>/admin/dashboard.php">Admin</a>
        <?php endif; ?>
        <?php if (current_user_id()): ?>
            <a href="<?php echo APP_URL; ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?php echo APP_URL; ?>/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main class="page">


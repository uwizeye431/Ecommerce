<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/functions.php';

$categories = get_categories();
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = sanitize($_GET['q'] ?? '');
$products = get_products($categoryId, $search);
$detailsBase = 'customer/product_details.php';
?>
<?php require __DIR__ . '/includes/partials/product_grid.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>


<?php
// Requires: $categories, $products, $categoryId, $search, $detailsBase (path to product_details.php)
?>
<div class="card">
    <form method="get" class="row">
        <div>
            <label>Search</label>
            <input type="text" name="q" value="<?php echo $search; ?>" placeholder="Search products">
        </div>
        <div>
            <label>Category</label>
            <select name="category">
                <option value="">All</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['CategoryID']; ?>" <?php if ($categoryId === (int)$c['CategoryID']) echo 'selected'; ?>>
                        <?php echo sanitize($c['CategoryName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="align-self:end;">
            <button class="btn primary" type="submit">Filter</button>
        </div>
    </form>
</div>

<div class="grid products">
    <?php foreach ($products as $p): ?>
        <div class="card product">
            <img src="<?php echo image_url($p['ImageURL']); ?>" alt="<?php echo sanitize($p['Name']); ?>">
            <div class="flex space-between">
                <div>
                    <h3><?php echo sanitize($p['Name']); ?></h3>
                    <span class="badge"><?php echo sanitize($p['CategoryName'] ?? ''); ?></span>
                </div>
                <strong><?php echo format_price((float)$p['Price']); ?></strong>
            </div>
            <div class="flex space-between">
                <a class="btn secondary" href="<?php echo $detailsBase; ?>?id=<?php echo $p['ProductID']; ?>">Details</a>
                <button class="btn primary" data-add-to-cart data-id="<?php echo $p['ProductID']; ?>">Add</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

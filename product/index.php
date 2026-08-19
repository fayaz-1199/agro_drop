<?php
$pageTitle = 'Products';
require_once '../config/database.php';
function goProduct($message, $error = false)
{
    header('Location: index.php?'.($error ? 'error' : 'success').'='.urlencode($message));
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $farmer = trim($_POST['farmer_name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $unit = trim($_POST['unit'] ?? 'kg');
    $description = trim($_POST['description'] ?? '');
    $status = (int)($_POST['status'] ?? 1);
    if (!$name || !$category || !$farmer || $price <= 0 || $stock < 0) {
        goProduct('Please complete all required fields with valid values.', true);
    }
    if ($id) {
        $stmt = mysqli_prepare($conn, 'UPDATE products SET name=?, category=?, farmer_name=?, price=?, stock=?, unit=?, description=?, status=? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'sssdissii', $name, $category, $farmer, $price, $stock, $unit, $description, $status, $id);
    } else {
        $stmt = mysqli_prepare($conn, 'INSERT INTO products (name,category,farmer_name,price,stock,unit,description,status) VALUES (?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'sssdissi', $name, $category, $farmer, $price, $stock, $unit, $description, $status);
    }
    if (!$stmt || !mysqli_stmt_execute($stmt)) {
        goProduct('Could not save product.', true);
    } goProduct($id ? 'Product updated successfully.' : 'Product added successfully.');
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM products WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) {
        goProduct('This product has orders and cannot be deleted.', true);
    } goProduct('Product deleted.');
}
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $r = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
    $edit = mysqli_fetch_assoc($r);
}
$search = trim($_GET['search'] ?? '');
$safe = mysqli_real_escape_string($conn, $search);
$products = mysqli_query($conn, "SELECT * FROM products WHERE name LIKE '%$safe%' OR category LIKE '%$safe%' OR farmer_name LIKE '%$safe%' ORDER BY id DESC");
require_once '../includes/header.php';
?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Farmer module</span>
            <h1>Products</h1>
            <p>Manage the agricultural products available for customers.</p>
        </div><a class="primary-btn" href="index.php?add=1">+ Add Product</a>
    </div>
    <?php if (isset($_GET['success']) || isset($_GET['error'])): ?><div class="alert <?=isset($_GET['error']) ? 'alert-error' : 'alert-success'?>"><?= htmlspecialchars($_GET['error'] ?? $_GET['success']) ?></div><?php endif; ?>
    <?php if (isset($_GET['add']) || $edit): $p = $edit ?? ['id' => '','name' => '','category' => '','farmer_name' => '','price' => '','stock' => '','unit' => 'kg','description' => '','status' => 1]; ?><div class="form-card">
        <h2><?= $edit ? 'Edit Product' : 'Add New Product' ?></h2>
        <form method="post" class="form-grid"><input type="hidden" name="id" value="<?= $p['id'] ?>">
            <div class="form-group"><label>Product name *</label><input name="name" required value="<?=htmlspecialchars($p['name'])?>"></div>
            <div class="form-group"><label>Category *</label><input name="category" required placeholder="Vegetables, Fruits..." value="<?=htmlspecialchars($p['category'])?>"></div>
            <div class="form-group"><label>Farmer name *</label><input name="farmer_name" required value="<?=htmlspecialchars($p['farmer_name'])?>"></div>
            <div class="form-group"><label>Price (৳) *</label><input type="number" min="1" step="0.01" name="price" required value="<?=htmlspecialchars($p['price'])?>"></div>
            <div class="form-group"><label>Available stock *</label><input type="number" min="0" name="stock" required value="<?=htmlspecialchars($p['stock'])?>"></div>
            <div class="form-group"><label>Unit</label><select name="unit"><?php foreach (['kg','piece','dozen','jar','bundle'] as $u): ?><option <?=$p['unit'] === $u ? 'selected' : ''?>><?=$u?></option><?php endforeach ?></select></div>
            <div class="form-group"><label>Status</label><select name="status">
                    <option value="1" <?=$p['status'] ? 'selected' : ''?>>Active</option>
                    <option value="0" <?=!$p['status'] ? 'selected' : ''?>>Inactive</option>
                </select></div>
            <div class="form-group full"><label>Description</label><textarea name="description" rows="3"><?=htmlspecialchars($p['description'])?></textarea></div>
            <div class="form-actions full"><a class="secondary-btn" href="index.php">Cancel</a><button class="primary-btn" type="submit">Save Product</button></div>
        </form>
    </div><?php endif; ?>
    <div class="table-card">
        <div class="table-header">
            <div>
                <h2>Product catalogue</h2>
                <p><?= mysqli_num_rows($products) ?> item(s) found</p>
            </div>
            <form class="search-form"><input name="search" value="<?=htmlspecialchars($search)?>" placeholder="Search products"><button class="secondary-btn small-btn">Search</button></form>
        </div>
        <div class="table-responsive">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Farmer</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody><?php while ($p = mysqli_fetch_assoc($products)): ?><tr>
                        <td><strong><?=htmlspecialchars($p['name'])?></strong><br><small><?=htmlspecialchars($p['category'])?></small></td>
                        <td><?=htmlspecialchars($p['farmer_name'])?></td>
                        <td>৳<?=number_format($p['price'], 2)?> / <?=htmlspecialchars($p['unit'])?></td>
                        <td><?=$p['stock']?> <?=htmlspecialchars($p['unit'])?></td>
                        <td><span class="status <?=$p['status'] ? 'status-active' : 'status-inactive'?>"><?=$p['status'] ? 'Active' : 'Inactive'?></span></td>
                        <td><a class="table-link" href="?edit=<?=$p['id']?>">Edit</a> <a class="table-link danger" onclick="return confirm('Delete this product?')" href="?delete=<?=$p['id']?>">Delete</a></td>
                    </tr><?php endwhile; ?></tbody>
            </table>
        </div>
    </div>
</section><?php require_once '../includes/footer.php'; ?>

<?php
$pageTitle = 'Manage products';
require_once '../includes/app.php';
require_farmer();
$farmer = (int)current_user()['id'];
$categories = ['Vegetables','Fruits','Grains','Dairy & Eggs','Organic','Fish','Meat'];
function farmerGo($message, $error = false)
{
    $_SESSION['flash'] = [$error ? 'error' : 'success',$message];
    redirect('farmer/products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $cat = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $unit = trim($_POST['unit'] ?? 'kg');
    $emoji = trim($_POST['emoji'] ?? '🥬');
    $desc = trim($_POST['description'] ?? '');
    if (!$name || !in_array($cat, $categories, true) || $price <= 0 || $stock < 0) {
        farmerGo('Complete all required product fields and select a category.', true);
    }
    if ($id) {
        $stmt = mysqli_prepare($conn, 'UPDATE products SET name=?,category=?,price=?,stock=?,unit=?,emoji=?,description=? WHERE id=? AND farmer_id=?');
        mysqli_stmt_bind_param($stmt, 'ssdisssii', $name, $cat, $price, $stock, $unit, $emoji, $desc, $id, $farmer);
    } else {
        $stmt = mysqli_prepare($conn, 'INSERT INTO products(farmer_id,name,category,price,stock,unit,emoji,description) VALUES(?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'issdisss', $farmer, $name, $cat, $price, $stock, $unit, $emoji, $desc);
    }
    if (!mysqli_stmt_execute($stmt)) {
        farmerGo('Could not save product.', true);
    } farmerGo($id ? 'Product updated.' : 'Product published.');
}
if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, 'DELETE FROM products WHERE id=? AND farmer_id=?');
    mysqli_stmt_bind_param($stmt, 'ii', $pid, $farmer);
    mysqli_stmt_execute($stmt);
    farmerGo('Product deleted.');
}
$edit = null;
if (isset($_GET['edit'])) {
    $pid = (int)$_GET['edit'];
    $r = mysqli_query($conn, "SELECT * FROM products WHERE id=$pid AND farmer_id=$farmer");
    $edit = mysqli_fetch_assoc($r);
}
$products = mysqli_query($conn, "SELECT * FROM products WHERE farmer_id=$farmer ORDER BY id DESC");
require_once '../includes/header.php';
?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Farmer center</span>
            <h1>My products</h1>
            <p>Add products and keep your inventory accurate.</p>
        </div><a class="primary-btn" href="?add=1">+ Add product</a>
    </div><?=flash()?>
    <?php if (isset($_GET['add']) || $edit):$p = $edit ?? ['id' => '','name' => '','category' => '','price' => '','stock' => '','unit' => 'kg','emoji' => '🥬','description' => ''];?><div class="form-card">
        <h2><?=$edit ? 'Edit product' : 'Add a new product'?></h2>
        <form method="post" class="form-grid"><input type="hidden" name="id" value="<?=$p['id']?>">
            <div class="form-group"><label>Product name *</label><input name="name" required value="<?=e($p['name'])?>"></div>
            <div class="form-group"><label>Category *</label><select name="category" required>
                    <option value="">Select a category</option><?php foreach ($categories as $category):?><option value="<?=e($category)?>" <?=$p['category'] === $category ? 'selected' : ''?>><?=e($category)?></option><?php endforeach;?>
                </select></div>
            <div class="form-group"><label>Price (৳) *</label><input type="number" step="0.01" min="1" name="price" required value="<?=e($p['price'])?>"></div>
            <div class="form-group"><label>Stock *</label><input type="number" min="0" name="stock" required value="<?=e($p['stock'])?>"></div>
            <div class="form-group"><label>Unit</label><select name="unit"><?php foreach (['kg','piece','dozen','jar','bundle'] as $u):?><option <?=$p['unit'] === $u ? 'selected' : ''?>><?=$u?></option><?php endforeach;?></select></div>
            <div class="form-group"><label>Product emoji</label><input name="emoji" maxlength="16" value="<?=e($p['emoji'])?>"></div>
            <div class="form-group full"><label>Description</label><textarea name="description" rows="3"><?=e($p['description'])?></textarea></div>
            <div class="full form-actions"><a class="secondary-btn" href="products.php">Cancel</a><button class="primary-btn">Save product</button></div>
        </form>
    </div><?php endif;?>
    <div class="table-card">
        <div class="table-responsive">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Published</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody><?php while ($p = mysqli_fetch_assoc($products)):?><tr>
                        <td><?=$p['emoji']?> <strong><?=e($p['name'])?></strong><br><small><?=e($p['category'])?></small></td>
                        <td>৳<?=number_format($p['price'], 0)?>/<?=e($p['unit'])?></td>
                        <td><?=$p['stock']?> <?=e($p['unit'])?></td>
                        <td><?=date('d M Y', strtotime($p['created_at']))?></td>
                        <td><a class="table-link" href="?edit=<?=$p['id']?>">Edit</a><a class="danger" onclick="return confirm('Delete this product?')" href="?delete=<?=$p['id']?>">Delete</a></td>
                    </tr><?php endwhile;?></tbody>
            </table>
        </div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>

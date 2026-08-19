<?php $pageTitle = 'Shop fresh products';
require_once 'includes/app.php';
$search = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$where = 'p.is_active=1 AND p.stock>0';
if ($search !== '') {
    $v = mysqli_real_escape_string($conn, $search);
    $where .= " AND (p.name LIKE '%$v%' OR p.category LIKE '%$v%')";
}if ($category !== '') {
    $v = mysqli_real_escape_string($conn, $category);
    $where .= " AND p.category='$v'";
}$products = mysqli_query($conn, "SELECT p.*,u.name farmer FROM products p JOIN users u ON u.id=p.farmer_id WHERE $where ORDER BY p.id DESC");
$categories = mysqli_query($conn, 'SELECT DISTINCT category FROM products WHERE is_active=1 ORDER BY category');
require_once 'includes/header.php'; ?>
<section class="shop-banner">
    <div class="container"><span class="eyebrow">Marketplace</span>
        <h1>Fresh products, fair prices.</h1>
        <p>Buy directly from farmers near you.</p>
    </div>
</section>
<section class="container section-space"><?=flash()?><form class="shop-tools"><input name="q" value="<?=e($search)?>" placeholder="Search vegetables, rice, honey..."><button class="primary-btn">Search</button></form>
    <div class="shop-layout">
        <aside class="filters">
            <h3>Categories</h3><a class="<?=!$category ? 'selected' : ''?>" href="shop.php">All products</a><?php while ($c = mysqli_fetch_assoc($categories)):?><a class="<?=$category === $c['category'] ? 'selected' : ''?>" href="shop.php?category=<?=urlencode($c['category'])?>"><?=e($c['category'])?></a><?php endwhile;?>
        </aside>
        <div>
            <div class="result-bar"><strong><?=mysqli_num_rows($products)?> products found</strong></div>
            <div class="product-grid"><?php if (mysqli_num_rows($products)):while ($p = mysqli_fetch_assoc($products)):?><article class="product-card"><a href="product.php?id=<?=$p['id']?>" class="product-image"><?=$p['emoji']?></a>
                    <div class="product-body"><small><?=e($p['category'])?></small>
                        <h3><a href="product.php?id=<?=$p['id']?>"><?=e($p['name'])?></a></h3>
                        <p class="farmer-name">by <?=e($p['farmer'])?></p>
                        <div class="product-bottom"><strong>৳<?=number_format($p['price'], 0)?><em>/<?=e($p['unit'])?></em></strong><a class="add-btn" href="cart.php?add=<?=$p['id']?>">Add +</a></div>
                    </div>
                </article><?php endwhile;
else:?><div class="empty-card">No product matches your search.</div><?php endif;?></div>
        </div>
    </div>
</section><?php require_once 'includes/footer.php'; ?>

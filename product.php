<?php
$pageTitle = 'Product details';

require_once 'includes/app.php';

$productId = (int) ($_GET['id'] ?? 0);

$productQuery = mysqli_prepare(
    $conn,
    'SELECT p.*, u.name farmer, u.address farm_address
     FROM products p
     JOIN users u ON u.id = p.farmer_id
     WHERE p.id = ? AND p.is_active = 1'
);
mysqli_stmt_bind_param($productQuery, 'i', $productId);
mysqli_stmt_execute($productQuery);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($productQuery));

if (!$product) {
    $_SESSION['flash'] = ['error', 'Product not found.'];
    redirect('shop.php');
}

$reviewQuery = mysqli_prepare(
    $conn,
    'SELECT r.*, u.name customer
     FROM reviews r
     JOIN users u ON u.id = r.customer_id
     WHERE r.product_id = ? AND r.status = 1
     ORDER BY r.updated_at DESC'
);
mysqli_stmt_bind_param($reviewQuery, 'i', $productId);
mysqli_stmt_execute($reviewQuery);
$reviews = mysqli_stmt_get_result($reviewQuery);

require_once 'includes/header.php';
?>

<section class="container detail-page">
    <div class="product-showcase">
        <?= e($product['emoji']) ?>
    </div>

    <div class="product-info">
        <a class="back-link" href="shop.php">← Back to products</a>

        <span class="category-pill">
            <?= e($product['category']) ?>
        </span>

        <h1><?= e($product['name']) ?></h1>

        <p class="farmer-name">
            Grown and sold by <strong><?= e($product['farmer']) ?></strong>
        </p>

        <p class="description">
            <?= e($product['description']) ?>
        </p>

        <div class="price-big">
            ৳<?= number_format($product['price'], 0) ?>
            <span>/ <?= e($product['unit']) ?></span>
        </div>

        <p class="stock-note">
            ✓ <?= $product['stock'] ?> <?= e($product['unit']) ?> available now
        </p>

        <form action="cart.php" method="get" class="buy-form">
            <input type="hidden" name="add" value="<?= $product['id'] ?>">
            <input type="number" name="qty" min="1" max="<?= $product['stock'] ?>" value="1">
            <button class="primary-btn large-btn">Add to cart</button>
        </form>

        <div class="farm-note">
            🌾
            <div>
                <strong>From <?= e($product['farm_address'] ?: 'a local Bangladesh farm') ?></strong>
                <br>
                <small>Freshly packed after you place an order.</small>
            </div>
        </div>
    </div>
</section>

<section class="container reviews-section">
    <div class="section-title">
        <div>
            <span class="eyebrow">Customer feedback</span>
            <h2>Reviews</h2>
        </div>

        <?php if (current_user() && current_user()['role'] === 'customer'): ?>
        <a class="text-btn" href="review/index.php">Write a review →</a>
        <?php endif; ?>
    </div>

    <div class="review-grid">
        <?php if (mysqli_num_rows($reviews)): ?>
        <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
        <article class="review-card">
            <div class="review-head">
                <strong><?= e($review['customer']) ?></strong>
                <span class="rating">
                    <?= str_repeat('★', (int) $review['rating']) ?>
                    <i><?= str_repeat('★', 5 - (int) $review['rating']) ?></i>
                </span>
            </div>

            <p>“<?= e($review['comment']) ?>”</p>
            <small><?= date('d M Y', strtotime($review['updated_at'])) ?></small>
        </article>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="empty-card">
            No reviews yet. Buy this product and share your experience after delivery.
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

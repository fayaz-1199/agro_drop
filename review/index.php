<?php
$pageTitle = 'My reviews';
require_once '../includes/app.php';
require_customer();

function reviewGo($message, $error = false)
{
    $_SESSION['flash'] = [$error ? 'error' : 'success', $message];
    redirect('review/index.php');
}

$customerId = (int) current_user()['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $rating = (int) ($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    if (!$productId || $rating < 1 || $rating > 5 || $comment === '') {
        reviewGo('Please choose a product, rating, and write a comment.', true);
    }

    $exists = mysqli_prepare($conn, 'SELECT id FROM products WHERE id=? AND is_active=1 LIMIT 1');
    mysqli_stmt_bind_param($exists, 'i', $productId);
    mysqli_stmt_execute($exists);
    if (!mysqli_fetch_assoc(mysqli_stmt_get_result($exists))) {
        reviewGo('The selected product is not available.', true);
    }

    $save = mysqli_prepare($conn, 'INSERT INTO reviews(customer_id,product_id,rating,comment) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE rating=VALUES(rating), comment=VALUES(comment), status=1');
    mysqli_stmt_bind_param($save, 'iiis', $customerId, $productId, $rating, $comment);
    if (!mysqli_stmt_execute($save)) {
        reviewGo('Could not save your review. Please try again.', true);
    }
    reviewGo('Your review has been saved. You can update it any time.');
}

$products = mysqli_query($conn, 'SELECT id,name,emoji FROM products WHERE is_active=1 ORDER BY name');
$reviews = mysqli_prepare($conn, 'SELECT r.*,p.name product,p.emoji FROM reviews r JOIN products p ON p.id=r.product_id WHERE r.customer_id=? ORDER BY r.updated_at DESC');
mysqli_stmt_bind_param($reviews, 'i', $customerId);
mysqli_stmt_execute($reviews);
$reviewList = mysqli_stmt_get_result($reviews);
require_once '../includes/header.php';
?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Customer account</span>
            <h1>My reviews</h1>
            <p>Share feedback about any product in the shop.</p>
        </div><a class="primary-btn" href="?add=1">Write a review</a>
    </div>
    <?= flash() ?>
    <?php if (isset($_GET['add'])): ?>
    <div class="form-card">
        <h2>Write a product review</h2>
        <form method="post" class="form-grid">
            <div class="form-group"><label>Product *</label><select name="product_id" required>
                    <option value="">Select a product</option><?php while ($product = mysqli_fetch_assoc($products)): ?><option value="<?= $product['id'] ?>"><?= e($product['emoji']) ?> <?= e($product['name']) ?></option><?php endwhile; ?>
                </select></div>
            <div class="form-group"><label>Rating *</label><select name="rating" required>
                    <option value="5">★★★★★ — Excellent</option>
                    <option value="4">★★★★ — Good</option>
                    <option value="3">★★★ — Average</option>
                    <option value="2">★★ — Poor</option>
                    <option value="1">★ — Bad</option>
                </select></div>
            <div class="form-group full"><label>Your feedback *</label><textarea name="comment" rows="4" maxlength="1000" required placeholder="Tell other customers about the product..."></textarea></div>
            <div class="form-actions full"><a class="secondary-btn" href="index.php">Cancel</a><button class="primary-btn">Save review</button></div>
        </form>
    </div>
    <?php endif; ?>
    <div class="review-grid"><?php if (mysqli_num_rows($reviewList)): while ($review = mysqli_fetch_assoc($reviewList)): ?><article class="review-card">
            <div class="review-head">
                <div><strong><?= e($review['emoji']) ?> <?= e($review['product']) ?></strong><small>Updated <?= date('d M Y', strtotime($review['updated_at'])) ?></small></div><span class="rating"><?= str_repeat('★', (int) $review['rating']) ?><i><?= str_repeat('★', 5 - (int) $review['rating']) ?></i></span>
            </div>
            <p>“<?= e($review['comment']) ?>”</p><a class="table-link" href="?add=1">Edit review</a>
        </article><?php endwhile;
    else: ?><div class="empty-card">You have not reviewed any products yet.</div><?php endif; ?></div>
</section>
<?php require_once '../includes/footer.php'; ?>

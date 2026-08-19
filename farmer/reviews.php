<?php
$pageTitle = 'Customer reviews';

require_once '../includes/app.php';
require_farmer();

$farmerId = (int) current_user()['id'];

$reviewQuery = mysqli_prepare(
    $conn,
    'SELECT r.id, r.rating, r.comment, r.created_at, r.updated_at,
            p.name product_name, p.emoji product_emoji,
            u.name customer_name, u.email customer_email
     FROM reviews r
     JOIN products p ON p.id = r.product_id
     JOIN users u ON u.id = r.customer_id
     WHERE p.farmer_id = ?
     ORDER BY r.updated_at DESC'
);
mysqli_stmt_bind_param($reviewQuery, 'i', $farmerId);
mysqli_stmt_execute($reviewQuery);
$reviews = mysqli_stmt_get_result($reviewQuery);

require_once '../includes/header.php';
?>

<section class="container section-space">
    <div class="page-header">
        <div>
            <span class="eyebrow">Farmer center</span>
            <h1>Latest customer reviews</h1>
            <p>Feedback customers have shared about your products.</p>
        </div>

        <a class="secondary-btn" href="dashboard.php">← Dashboard</a>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div>
                <h2>All reviews</h2>
                <p><?= mysqli_num_rows($reviews) ?> review(s) received</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($reviews)): ?>
                    <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                    <tr>
                        <td>
                            <strong><?= e($review['customer_name']) ?></strong>
                            <br>
                            <small><?= e($review['customer_email']) ?></small>
                        </td>
                        <td>
                            <?= e($review['product_emoji']) ?>
                            <?= e($review['product_name']) ?>
                        </td>
                        <td>
                            <span class="rating">
                                <?= str_repeat('★', (int) $review['rating']) ?>
                                <i><?= str_repeat('★', 5 - (int) $review['rating']) ?></i>
                            </span>
                        </td>
                        <td class="review-comment"><?= e($review['comment']) ?></td>
                        <td><?= date('d M Y', strtotime($review['updated_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty">No reviews have been received for your products yet.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>

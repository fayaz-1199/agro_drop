<?php
$pageTitle = 'Customer Details';
require_once '../config/database.php';
$id = (int) ($_GET['id'] ?? 0);
$result = mysqli_query($conn, "SELECT * FROM customers WHERE id=$id");
$customer = $result ? mysqli_fetch_assoc($result) : null;
if (!$customer) {
    header('Location: index.php?error='.urlencode('Customer not found'));
    exit;
}
require_once '../includes/header.php';
?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Customer module</span>
            <h1><?= htmlspecialchars($customer['name']) ?></h1>
            <p>Customer profile and contact information.</p>
        </div><a class="secondary-btn" href="index.php">← Back to Customers</a>
    </div>
    <div class="form-card detail-card">
        <div><small>Phone</small><strong>📞 <?= htmlspecialchars($customer['phone']) ?></strong></div>
        <div><small>Email</small><strong><?= htmlspecialchars($customer['email'] ?: '—') ?></strong></div>
        <div><small>Address</small><strong><?= htmlspecialchars($customer['address'] ?: '—') ?></strong></div>
        <div><small>Account status</small><span class="status <?= $customer['status'] ? 'status-active' : 'status-inactive' ?>"><?= $customer['status'] ? 'Active' : 'Inactive' ?></span></div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>
